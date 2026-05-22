<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['idUtente'])) {
    http_response_code(401);
    exit();
}

require_once '../include/dbHandler.php';
$conn = DBHandler::getPDO();

$myId       = $_SESSION['idUtente'];
$receiverId = intval($_POST['idDestinatario'] ?? 0);
$testo      = strip_tags(trim($_POST['testo'] ?? ''));
$ip         = $_SERVER['REMOTE_ADDR'];
$isSwap     = intval($_POST['is_swap'] ?? 0); // 1 se è messaggio di richiesta swap

$limiteMsg     = 25;
$intervalloSec = 60;

if (empty($testo) || !$receiverId || $receiverId === $myId) {
    http_response_code(400);
    exit();
}

if (mb_strlen($testo) > 2000) {
    http_response_code(400);
    exit();
}

try {
    $conn->beginTransaction();

    // Rate limit dentro la transazione
    $check = $conn->prepare(
        "SELECT COUNT(*) FROM messaggi
         WHERE idMittente = :myId
         AND data_invio > DATE_SUB(NOW(), INTERVAL :sec SECOND)"
    );
    $check->bindValue(':myId', $myId, PDO::PARAM_INT);
    $check->bindValue(':sec',  $intervalloSec, PDO::PARAM_INT);
    $check->execute();

    if ($check->fetchColumn() >= $limiteMsg) {
        $conn->rollBack();
        http_response_code(429);
        exit();
    }

    // Inserisce messaggio
    $stmt = $conn->prepare(
        "INSERT INTO messaggi (idMittente, idDestinatario, testo, ip_mittente)
         VALUES (:myId, :receiverId, :testo, :ip)"
    );
    $stmt->bindValue(':myId',       $myId,       PDO::PARAM_INT);
    $stmt->bindValue(':receiverId', $receiverId, PDO::PARAM_INT);
    $stmt->bindValue(':testo',      $testo,      PDO::PARAM_STR);
    $stmt->bindValue(':ip',         $ip,         PDO::PARAM_STR);
    $stmt->execute();

    // +5 punti se è una richiesta di swap, ma solo una volta per coppia utente
    if ($isSwap === 1) {
        $giaProposto = $conn->prepare(
            "SELECT COUNT(*) FROM messaggi
             WHERE idMittente = :myId AND idDestinatario = :rid
             AND testo LIKE '%SkillSwap%'"
        );
        $giaProposto->execute([':myId' => $myId, ':rid' => $receiverId]);
        // Solo se questo è il primo messaggio swap verso questo utente
        // (il messaggio appena inserito conta, quindi controlliamo > 1)
        if ($giaProposto->fetchColumn() <= 1) {
            $conn->prepare("UPDATE utenti SET punti = punti + 5 WHERE idUtente = :id")
                 ->execute([':id' => $myId]);
        }
    }

    $conn->commit();
    http_response_code(200);

} catch (PDOException $e) {
    $conn->rollBack();
    http_response_code(500);
}
?>

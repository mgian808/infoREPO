<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['idUtente'])) {
    header('Location: ../include/loginForm.php');
    exit();
}

require_once '../include/dbHandler.php';
$conn = DBHandler::getPDO();

$myId       = $_SESSION['idUtente'];
$receiverId = intval($_GET['con']    ?? 0);
$limit      = max(1, min(200, intval($_GET['limit']  ?? 20)));
$offset     = max(0, intval($_GET['offset'] ?? 0));

if (!$receiverId) exit();

// Totale messaggi — usato dal JS per mostrare/nascondere il bottone "carica altri"
$countStmt = $conn->prepare(
    "SELECT COUNT(*) FROM messaggi
     WHERE (idMittente = :myId AND idDestinatario = :rid)
        OR (idMittente = :rid2 AND idDestinatario = :myId2)"
);
$countStmt->execute([':myId' => $myId, ':rid' => $receiverId, ':rid2' => $receiverId, ':myId2' => $myId]);
$totale = $countStmt->fetchColumn();
header("X-Total-Messages: $totale");

// Ultimi N messaggi in ordine cronologico
$sql = "SELECT * FROM (
            SELECT * FROM messaggi
            WHERE (idMittente = :myId AND idDestinatario = :rid)
               OR (idMittente = :rid2 AND idDestinatario = :myId2)
            ORDER BY data_invio DESC
            LIMIT :lim OFFSET :off
        ) AS sub
        ORDER BY data_invio ASC";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':myId',  $myId,       PDO::PARAM_INT);
$stmt->bindValue(':rid',   $receiverId, PDO::PARAM_INT);
$stmt->bindValue(':rid2',  $receiverId, PDO::PARAM_INT);
$stmt->bindValue(':myId2', $myId,       PDO::PARAM_INT);
$stmt->bindValue(':lim',   $limit,      PDO::PARAM_INT);
$stmt->bindValue(':off',   $offset,     PDO::PARAM_INT);
$stmt->execute();
$messaggi = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ultimaData = null;

foreach ($messaggi as $mex) {
    $dataCorrente = date('Y-m-d', strtotime($mex['data_invio']));
    $ora          = date('H:i',   strtotime($mex['data_invio']));

    if ($dataCorrente !== $ultimaData) {
        $label = formattaData($dataCorrente);
        echo "<div style='text-align:center;margin:12px 0;'>
                <span style='background:#ddd;padding:3px 12px;border-radius:14px;font-size:12px;font-weight:700;color:#555;'>$label</span>
              </div>";
        $ultimaData = $dataCorrente;
    }

    $classe = ($mex['idMittente'] == $myId) ? 'inviato' : 'ricevuto';
    echo "<div class='messaggio $classe'>";
    echo "<p>" . htmlspecialchars($mex['testo']) . "</p>";
    echo "<small>$ora</small>";
    echo "</div>";
}

function formattaData($data) {
    $oggi = date('Y-m-d');
    $ieri = date('Y-m-d', strtotime('-1 day'));
    if ($data === $oggi) return 'Oggi';
    if ($data === $ieri) return 'Ieri';
    return date('d/m/Y', strtotime($data));
}
?>

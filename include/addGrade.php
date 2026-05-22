<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['idUtente'])) {
    header('Location: ../include/loginForm.php');
    exit();
}

require_once 'dbHandler.php';
$conn = DBHandler::getPDO();

$materia   = htmlspecialchars(trim($_POST['materia']   ?? ''));
$argomento = htmlspecialchars(trim($_POST['argomento'] ?? ''));
$voto      = floatval($_POST['voto']  ?? 0);
$data      = $_POST['data']           ?? date('Y-m-d');
$tipo      = $_POST['tipo']           ?? 'NEUTRO';

if (empty($materia) || empty($argomento)) {
    die("Errore: materia e argomento obbligatori.");
}
if ($voto < 1 || $voto > 10) {
    die("Errore: voto non valido.");
}
if (!in_array($tipo, ['OFFRO', 'CERCO', 'NEUTRO'])) {
    $tipo = 'NEUTRO';
}

$idUtente = $_SESSION['idUtente'];

try {
    $conn->beginTransaction();

    // Inserisce il voto
    $stmt = $conn->prepare(
        "INSERT INTO voti (idUtente, materia, argomento, voto, tipo, data_inserimento)
         VALUES (:idUtente, :materia, :argomento, :voto, :tipo, :data)"
    );
    $stmt->execute([
        ':idUtente'  => $idUtente,
        ':materia'   => $materia,
        ':argomento' => $argomento,
        ':voto'      => $voto,
        ':tipo'      => $tipo,
        ':data'      => $data,
    ]);

    // +2 punti se OFFRO o CERCO, solo se l'utente non ha già 50 punti da voti
    // Controlliamo quanti voti OFFRO/CERCO ha già (per il cap)
    if ($tipo === 'OFFRO' || $tipo === 'CERCO') {
        $checkPunti = $conn->prepare("SELECT punti FROM utenti WHERE idUtente = :id");
        $checkPunti->execute([':id' => $idUtente]);
        $puntiAttuali = $checkPunti->fetchColumn();

        if ($puntiAttuali < 60) { // 10 registrazione + 50 da voti
            $conn->prepare("UPDATE utenti SET punti = punti + 2 WHERE idUtente = :id")
                 ->execute([':id' => $idUtente]);
        }
    }

    $conn->commit();
    header('Location: ../userpages/profile.php?msg=voto_aggiunto');
    exit();
} catch (PDOException $e) {
    $conn->rollBack();
    header('Location: ../userpages/profile.php?msg=errore');
    exit();
}
?>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
date_default_timezone_set('Europe/Rome');

if (!isset($_SESSION['idUtente'])) {
    header('Location: ../include/loginForm.php');
    exit();
}

require_once 'dbHandler.php';
$conn = DBHandler::getPDO();

$id      = $_SESSION['idUtente'];
$livello = max(1, min(10, intval($_POST['livello'] ?? 5)));
$oggi    = date('Y-m-d');

try {
    $conn->beginTransaction();

    // Controlla se ha già votato oggi — FOR UPDATE blocca la riga per evitare doppioni
    $check = $conn->prepare(
        "SELECT idLog FROM stress_log 
         WHERE idUtente = :id AND DATE(data_voto) = :oggi FOR UPDATE"
    );
    $check->execute([':id' => $id, ':oggi' => $oggi]);

    if ($check->fetch()) {
        $conn->rollBack();
        header('Location: ../index.php?status=gia_votato');
        exit();
    }

    $stmt = $conn->prepare(
        "INSERT INTO stress_log (idUtente, livello, data_voto)
         VALUES (:id, :livello, CURRENT_TIMESTAMP)"
    );
    $stmt->execute([':id' => $id, ':livello' => $livello]);

    $conn->commit();
    header('Location: ../index.php?status=success');
    exit();
} catch (PDOException $e) {
    $conn->rollBack();
    header('Location: ../index.php?status=error');
    exit();
}
?>

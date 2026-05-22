<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['idUtente'])) {
    header('Location: ../include/loginForm.php');
    exit();
}

require_once '../include/dbHandler.php';
$conn = DBHandler::getPDO();

$myId = $_SESSION['idUtente'];
$domandaId = intval($_POST['domanda_id'] ?? 0);
if (!$domandaId) { header('Location: domanda.php'); exit(); }
$risposta = htmlspecialchars(trim($_POST['risposta'] ?? ''));
$oggi = date('Y-m-d');

if (empty($risposta)) {
    header('Location: domanda.php');
    exit();
}

try {
    $conn->beginTransaction();

    $check = $conn->prepare(
        "SELECT id FROM daily_answers
        WHERE idUtente = :id AND data_risposta = :oggi AND domanda_id = :did FOR UPDATE"
    );
    $check->execute([':id' => $myId, ':oggi' => $oggi, ':did' => $domandaId]);

    if ($check->fetch()) {
        $conn->rollBack();
        header('Location: domanda.php');
        exit();
    }

    $stmt = $conn->prepare(
        "INSERT INTO daily_answers (idUtente, domanda_idx, domanda_id, risposta, data_risposta)
        VALUES (:id, 0, :did, :risposta, :oggi)"
    );
    $stmt->execute([':id' => $myId, ':did' => $domandaId, ':risposta' => $risposta, ':oggi' => $oggi]);

    $conn->commit();
    header('Location: domanda.php');
    exit();
} catch (PDOException $e) {
    $conn->rollBack();
    header('Location: domanda.php');
    exit();
}
?>

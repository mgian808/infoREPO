<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['idUtente'])) {
    header('Location: ../include/loginForm.php');
    exit();
}

require_once 'dbHandler.php';
$conn = DBHandler::getPDO();

$idVoto   = intval($_GET['id'] ?? 0);
$idUtente = $_SESSION['idUtente'];

if (!$idVoto) {
    header('Location: ../userpages/profile.php?msg=errore');
    exit();
}

try {
    $conn->beginTransaction();

    // Verifica che il voto appartenga all'utente loggato
    $check = $conn->prepare(
        "SELECT idVoto FROM voti WHERE idVoto = :id AND idUtente = :uid"
    );
    $check->execute([':id' => $idVoto, ':uid' => $idUtente]);

    if (!$check->fetch()) {
        $conn->rollBack();
        header('Location: ../userpages/profile.php?msg=errore');
        exit();
    }

    $conn->prepare("DELETE FROM voti WHERE idVoto = :id AND idUtente = :uid")
         ->execute([':id' => $idVoto, ':uid' => $idUtente]);

    $conn->commit();
    header('Location: ../userpages/profile.php?msg=voto_eliminato');
    exit();
} catch (PDOException $e) {
    $conn->rollBack();
    header('Location: ../userpages/profile.php?msg=errore');
    exit();
}
?>
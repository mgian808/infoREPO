<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['idUtente'])) {
    header('Location: ../include/loginForm.php');
    exit();
}

require_once 'dbHandler.php';
$conn = DBHandler::getPDO();

$materia = htmlspecialchars(trim($_POST['materia'] ?? ''));
$descrizione = htmlspecialchars(trim($_POST['descrizione'] ?? ''));

if (empty($materia)) {
    die("Errore: il nome della materia è obbligatorio.");
}

try {
    $conn->beginTransaction();
    $stmt = $conn->prepare(
        "INSERT INTO materie (idUtente, materia, descrizione)
         VALUES (:idUtente, :materia, :descrizione)"
    );
    $stmt->execute([
        ':idUtente'    => $_SESSION['idUtente'],
        ':materia'     => $materia,
        ':descrizione' => $descrizione,
    ]);
    $conn->commit();
    header('Location: ../userpages/profile.php?msg=materia_aggiunta');
    exit();
} catch (PDOException $e) {
    $conn->rollBack();
    header('Location: ../userpages/profile.php?msg=errore');
    exit();
}
?>

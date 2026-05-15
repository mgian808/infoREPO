<?php

require_once 'dbHandler.php'; 
session_start(); 
$conn = DBHandler::getPDO(); 

$materia = $_POST['materia'];
$descrizione = $_POST['descrizione'];

try {
    $sql = "INSERT INTO materie (idUtente, materia, descrizione) VALUES (:idUtente, :materia, :descrizione)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':idUtente' => $_SESSION['idUtente'], ':materia' => $materia, ':descrizione' => $descrizione]);
    header('Location: ../userpages/profile.php');
    exit();
} catch (PDOException $e) {
    die("Errore di connessione: " . $e->getMessage());
}

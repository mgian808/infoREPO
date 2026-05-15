<?php

require_once 'dbHandler.php'; 
session_start(); 
$conn = DBHandler::getPDO(); 

$materia = $_POST['materia'];
$argomento = $_POST['argomento'];
$voto = $_POST['voto'];
$data = $_POST['data'];
$tipo = $_POST['tipo'];
$argomento_pulito = strtolower(trim($_POST['argomento']));
try {
    $sql = "INSERT INTO voti (idUtente, materia, argomento, voto, tipo, data_inserimento) VALUES (:idUtente, :materia, :argomento, :voto, :tipo, :data)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':idUtente' => $_SESSION['idUtente'], 
        ':materia' => $materia, 
        ':argomento' => $argomento_pulito, 
        ':voto' => $voto, 
        ':tipo' => $tipo,
        ':data' => $data
    ]);
    header('Location: ../userpages/profile.php');
    exit();
} catch (PDOException $e) {
    die("Errore di connessione: " . $e->getMessage());
}
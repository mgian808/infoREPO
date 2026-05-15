<?php
session_start();
date_default_timezone_set('Europe/Rome');

if(!isset($_SESSION['idUtente'])){ 
    header('Location:../include/loginForm.php'); 
    exit();
}

require_once 'dbHandler.php';
$conn = DBHandler::getPDO();

$id = $_SESSION['idUtente'];
$livello = $_POST['livello'];
$oggi = date("Y-m-d");

try {
    $check = $conn->prepare("SELECT idUtente FROM stress_log WHERE idUtente = :id AND DATE(data_voto) = :oggi");
    $check->execute([
        ':id'=> $id,
        ':oggi'=> $oggi,
    ]);
    
    if ($check->fetch()) {
        header("Location: ../index.php?status=gia_votato");
        exit();
    }

    $conn->exec("SET time_zone = '" . date('P') . "'"); 

    $sql = "INSERT INTO stress_log (idUtente, livello, data_voto) VALUES (:id, :livello, CURRENT_TIMESTAMP)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id, ':livello' => $livello]);

    header("Location: ../index.php?status=success"); 
} catch (PDOException $e) {
    die("Errore: " . $e->getMessage());
}
?>

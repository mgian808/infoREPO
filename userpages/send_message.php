<?php
session_start(); 

if (!isset($_SESSION['idUtente'])) {
    exit(); 
}

require_once '../include/dbHandler.php'; 
$conn = DBHandler::getPDO();  

$myId = $_SESSION['idUtente'];
$limiteMessaggi = 25; 
$intervalloSecondi = 60; 

$checkSql = "SELECT COUNT(*) FROM messaggi 
             WHERE idMittente = :myId 
             AND data_invio > DATE_SUB(NOW(), INTERVAL :secondi SECOND)";

$checkStmt = $conn->prepare($checkSql);
$checkStmt->bindValue(':myId', $myId, PDO::PARAM_INT);
$checkStmt->bindValue(':secondi', $intervalloSecondi, PDO::PARAM_INT);
$checkStmt->execute();

$messaggiInviati = $checkStmt->fetchColumn();

if ($messaggiInviati >= $limiteMessaggi) {
    http_response_code(429); 
    mostraErroreConnessione("Stai inviando messaggi troppo velocemente. Riprova tra un minuto.", 5);
    exit();
}


$receiverId = intval($_POST['idDestinatario']);
$testo_trim = trim($_POST['testo']); 
$testo = strip_tags($testo_trim);
$ip = $_SERVER['REMOTE_ADDR'];

if (!empty($testo)) {
    $stmt = $conn->prepare("INSERT INTO messaggi (idMittente, idDestinatario, testo, ip_mittente) VALUES (:myId, :receiverId, :testo, :ip)");
    $stmt->bindParam(':myId', $myId);
    $stmt->bindParam(':receiverId', $receiverId);
    $stmt->bindParam(':testo', $testo);
    $stmt->bindParam(':ip', $ip);
        
    $stmt->execute();
}

?>
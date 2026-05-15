<?php
session_start(); 

if (!isset($_SESSION['idUtente'])) {
    header("Location: ../include/loginForm.php");
    exit();
}
require_once '../include/dbHandler.php'; 
$conn = DBHandler::getPDO();  

$myId = $_SESSION['idUtente'];
$receiverId = intval($_GET['con']); 


$updateStmt = $conn->prepare("UPDATE messaggi SET letto = 1 WHERE idMittente = :receiverId AND idDestinatario = :myId AND letto = 0");
$updateStmt->bindParam(':receiverId', $receiverId);
$updateStmt->bindParam(':myId', $myId);
$updateStmt->execute();


$stmt = $conn->prepare("SELECT username FROM utenti WHERE idUtente = :receiverId");
$stmt->bindParam(':receiverId', $receiverId);
$stmt->execute();
$receiverUtente = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it" id="chat-page">
<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php require_once '../include/header_home.php'; ?>

<main class="page-container">
    <div class="chat-wrapper">
        <h2><?php echo $receiverUtente['username']; ?></h2>

        <div id="chat-box" class="chat-container">
            <div style="text-align:center;">
                <button class="btn" id="btn-carica-altro" onclick="caricaCronologia()">Carica messaggi precedenti</button>
            </div>
            <div id="messaggi-wrapper">
            </div>
        </div>

        <div class="input-area">
            <input type="hidden" id="destinatario_id" value="<?php echo $receiverId; ?>">
            <input type="text" id="messaggio_testo" placeholder="Scrivi un messaggio..." autocomplete="off">
            <button onclick="inviaMessaggio()" class="btn">Invia</button> 
        </div>
    </div>
    <script src="../js/chat.js"></script>

    <div style="text-align: center; margin-top: 20px;">
    <a href="lista_chat.php" style="text-decoration: none; color: #774caf; font-weight: bold; font-size: 18px;">← Torna alla Lista Chat</a>
</div>
</main>
</body>
</html>
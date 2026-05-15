<?php
session_start();
if (!isset($_SESSION['idUtente'])) {
    header('Location:../include/loginForm.php'); 
    exit();
}

require_once '../include/dbHandler.php';
$conn = DBHandler::getPDO();
$myId = $_SESSION['idUtente'];

$sql = "SELECT 
            u.idUtente, 
            u.username, 
            m.testo as ultimo_messaggio, 
            m.data_invio,
            (SELECT COUNT(*) FROM messaggi 
             WHERE idMittente = u.idUtente 
             AND idDestinatario = :myId 
             AND letto = 0) as contatore_non_letti
        FROM utenti u
        INNER JOIN messaggi m ON (u.idUtente = m.idMittente OR u.idUtente = m.idDestinatario)
        WHERE (m.idMittente = :myId OR m.idDestinatario = :myId)
          AND u.idUtente != :myId
          AND m.idMessaggio IN (
              SELECT MAX(idMessaggio) 
              FROM messaggi 
              WHERE idMittente = :myId OR idDestinatario = :myId 
              GROUP BY IF(idMittente = :myId, idDestinatario, idMittente)
          )
        ORDER BY m.data_invio DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([':myId' => $myId]);
$conversazioni = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>I miei Messaggi</title>
    <link rel="stylesheet" href="../style.css">

</head>
<body>
<?php require_once '../include/header_home.php'; ?>
<div class="lista-conversazioni">
    <h2 style="padding: 15px; background: #774caf; color: white; margin: 0;">Messaggi</h2>
    
    <?php if (empty($conversazioni)): ?>
        <div class="senza-messaggi">Non hai ancora nessuna conversazione attiva.</div>
    <?php else: ?>
        <?php foreach ($conversazioni as $chat): ?>
            <a href="chat.php?con=<?php echo $chat['idUtente']; ?>" class="chat-item">
                <div class="chat-info">
                    <div class="chat-info-header">
                        <h4><?php echo htmlspecialchars($chat['username']); ?></h4>
                        <?php if ($chat['contatore_non_letti'] > 0): ?>
                            <span class="badge"><?php echo $chat['contatore_non_letti']; ?></span>
                        <?php endif; ?>
                    </div>
                    <p><?php echo htmlspecialchars($chat['ultimo_messaggio']); ?></p>
                </div>
                <div class="chat-meta">
                    <?php echo date('H:i', strtotime($chat['data_invio'])); ?>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>


</body>
</html>
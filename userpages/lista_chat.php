<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['idUtente'])) {
    header('Location: ../include/loginForm.php');
    exit();
}

require_once '../include/dbHandler.php';
$conn = DBHandler::getPDO();

$myId = $_SESSION['idUtente'];

// Recupera tutte le conversazioni con ultimo messaggio e non letti
$sql = "SELECT
            u.idUtente,
            u.username,
            (SELECT testo FROM messaggi m2
             WHERE (m2.idMittente = :myId1 AND m2.idDestinatario = u.idUtente)
                OR (m2.idMittente = u.idUtente AND m2.idDestinatario = :myId2)
             ORDER BY m2.data_invio DESC LIMIT 1) AS ultimo_messaggio,
            (SELECT data_invio FROM messaggi m3
             WHERE (m3.idMittente = :myId3 AND m3.idDestinatario = u.idUtente)
                OR (m3.idMittente = u.idUtente AND m3.idDestinatario = :myId4)
             ORDER BY m3.data_invio DESC LIMIT 1) AS data_invio,
            (SELECT COUNT(*) FROM messaggi m4
             WHERE m4.idMittente = u.idUtente AND m4.idDestinatario = :myId5 AND m4.letto = 0) AS non_letti
        FROM utenti u
        WHERE u.idUtente != :myId6
          AND EXISTS (
              SELECT 1 FROM messaggi m5
              WHERE (m5.idMittente = :myId7 AND m5.idDestinatario = u.idUtente)
                 OR (m5.idMittente = u.idUtente AND m5.idDestinatario = :myId8)
          )
        ORDER BY data_invio DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':myId1' => $myId, ':myId2' => $myId,
    ':myId3' => $myId, ':myId4' => $myId,
    ':myId5' => $myId, ':myId6' => $myId,
    ':myId7' => $myId, ':myId8' => $myId,
]);
$conversazioni = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap — Messaggi</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/e9e5938e26.js" crossorigin="anonymous"></script>
</head>
<body>
<?php require_once '../include/navbar.php'; ?>

<div class="page-content-narrow">
    <h1 style="margin-bottom:20px;">Messaggi</h1>

    <div class="lista-conversazioni">
        <h2>Conversazioni</h2>
        <?php if (empty($conversazioni)): ?>
            <div class="senza-messaggi">
                <p style="font-size:2rem;margin-bottom:10px;"><i class="fa-regular fa-comment"></i></p>
                <p>Nessuna conversazione ancora.</p>
                <p style="font-size:13px;color:#999;margin:8px 0 16px;">Trova un match e inizia a studiare insieme!</p>
                <a href="../include/match.php" class="btn"><i class="fa-solid fa-comment-nodes"></i> Trova un Match</a>
            </div>
        <?php else: ?>
            <?php foreach ($conversazioni as $conv): ?>
                <a href="chat.php?con=<?php echo $conv['idUtente']; ?>" class="chat-item">
                    <div class="chat-avatar">
                        <?php echo strtoupper(substr($conv['username'], 0, 1)); ?>
                    </div>
                    <div class="chat-info">
                        <div class="chat-info-header">
                            <h4>
                                <?php echo htmlspecialchars($conv['username']); ?>
                                <?php if ($conv['non_letti'] > 0): ?>
                                    <span class="badge"><?php echo $conv['non_letti']; ?></span>
                                <?php endif; ?>
                            </h4>
                        </div>
                        <p><?php echo htmlspecialchars(substr($conv['ultimo_messaggio'] ?? '—', 0, 50)); ?></p>
                    </div>
                    <div class="chat-meta">
                        <?php if ($conv['data_invio']):
                            $d    = new DateTime($conv['data_invio']);
                            $oggi = new DateTime();
                            echo $d->format('Y-m-d') === $oggi->format('Y-m-d')
                                ? $d->format('H:i')
                                : $d->format('d/m');
                        endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['idUtente'])) {
    header('Location: ../include/loginForm.php');
    exit();
}

require_once '../include/dbHandler.php';
$conn = DBHandler::getPDO();

$myId       = $_SESSION['idUtente'];
$receiverId = intval($_GET['con'] ?? 0);
$msgPrecomp = htmlspecialchars(urldecode($_GET['msg'] ?? ''));

if (!$receiverId || $receiverId === $myId) {
    header('Location: lista_chat.php');
    exit();
}

// Segna come letti i messaggi ricevuti
$conn->prepare(
    "UPDATE messaggi SET letto = 1
     WHERE idMittente = :rid AND idDestinatario = :myId AND letto = 0"
)->execute([':rid' => $receiverId, ':myId' => $myId]);

// Recupera username interlocutore
$stmt = $conn->prepare("SELECT username FROM utenti WHERE idUtente = :id");
$stmt->execute([':id' => $receiverId]);
$receiver = $stmt->fetch();

if (!$receiver) {
    header('Location: lista_chat.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="it" id="chat-page">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap — Chat con <?php echo htmlspecialchars($receiver['username']); ?></title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/e9e5938e26.js" crossorigin="anonymous"></script>
</head>
<body>
<main class="page-container">
    <div class="chat-wrapper">
        <div class="chat-header">
            <a href="lista_chat.php">←</a>
            <div class="chat-avatar" style="width:32px;height:32px;font-size:14px;border-radius:50%;background:#774caf;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0;">
                <?php echo strtoupper(substr($receiver['username'], 0, 1)); ?>
            </div>
            <?php echo htmlspecialchars($receiver['username']); ?>
        </div>

        <div id="chat-box" class="chat-container">
            <div style="text-align:center;padding:8px;">
                <button class="btn btn-sm btn-outline" id="btn-carica-altro" onclick="caricaCronologia()">
                    Carica messaggi precedenti
                </button>
            </div>
            <div id="messaggi-wrapper"></div>
        </div>

        <div class="input-area">
            <input type="hidden" id="destinatario_id" value="<?php echo $receiverId; ?>">
            <input type="hidden" id="is_swap" value="0">
            <input type="text" id="messaggio_testo"
                   placeholder="Scrivi un messaggio..."
                   autocomplete="off"
                   value="<?php echo $msgPrecomp; ?>">
            <?php if ($msgPrecomp): ?>
            <script>
                // Se c'è un messaggio precompilato segna come swap
                document.getElementById('is_swap').value = '1';
            </script>
            <?php endif; ?>
            <button onclick="inviaMessaggio()">Invia</button>
        </div>
    </div>
</main>
<script src="../js/chat.js"></script>
</body>
</html>

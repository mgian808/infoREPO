<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap — Contattaci</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/e9e5938e26.js" crossorigin="anonymous"></script>
</head>
<body>
<?php require_once '../include/navbar.php'; ?>

<div class="page-content-narrow">

    <h1 style="margin-bottom:6px; color:#774caf;">Contattaci</h1>
    <p style="color:#888;margin-bottom:30px;">Hai domande, problemi o vuoi segnalare qualcosa? Scrivici.</p>

    <div class="myDiv" style="max-width:100%;margin-bottom:20px;">
        <h3>Supporto generale</h3>
        <p style="margin-top:10px;">Per domande sul funzionamento del sito o problemi con il tuo account.</p>
        <p style="margin-top:8px;">
            <a href="mailto:supporto@skillswap.it">supporto@skillswap.it</a>
        </p>
    </div>

    <div class="myDiv" style="max-width:100%;margin-bottom:20px;">
        <h3>Segnalazioni</h3>
        <p style="margin-top:10px;">Per segnalare comportamenti scorretti o contenuti inappropriati.</p>
        <p style="margin-top:8px;">
            <a href="mailto:segnalazioni@skillswap.it">segnalazioni@skillswap.it</a>
        </p>
    </div>

</div>

</body>
</html>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap — Login</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/e9e5938e26.js" crossorigin="anonymous"></script>
</head>
<body>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'navbar.php';
?>
<div class="page-content-narrow">
    <div class="myDiv">
        <h2>Bentornato</h2>
        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">Registrazione completata! Accedi ora.</div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autocomplete="email">
            <label for="psw">Password</label>
            <input type="password" id="psw" name="psw" required autocomplete="current-password">
            <input type="submit" value="Accedi">
        </form>
        <hr>
        <p style="font-size:14px;color:#888;">
            Non hai un account?
            <a href="signupForm.php" style="color:#774caf;font-weight:700;">Registrati</a>
        </p>
    </div>
</div>
</body>
</html>

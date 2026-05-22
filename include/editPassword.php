<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['idUtente'])) { header('Location: loginForm.php'); exit(); }
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap — Modifica Password</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/e9e5938e26.js" crossorigin="anonymous"></script>
</head>
<body>
<?php require_once 'navbar.php'; ?>
<div class="page-content-narrow">
    <div class="myDiv">
        <h2>Modifica Password</h2>
        <form action="updatePassword.php" method="POST">
            <label>Password attuale</label>
            <input type="password" name="old_password" required>
            <label>Nuova password</label>
            <input type="password" name="new_password" required minlength="8">
            <label>Conferma nuova password</label>
            <input type="password" name="confirm_password" required minlength="8">
            <input type="submit" value="Aggiorna Password">
        </form>
    </div>
</div>
</body>
</html>

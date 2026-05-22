<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['idUtente'])) {
    header('Location: loginForm.php');
    exit();
}

require_once 'dbHandler.php';
$conn = DBHandler::getPDO();

$id               = $_SESSION['idUtente'];
$old_password     = $_POST['old_password']     ?? '';
$new_password     = $_POST['new_password']     ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap — Aggiorna Password</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/e9e5938e26.js" crossorigin="anonymous"></script>
</head>
<body>
<?php require_once 'navbar.php'; ?>
<div class="page-content-narrow">
<?php

if ($new_password !== $confirm_password) {
    echo '<div class="alert alert-error">Le nuove password non corrispondono.</div>';
    echo '<a class="btn" href="editPassword.php">Torna indietro</a>';
    exit();
}

if (strlen($new_password) < 8) {
    echo '<div class="alert alert-error">La nuova password deve essere di almeno 8 caratteri.</div>';
    echo '<a class="btn" href="editPassword.php">Torna indietro</a>';
    exit();
}

try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("SELECT password FROM utenti WHERE idUtente = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($old_password, $user['password'])) {
        $conn->rollBack();
        echo '<div class="alert alert-error">La password attuale è errata.</div>';
        echo '<a class="btn" href="editPassword.php">Torna indietro</a>';
        exit();
    }

    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $upd = $conn->prepare("UPDATE utenti SET password = :password WHERE idUtente = :id");
    $upd->execute([':password' => $hashed, ':id' => $id]);

    $conn->commit();
    echo '<div class="alert alert-success">Password aggiornata con successo!</div>';
    echo '<a class="btn" href="../userpages/profile.php">Torna al Profilo</a>';

} catch (PDOException $e) {
    $conn->rollBack();
    echo '<div class="alert alert-error">Errore interno. Riprova.</div>';
}
?>
</div>
</body>
</html>

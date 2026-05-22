<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'dbHandler.php';
$conn = DBHandler::getPDO();

$email = trim($_POST['email'] ?? '');
$psw   = $_POST['psw'] ?? '';
$user  = null;

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $stmt = $conn->prepare("SELECT * FROM utenti WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $foundUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($foundUser && password_verify($psw, $foundUser['password'])) {
        $user = $foundUser;
        session_regenerate_id(true);
        $_SESSION['idUtente'] = $user['idUtente'];
        $_SESSION['nome']     = $user['nome'];
        $_SESSION['ruolo']    = $user['ruolo'] ?? 'user';
    }
}

if ($user) {
    if ($user['ruolo'] === 'admin') {
        header('Location: ../adminpages/stats.php');
    } else {
        header('Location: ../userpages/profile.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap — Accesso negato</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/e9e5938e26.js" crossorigin="anonymous"></script>
</head>
<body>
<?php require_once 'navbar.php'; ?>
<div class="page-content-narrow">
    <div class="myDiv">
        <h2>Accesso negato</h2>
        <div class="alert alert-error" style="margin-top:14px;">Email o password errati.</div>
        <div style="display:flex;gap:10px;margin-top:16px;flex-wrap:wrap;">
            <a href="loginForm.php" class="btn">Riprova il Login</a>
            <a href="signupForm.php" class="btn btn-outline">Registrati</a>
        </div>
    </div>
</div>
</body>
</html>

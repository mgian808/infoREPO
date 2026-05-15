<?php
require_once 'dbHandler.php'; 
session_start(); 
$conn = DBHandler::getPDO(); 

$email = $_POST['email'] ?? '';
$psw = $_POST['psw'] ?? '';
$user = null; 

if(filter_var($email, FILTER_VALIDATE_EMAIL)){
    $sql = "SELECT * FROM utenti WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':email' => $email]);
    $foundUser = $stmt->fetch(PDO::FETCH_ASSOC);
    if($foundUser && password_verify($psw, $foundUser['password'])){
        $user = $foundUser;
        $_SESSION['idUtente'] = $user['idUtente'];
    } 
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Stato Login</title>
    <link rel="stylesheet" href="../style.css"> 
</head>
<body>
    <div>
        <?php if ($user): ?>
            <h1>Benvenuto, <?php echo htmlspecialchars($user['nome']); ?>!</h1>
            <p>Il login è stato effettuato con successo.</p>    
            <a href="../userpages/profile.php" class="btn">Vai al Profilo</a>
            <a href="../index.php" class="btn">Vai alla Home</a>
        <?php else: ?>
            <h1>Accesso Negato</h1>
            <p>Email o Password errati.</p>
            <div class="options">
                <a href="loginForm.php" class="btn">Riprova il Login</a> 
                <a href="signupForm.php" class="btn">Registrati ora</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

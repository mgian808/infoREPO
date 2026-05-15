<?php
session_start(); 
if(!isset($_SESSION['idUtente'])){ 
    header('Location: loginForm.php'); 
    exit();
}
require_once 'dbHandler.php';
$id = $_SESSION['idUtente'];
$conn = DBHandler::getPDO();
 
try{
    $sql = "SELECT * FROM utenti WHERE idUtente = :idUtente";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':idUtente' => $id]);
    $user = $stmt->fetch();

    if($user){
        $user_name = $user['nome'];
    }else{
        header('Location: logout.php');
        exit();
    }
}catch (PDOException $e) {
    die("Errore di connessione: " . $e->getMessage());
}


?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Password</title>
    <link rel="stylesheet" href="../style.css">
</head>
<?php require_once '../include/header_home.php'; ?>
<body>
    <h1>Modifica Password per <?php echo htmlspecialchars($user_name); ?></h1>
    <form action="updatePassword.php" method="POST">
        <label for="old_password">Password Attuale:</label>
        <input type="password" id="old_password" name="old_password" required>
        <br><br>
        <label for="new_password">Nuova Password:</label>
        <input type="password" id="new_password" name="new_password" required>
        <br><br>
        <label for="confirm_password">Conferma Nuova Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <br><br>
        <input type="submit" value="Aggiorna Password">
    </form>
</body>
</html>
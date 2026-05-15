<?php
session_start(); 
if(!isset($_SESSION['idUtente'])){ 
    header('Location: loginForm.php'); 
    exit();
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiorna Password</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    
<?php
require_once 'dbHandler.php';
$id = $_SESSION['idUtente'];
$conn = DBHandler::getPDO();




$old_password = $_POST['old_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

if ($new_password !== $confirm_password) {
    die("Le nuove password non corrispondono.");
}

try {
    $sql = "SELECT password FROM utenti WHERE idUtente = :idUtente";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':idUtente' => $id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($old_password, $user['password'])) {
        echo "La password attuale è errata.";
        echo "<br><br><a class='btn' href='editPassword.php'>Torna indietro</a>";
        exit();
    }

    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
    $update_sql = "UPDATE utenti SET password = :password WHERE idUtente = :idUtente";
    $update_stmt = $conn->prepare($update_sql);
    
    if ($update_stmt->execute([':password' => $hashed_new_password, ':idUtente' => $id])) {
        echo "La password è stata aggiornata con successo.";
        echo "<br><br><a class='btn' href='../userpages/profile.php'>Torna al profilo</a>";
        exit();
    }
    exit();
} catch (PDOException $e) {
    die("Errore di connessione: " . $e->getMessage());
}

?>
</body>
</html>
<?php
require_once 'dbHandler.php';
$conn = DBHandler::getPDO();

$first_name = htmlspecialchars(trim($_POST['first_name'] ?? ''));
$last_name  = htmlspecialchars(trim($_POST['last_name']  ?? ''));
$username   = htmlspecialchars(trim($_POST['username']   ?? ''));
$address    = htmlspecialchars(trim($_POST['address']    ?? ''));
$dob        = $_POST['dob']      ?? '';
$province   = $_POST['province'] ?? '';
$comune     = $_POST['comune']   ?? '';
$school     = $_POST['school']   ?? '';
$classe     = intval($_POST['classe'] ?? 0);
$phone      = htmlspecialchars(trim($_POST['phone'] ?? ''));
$email      = trim($_POST['email'] ?? '');
$psw        = $_POST['psw'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Errore: formato email non valido.");
}

if (empty($username) || empty($first_name) || empty($last_name)) {
    die("Errore: compila tutti i campi obbligatori.");
}

$hashed_psw = password_hash($psw, PASSWORD_DEFAULT);

$sql = "INSERT INTO utenti 
        (nome, cognome, username, indirizzo, dataNascita, provinciaIstituto,
         comuneIstituto, nomeIstituto, classe, telefono, email, password, ruolo, punti)
        VALUES 
        (:nome, :cognome, :username, :indirizzo, :dataNascita, :provinciaIstituto,
         :comuneIstituto, :nomeIstituto, :classe, :telefono, :email, :password, 'user', 10)";

try {
    $conn->beginTransaction();
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':nome'              => $first_name,
        ':cognome'           => $last_name,
        ':username'          => $username,
        ':indirizzo'         => $address,
        ':dataNascita'       => $dob,
        ':provinciaIstituto' => $province,
        ':comuneIstituto'    => $comune,
        ':nomeIstituto'      => $school,
        ':classe'            => $classe,
        ':telefono'          => $phone,
        ':email'             => $email,
        ':password'          => $hashed_psw,
    ]);
    $conn->commit();
    header('Location: ../include/loginForm.php?registered=1');
    exit();
} catch (PDOException $e) {
    $conn->rollBack();
    if ($e->getCode() == 23000) {
        die("Errore: email o username già registrati. <a href='signupForm.php'>Torna indietro</a>");
    }
    die("Errore nel database: " . $e->getMessage());
}
?>

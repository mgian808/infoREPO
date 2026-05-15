<?php
require_once 'dbHandler.php';

$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$username = $_POST['username'];
$address = $_POST['address'];
$dob = $_POST['dob'];
$province = $_POST['province'];
$comune = $_POST['comune'];
$school = $_POST['school'];
$classe = $_POST['classe'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$psw = $_POST['psw'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Errore: Formato email non valido.");
}

$hashed_psw = password_hash($psw, PASSWORD_DEFAULT);

$sql = "INSERT INTO utenti (nome, cognome, username, indirizzo, dataNascita, provinciaIstituto, comuneIstituto, nomeIstituto, classe, telefono, email, password) 
        VALUES (:nome, :cognome, :username, :indirizzo, :dataNascita, :provinciaIstituto, :comuneIstituto, :nomeIstituto, :classe, :telefono, :email, :password)";

try {
    $conn = DBHandler::getPDO(); 
    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ':nome' => $first_name,
        ':cognome' => $last_name,
        ':username' => $username,
        ':indirizzo' => $address,
        ':dataNascita' => $dob,
        ':provinciaIstituto' => $province,
        ':comuneIstituto' => $comune,
        ':nomeIstituto' => $school,
        ':classe' => $classe,
        ':telefono' => $phone,
        ':email' => $email,
        ':password' => $hashed_psw 
    ]);
    
    header('Location: ../index.php');
    exit(); 
} catch (PDOException $e) {
    die("Errore nel database: " . $e->getMessage());
}



<?php
// menuChoice.php — framework classificazione pagine
// Include con require_once in cima a ogni pagina in userpages/ e adminpages/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$includeDir = dirname(__FILE__);
$json       = file_get_contents($includeDir . '/pages.json');
$obj        = json_decode($json);
$pageName   = basename($_SERVER['PHP_SELF']);

// Calcola quanti livelli di profondità siamo dalla root
$profondita = substr_count(str_replace('\\', '/', $_SERVER['PHP_SELF']), '/') - 2;
$base       = str_repeat('../', max(0, $profondita));

// Pagine che richiedono login
if (in_array($pageName, $obj->loggedInPages)) {
    if (!isset($_SESSION['idUtente'])) {
        header('Location: ' . $base . 'include/loginForm.php');
        exit();
    }
}

// Pagine riservate ad admin
if (in_array($pageName, $obj->adminpages)) {
    if (!isset($_SESSION['ruolo']) || $_SESSION['ruolo'] !== 'admin') {
        header('Location: ' . $base . 'index.php');
        exit();
    }
}

// Connessione DB
if (in_array($pageName, $obj->DBPages)) {
    require_once $includeDir . '/dbHandler.php';
    $conn = DBHandler::getPDO();
}
?>

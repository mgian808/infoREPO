<?php
session_start(); // the page is part of the session
session_unset(); // destroy session variables
session_destroy(); // destroy session
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGOUT</title>
    <link rel="stylesheet" href="../style.css">
    
</head>
<body>
    <p>Log Out effettuato con successo :<a href="../index.php" class="myTitle">Torna alla home</a></p>
     <br><br>
</body>
</html>
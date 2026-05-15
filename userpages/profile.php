<?php
session_start(); 
if(!isset($_SESSION['idUtente'])){ 
    header('Location:../include/loginForm.php'); 
    exit();
}
require_once '../include/dbHandler.php';
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
        header('Location: ../include/logout.php');
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
    <title>Profilo</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php require_once '../include/header_home.php'; ?>
    <h1>Benvenuto, <?php echo htmlspecialchars($user_name); ?>!</h1>
    
    <hr>
    <h3 style="font-size: 23px;">Il mio Profilo</h3>
    <div class = 'myDiv'>
        <p><b>Nome : </b><?php echo htmlspecialchars($user_name); ?></p>
        <p><b>Cognome: </b><?php echo htmlspecialchars($user['cognome']); ?></p>
        <p><b>Username : </b><?php echo htmlspecialchars($user['username']); ?></p>
        <p><b>Data di Nascita: </b><?php echo date('d/m/Y', strtotime($user['dataNascita'])); ?></p>
        <p><b>Provincia del tuo Istituto: </b><?php echo htmlspecialchars($user['provinciaIstituto']); ?></p>
        <p><b>Comune del tuo Istituto: </b><?php echo htmlspecialchars($user['comuneIstituto']); ?></p>
        <p><b>Nome del tuo Istituto: </b><?php echo htmlspecialchars($user['nomeIstituto']); ?></p>
        <p><b>Classe: </b><?php echo htmlspecialchars($user['classe']); ?>^</p>
        <p><b>Telefono: </b><?php echo htmlspecialchars($user['telefono']); ?></p>
        <p><b>Email: </b><?php echo htmlspecialchars($user['email']); ?></p>
        <p><b>Password:</b> ******** <a href="../include/editPassword.php" style="font-size: 0.8em;">(Modifica)</a></p>
    </div>
    <br><hr><br>

    <h3 style="font-size: 23px;">I miei voti</h3>
    
        <?php
        try {
            $sql = "SELECT * FROM voti WHERE idUtente = :idUtente ORDER BY idVoto DESC"; 
            $stmt = $conn->prepare($sql);
            $stmt->execute([':idUtente' => $id]);
            $voti = $stmt->fetchAll();

            if ($voti) {
                foreach ($voti as $voto) {
                    echo "<p><b>Materia:</b> " . htmlspecialchars($voto['materia']) . " | <b>Argomento:</b> " . htmlspecialchars($voto['argomento']) . " | <b>Voto:</b> " . htmlspecialchars($voto['voto']) . " | <b>Tipo:</b> " . htmlspecialchars($voto['tipo']) ." | <b>Data:</b> " . date('d/m/Y ', strtotime($voto['data_inserimento'])) . "</p>";
                }
            } else {
                echo "<p>Nessun voto aggiunto finora.</p>";
            }
        } catch (PDOException $e) {
            die("Errore di connessione: " . $e->getMessage());
        }
        ?>

        <br><hr><br>
    <div class="myDiv">
        <h3>Aggiungi Voto</h3>
        <form action="../include/addGrade.php" method="POST">
            <label for="materia">Materia:</label>
            <select id="materia" name="materia" required>
                <option value="" disabled selected>Seleziona una materia...</option>
                <?php
                try {
                    $myId = $_SESSION['idUtente'];
                    $sql = "SELECT materia FROM materie WHERE idUtente = :idUtente ORDER BY materia ASC";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([':idUtente' => $myId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . htmlspecialchars($row['materia']) . '">' . htmlspecialchars($row['materia']) . '</option>';
                    }
                } catch (PDOException $e) {
                    die("Errore di connessione: " . $e->getMessage());
                }
                ?>
            </select>
            <br><br>

            <label for="argomento">Argomento:</label>
            <input type="text" id="argomento" name="argomento" placeholder="Es: Derivate, Canto V Inferno..." required>
            <br><br>

            <label for="voto">Voto:</label>
            <input type="number" id="voto" name="voto" min="1" max="10" step="0.25" required>
            <br><br>

            <label for="data">Data:</label>
            <input type="date" id="data" name="data" required>
            <br><br>

            <label for="tipo">Cosa vuoi fare?</label>
            <select id="tipo" name="tipo" required>
                <option value="OFFRO">Posso aiutare (OFFRO)</option>
                <option value="CERCO">Ho bisogno di aiuto (CERCO)</option>
                <option value="NEUTRO">Solo per mio archivio (NEUTRO)</option>
            </select>
            <br><br>

            <input type="submit" value="Aggiungi Voto">
        </form>

        <br><hr><br>

        <h3>Aggiungi Materie</h3>
        <form action="../include/addSubject.php" method="POST">
            <label for="materia">Materia:</label>
            <input type="text" id="materia" name="materia" required><br><br>
            <label for="descrizione">Descrizione:</label><br>
            <textarea name="descrizione" rows="4" cols="50" required></textarea> <br><br>
            <input type="submit" value="Aggiungi Materia">
        </form>
    </div>
    <br><hr><br>
    <a href="../include/logout.php" class="myTitle">Log Out</a>
    <a href="../index.php" class="myTitle">Torna alla home</a> <br><br>
    

</body>
</html>
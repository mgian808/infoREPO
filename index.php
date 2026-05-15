<?php
    session_set_cookie_params([
        'lifetime' => 3600,    
        'path' => '/',         
        'domain' => 'skillswapstudio.it',   
        'secure' => true,        
        'httponly' => true,        
        'samesite' => 'Lax'         
    ]);
    session_start();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/e9e5938e26.js" crossorigin="anonymous"></script>

    
</head>
<body>
    <div class="logo">
        <img src="images/logo_bozza.png" alt="SkillSwap Logo">
    </div>
    <?php
    require "include/dbHandler.php";
    $conn = DBHandler::getPDO();
    ?>
    <!--<hr><br>-->
    <div class="myDiv">
        <br>
        <a href="include/signupForm.php" class="myTitle" ><i class="fa-solid fa-user"></i> Sign Up</a> <br><br>
        <a href="include/loginForm.php" class="myTitle"><i class="fa-solid fa-user"></i> Login</a> <br><br>
        <a href="userpages/profile.php" class="myTitle"><i class="fa-solid fa-user"></i> Profile</a> <br><br>
        <a href="include/match.php" class="myTitle"><i class="fa-solid fa-user"></i> Match</a> <br><br>
        <a href="userpages/lista_chat.php" class="myTitle"><i class="fa-solid fa-user"></i> Messaggi</a> <br><br>
    </div>
    <br><hr>
    <div class="stress-input-box">
    <?php
    date_default_timezone_set('Europe/Rome');
    $oggi = date("Y-m-d");
    if(!isset($_SESSION['idUtente'])){ 
        echo "<p style='color: #555;'>Accedi per condividere il tuo livello di stress oggi.</p>";
    } else {
        $stmt_voto = $conn->prepare("SELECT livello, data_voto FROM stress_log WHERE idUtente = :id AND DATE(data_voto) = :oggi");
        $stmt_voto->execute([':id' => $_SESSION['idUtente'], ':oggi' => $oggi]);
        $voto_utente = $stmt_voto->fetch(PDO::FETCH_ASSOC);
        
        if ($voto_utente): ?>
            <!-- messaggio dopo il voto -->
            <div style="background: #e8c1ffff; padding: 15px; border-radius: 8px; border: 1px solid #481169; width: 420px; box-sizing: border-box;">
                <h3 style="color: #000000ff; margin-top:0;">Grazie per aver votato!</h3>
                <p>Il tuo livello di stress oggi: <strong><?php echo $voto_utente['livello']; ?>/10</strong></p>
                <p style="font-size: 0.85em; color: #666;">
                    Registrato il: <?php echo date("d-m-Y", strtotime($voto_utente['data_voto'])); ?>
                </p>
            </div>
        <?php else: ?>
            <!-- form (solo se non ha votato) -->
            <h3>Come ti senti oggi?</h3>
            <form action="include/salva_stress.php" method="POST">
                <label>Livello di stress: <span id="valoreStress">5</span></label><br>
                <input type="range" name="livello" min="1" max="10" value="5" oninput="document.getElementById('valoreStress').innerText = this.value">
                <br>
                <button type="submit" class="btn">Invia</button>
            </form>
        <?php endif; 
    }
    
    ?>

    <?php
    // logica media
    $sql_media = "SELECT AVG(livello) as media FROM stress_log WHERE DATE(data_voto) = CURRENT_DATE";
    $stmt_media = $conn->prepare($sql_media);
    $stmt_media->execute();
    $dato = $stmt_media->fetch(PDO::FETCH_ASSOC);

    $media = ($dato && $dato['media']) ? round($dato['media'], 1) : 0; 
    $percentuale = $media * 10; 
    $colore = ($media > 7) ? "#F44336" : (($media > 4) ? "#FFC107" : "#4CAF50");
    ?>

    <div class="stress-indicator" style="margin-top: 20px;">
        <span>Media Community: <?php echo $media; ?>/10</span><br>
        <small>Ultimo aggiornamento: <?php echo date("H:i"); ?></small>
    </div>
</div>

<div class="stress-section">
    <h4>Stress della community oggi</h4>
    <div class="stress-container" style="background:#eee; border-radius:10px; overflow:hidden;">
        <div class="stress-bar" style="width: <?php echo $percentuale; ?>%; background-color: <?php echo $colore; ?>; height: 30px; text-align: center; color: white; transition: width 0.5s;">
            <?php if($percentuale > 0) echo $percentuale . "%"; ?>
        </div>
    </div>
</div>

    <br><br>
</body>
</html>
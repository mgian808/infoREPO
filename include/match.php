<?php

require_once 'dbHandler.php';  
session_start();
$conn = DBHandler::getPDO();   

if (!isset($_SESSION['idUtente'])) {
    header('Location:../include/loginForm.php'); 
    exit();
}

$myId = $_SESSION['idUtente'];
/*
$sql = "SELECT DISTINCT 
            u.username, 
            u.idUtente,
            u.classe, 
            v_altri.materia, 
            v_altri.argomento 
        FROM utenti u
        INNER JOIN voti v_altri ON u.idUtente = v_altri.idUtente
        INNER JOIN voti v_miei ON v_altri.materia = v_miei.materia
        WHERE v_miei.idUtente = :myId
          AND v_miei.tipo = 'CERCO'
          AND v_altri.tipo = 'OFFRO'
          AND u.idUtente != :myId";
*/
$sql = "CALL get_matches(:myId)";
$stmt = $conn->prepare($sql);
$stmt->execute(['myId' => $myId]);
$matches = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <title>SkillSwap - I tuoi Match</title>
    <link rel="stylesheet" href="../style.css"> </head>
<body>
    <?php require_once 'header_home.php'; ?>
    <h1>I tuoi Match</h1>
    <h2><i> SkillSwap disponibili per te</i></h2>
<br>
    <div class="match-container">
        <?php if (count($matches) > 0): ?>
            <?php foreach ($matches as $match): ?>
                <div class="match-card">
                    <h3><?php echo htmlspecialchars($match['username']); ?></h3>
                    <p><strong>Classe:</strong> <?php echo htmlspecialchars($match['classe']); ?>^</p>
                    <p><strong>Può aiutarti in:</strong> <?php echo htmlspecialchars($match['materia']); ?></p>
                    <p><strong>Argomento:</strong> <?php echo htmlspecialchars($match['argomento']); ?></p>
                    <a href="../userpages/chat.php?con=<?php echo $match['idUtente']; ?>" class="btn">Contatta</a>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Al momento non ci sono match disponibili. Prova ad aggiungere altre materie in cui hai bisogno di aiuto!</p>
        <?php endif; ?>
    </div>
</body>
</html>
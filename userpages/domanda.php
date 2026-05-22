<?php
require_once '../include/menuChoice.php';
$myId = $_SESSION['idUtente'];

// Numero totale domande nel DB
$totDomande = $conn->query("SELECT COUNT(*) FROM domande_giornaliere")->fetchColumn();

// Rotazione per giorno dell'anno — indice 1-based per l'id nel DB
$domandaId = ($totDomande > 0)
    ? (intval(date('z')) % $totDomande) + 1
    : 1;

// Carica la domanda dal DB
$stmtD = $conn->prepare("SELECT * FROM domande_giornaliere WHERE id = :id");
$stmtD->execute([':id' => $domandaId]);
$domandaOggi = $stmtD->fetch();

if (!$domandaOggi) {
    die("Nessuna domanda disponibile.");
}

$opzioni = json_decode($domandaOggi['opzioni'], true);
$oggi    = date('Y-m-d');

// Controlla se l'utente ha già risposto oggi a questa domanda
$stmtCheck = $conn->prepare(
    "SELECT risposta FROM daily_answers
     WHERE idUtente = :id AND data_risposta = :oggi AND domanda_id = :did"
);
$stmtCheck->execute([':id' => $myId, ':oggi' => $oggi, ':did' => $domandaId]);
$rispostaUtente = $stmtCheck->fetchColumn();

// Risultati di oggi per questa domanda
$stmtRis = $conn->prepare(
    "SELECT risposta, COUNT(*) AS n FROM daily_answers
     WHERE data_risposta = :oggi AND domanda_id = :did
     GROUP BY risposta"
);
$stmtRis->execute([':oggi' => $oggi, ':did' => $domandaId]);
$risultati = $stmtRis->fetchAll();

$totVoti = array_sum(array_column($risultati, 'n'));
$mapRis  = [];
foreach ($risultati as $r) $mapRis[$r['risposta']] = $r['n'];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap — Domanda del Giorno</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/e9e5938e26.js" crossorigin="anonymous"></script>
</head>
<body>
<?php require_once '../include/navbar.php'; ?>

<div class="page-content-narrow">
    <h1 style="margin-bottom:6px;"><i class="fa-solid fa-square-poll-horizontal"></i> Domanda del Giorno</h1>
    <p style="color:#555;margin-bottom:20px;">Una domanda nuova ogni giorno. Rispondi e vedi cosa pensano gli altri!</p>

    <div class="domanda-box">
        <div class="domanda-label"><i class="fa-solid fa-calendar-days"></i> <?php echo date('d/m/Y'); ?></div>
        <h3><?php echo htmlspecialchars($domandaOggi['testo']); ?></h3>

        <?php if ($rispostaUtente !== false): ?>
            <div class="alert alert-success">Hai risposto: <b><?php echo htmlspecialchars($rispostaUtente); ?></b></div>
            <?php foreach ($opzioni as $opz):
                $n      = $mapRis[$opz] ?? 0;
                $pct    = $totVoti > 0 ? round($n / $totVoti * 100) : 0;
                $isMine = ($opz === $rispostaUtente);
            ?>
                <div class="bar-label">
                    <span><?php echo htmlspecialchars($opz); ?><?php echo $isMine ? ' <- tu' : ''; ?></span>
                    <span><?php echo $pct; ?>% (<?php echo $n; ?>)</span>
                </div>
                <div class="bar-wrap">
                    <div class="bar-fill" style="width:<?php echo $pct; ?>%;<?php echo $isMine ? 'background:#2e2e2e;' : ''; ?>"></div>
                </div>
            <?php endforeach; ?>
            <p style="font-size:13px;color:#555;margin-top:8px;"><?php echo $totVoti; ?> risposte totali oggi.</p>
        <?php else: ?>
            <form action="salva_risposta.php" method="POST">
                <input type="hidden" name="domanda_id" value="<?php echo $domandaId; ?>">
                <?php foreach ($opzioni as $opz): ?>
                    <label class="poll-option">
                        <input type="radio" name="risposta" value="<?php echo htmlspecialchars($opz); ?>" required>
                        <?php echo htmlspecialchars($opz); ?>
                    </label>
                <?php endforeach; ?>
                <div style="margin-top:16px;">
                    <button type="submit" class="btn">Rispondi</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
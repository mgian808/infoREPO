<?php
require_once '../include/menuChoice.php';

$utentiAttivi = $conn->query(
    "SELECT u.username, u.nomeIstituto, u.classe, u.punti,
            COUNT(m.idMessaggio) AS n_messaggi
     FROM utenti u
     LEFT JOIN messaggi m ON m.idMittente = u.idUtente
     WHERE u.ruolo = 'user'
     GROUP BY u.idUtente, u.username, u.nomeIstituto, u.classe, u.punti
     ORDER BY n_messaggi DESC LIMIT 10"
)->fetchAll();

$materieRichieste = $conn->query(
    "SELECT LOWER(TRIM(materia)) AS materia, COUNT(*) AS n
     FROM voti WHERE tipo = 'CERCO'
     GROUP BY LOWER(TRIM(materia)) ORDER BY n DESC LIMIT 8"
)->fetchAll();

$materieOfferte = $conn->query(
    "SELECT LOWER(TRIM(materia)) AS materia, COUNT(*) AS n
     FROM voti WHERE tipo = 'OFFRO'
     GROUP BY LOWER(TRIM(materia)) ORDER BY n DESC LIMIT 8"
)->fetchAll();

$stressGiorni = $conn->query(
    "SELECT DATE(data_voto) AS giorno,
            ROUND(AVG(livello),1) AS media, COUNT(*) AS n_voti
     FROM stress_log
     WHERE data_voto >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
     GROUP BY DATE(data_voto) ORDER BY giorno DESC"
)->fetchAll();

$totUtenti = $conn->query("SELECT COUNT(*) FROM utenti WHERE ruolo='user'")->fetchColumn();
$totMsg    = $conn->query("SELECT COUNT(*) FROM messaggi")->fetchColumn();
$totVoti   = $conn->query("SELECT COUNT(*) FROM voti")->fetchColumn();
$avgStress = $conn->query("SELECT ROUND(AVG(livello),1) FROM stress_log WHERE DATE(data_voto)=CURDATE()")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap — Statistiche Admin</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/e9e5938e26.js" crossorigin="anonymous"></script>
</head>
<body>
<?php require_once '../include/navbar.php'; ?>

<div class="page-content">
    <h1 style="margin-bottom:6px;"><i class="fa-solid fa-chart-bar"></i> Pannello Statistiche</h1>
    <p style="color:#555;margin-bottom:24px;">Panoramica generale della piattaforma.</p>

    <!-- KPI -->
    <div class="stat-grid">
        <div class="stat-card"><div class="stat-num"><?php echo $totUtenti; ?></div><div class="stat-label">Utenti</div></div>
        <div class="stat-card"><div class="stat-num"><?php echo $totMsg; ?></div><div class="stat-label">Messaggi totali</div></div>
        <div class="stat-card"><div class="stat-num"><?php echo $totVoti; ?></div><div class="stat-label">Voti inseriti</div></div>
        <div class="stat-card"><div class="stat-num"><?php echo $avgStress ?: '—'; ?></div><div class="stat-label">Stress medio oggi</div></div>
    </div>

    <!-- Utenti attivi -->
    <div class="myDiv" style="max-width:100%;margin-bottom:24px;">
        <h3 style="margin-bottom:14px;">👑 Utenti più attivi</h3>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr><th>#</th><th>Username</th><th>Istituto</th><th>Classe</th><th>Messaggi</th><th>Punti</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($utentiAttivi as $i => $u): ?>
                    <tr>
                        <td><b><?php echo $i+1; ?></b></td>
                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                        <td style="font-size:13px;"><?php echo htmlspecialchars($u['nomeIstituto'] ?? '—'); ?></td>
                        <td><?php echo $u['classe'] ?? '—'; ?>^</td>
                        <td><b><?php echo $u['n_messaggi']; ?></b></td>
                        <td><?php echo $u['punti'] ?? 0; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Materie -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
        <div class="myDiv" style="max-width:100%;">
            <h3 style="margin-bottom:14px;">🔴 Materie più richieste</h3>
            <?php foreach ($materieRichieste as $m):
                $pct = $materieRichieste[0]['n'] > 0 ? round($m['n']/$materieRichieste[0]['n']*100) : 0; ?>
                <div class="bar-label">
                    <span><?php echo htmlspecialchars(ucfirst($m['materia'])); ?></span>
                    <span><?php echo $m['n']; ?></span>
                </div>
                <div class="bar-wrap">
                    <div class="bar-fill" style="width:<?php echo $pct; ?>%;background:#e05252;"></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="myDiv" style="max-width:100%;">
            <h3 style="margin-bottom:14px;">🟢 Materie più offerte</h3>
            <?php foreach ($materieOfferte as $m):
                $pct = $materieOfferte[0]['n'] > 0 ? round($m['n']/$materieOfferte[0]['n']*100) : 0; ?>
                <div class="bar-label">
                    <span><?php echo htmlspecialchars(ucfirst($m['materia'])); ?></span>
                    <span><?php echo $m['n']; ?></span>
                </div>
                <div class="bar-wrap">
                    <div class="bar-fill" style="width:<?php echo $pct; ?>%;background:#4caf50;"></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Stress 14 giorni -->
    <div class="myDiv" style="max-width:100%;">
        <h3 style="margin-bottom:14px;">📈 Stress community — ultimi 14 giorni</h3>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead><tr><th>Data</th><th>Media</th><th>Voti</th><th style="min-width:140px;">Barra</th></tr></thead>
                <tbody>
                    <?php foreach ($stressGiorni as $g):
                        $pct = round($g['media'] * 10);
                        $col = $g['media'] > 7 ? '#e05252' : ($g['media'] > 4 ? '#f0a500' : '#4caf50'); ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($g['giorno'])); ?></td>
                            <td><b style="color:<?php echo $col; ?>"><?php echo $g['media']; ?>/10</b></td>
                            <td><?php echo $g['n_voti']; ?></td>
                            <td>
                                <div class="bar-wrap">
                                    <div class="bar-fill" style="width:<?php echo $pct; ?>%;background:<?php echo $col; ?>;"></div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>

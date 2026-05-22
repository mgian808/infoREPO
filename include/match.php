<?php
require_once 'dbHandler.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$conn = DBHandler::getPDO();

if (!isset($_SESSION['idUtente'])) {
    header('Location: ../include/loginForm.php');
    exit();
}

$myId          = $_SESSION['idUtente'];
$filtroMateria = htmlspecialchars(trim($_GET['materia'] ?? ''));
$filtroTipo    = htmlspecialchars(trim($_GET['tipo']    ?? ''));

// Chiama la SP
$stmt = $conn->prepare("CALL get_matches_scored(:myId)");
$stmt->execute([':myId' => $myId]);
$righe = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Raggruppa per utente e calcola tipo_match in PHP
// dove abbiamo i voti giusti: voto_loro = voto OFFRO dell'altro,
// voto_mio = voto OFFRO mio
$utentiMap = [];
foreach ($righe as $r) {
    $id = $r['idUtente'];
    if (!isset($utentiMap[$id])) {
        $utentiMap[$id] = [
            'idUtente'       => $id,
            'username'       => $r['username'],
            'nomeIstituto'   => $r['nomeIstituto'],
            'classe'         => $r['classe'],
            'materia_offerta'=> null,
            'voto_loro'      => null,
            'materia_cercata'=> null,
            'voto_mio'       => null,
            'score_bonus'    => intval($r['score_bonus']),
        ];
    }
    // Tieni il voto più alto per materia_offerta (l'altro mi aiuta)
    if (!empty($r['materia_offerta'])) {
        if (is_null($utentiMap[$id]['voto_loro']) || $r['voto_loro'] > $utentiMap[$id]['voto_loro']) {
            $utentiMap[$id]['materia_offerta'] = $r['materia_offerta'];
            $utentiMap[$id]['voto_loro']       = $r['voto_loro'];
        }
    }
    // Tieni il voto più alto per materia_cercata (io aiuto lui)
    if (!empty($r['materia_cercata'])) {
        if (is_null($utentiMap[$id]['voto_mio']) || $r['voto_mio'] > $utentiMap[$id]['voto_mio']) {
            $utentiMap[$id]['materia_cercata'] = $r['materia_cercata'];
            $utentiMap[$id]['voto_mio']        = $r['voto_mio'];
        }
    }
}

// Calcola tipo_match e score finale in PHP
$matches = [];
foreach ($utentiMap as $m) {
    $haOfferta = !is_null($m['materia_offerta']);
    $haCercata = !is_null($m['materia_cercata']);
    $votoLoro  = floatval($m['voto_loro'] ?? 0);
    $votoMio   = floatval($m['voto_mio']  ?? 0);

    if ($haOfferta && $haCercata && $votoLoro >= 8 && $votoMio >= 8) {
        $m['tipo_match'] = 'TOP';
        $m['score']      = 300 + $m['score_bonus'];
    } elseif ($haOfferta && $haCercata) {
        $m['tipo_match'] = 'PERFECT';
        $m['score']      = 200 + $m['score_bonus'];
    } else {
        $m['tipo_match'] = 'MATCH';
        $m['score']      = 100 + $m['score_bonus'];
    }
    $matches[] = $m;
}

// Ordina per score
usort($matches, function($a, $b) { return $b['score'] - $a['score']; });

// Filtri
if ($filtroMateria !== '') {
    $matches = array_filter($matches, function($m) use ($filtroMateria) {
        return stripos($m['materia_offerta'] ?? '', $filtroMateria) !== false
            || stripos($m['materia_cercata'] ?? '', $filtroMateria) !== false;
    });
}
if ($filtroTipo !== '' && in_array($filtroTipo, ['TOP', 'PERFECT', 'MATCH'])) {
    $matches = array_filter($matches, function($m) use ($filtroTipo) {
        return $m['tipo_match'] === $filtroTipo;
    });
}

// Materie per il filtro select
$materieDisp = array_unique(array_merge(
    array_column(array_values($utentiMap), 'materia_offerta'),
    array_column(array_values($utentiMap), 'materia_cercata')
));
$materieDisp = array_filter($materieDisp);
sort($materieDisp);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap — Match</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/e9e5938e26.js" crossorigin="anonymous"></script>
</head>
<body>
<?php require_once 'navbar.php'; ?>

<div class="page-content">
    <h1 style="margin-bottom:6px;">I tuoi Match</h1>
    <p style="color:#555;margin-bottom:20px;">Studenti con cui fare uno SkillSwap, ordinati per compatibilità.</p>

    <form method="GET" action="">
        <div class="filter-bar">
            <label style="margin:0;">Materia:</label>
            <select name="materia" onchange="this.form.submit()">
                <option value="">— Tutte —</option>
                <?php foreach ($materieDisp as $m): ?>
                    <option value="<?php echo htmlspecialchars($m); ?>"
                        <?php echo ($filtroMateria === $m) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(ucfirst($m)); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label style="margin:0;">Tipo:</label>
            <select name="tipo" onchange="this.form.submit()">
                <option value="">— Tutti —</option>
                <option value="TOP"     <?php echo $filtroTipo === 'TOP'     ? 'selected' : ''; ?>>Top Match</option>
                <option value="PERFECT" <?php echo $filtroTipo === 'PERFECT' ? 'selected' : ''; ?>>Perfect Match</option>
                <option value="MATCH"   <?php echo $filtroTipo === 'MATCH'   ? 'selected' : ''; ?>>Match</option>
            </select>

            <?php if ($filtroMateria || $filtroTipo): ?>
                <a href="match.php" class="btn btn-outline btn-sm">Rimuovi filtri</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if (empty($matches)): ?>
        <div class="alert alert-info">
            Nessun match trovato. Aggiungi i tuoi voti nel profilo per trovare compagni di studio!
        </div>
    <?php else: ?>
        <p style="font-size:14px;color:#555;margin-bottom:12px;"><?php echo count($matches); ?> match trovati</p>
        <div class="match-container">
            <?php foreach ($matches as $match):
                $tipo = $match['tipo_match'];
                if ($tipo === 'TOP') {
                    $badgeClass = 'top';
                    $badgeLabel = '<i class="fa-solid fa-star"></i> Top Match';
                } elseif ($tipo === 'PERFECT') {
                    $badgeClass = 'perfect';
                    $badgeLabel = '<i class="fa-solid fa-rotate"></i> Perfect Match';
                } else {
                    $badgeClass = 'normal';
                    $badgeLabel = '<i class="fa-solid fa-handshake"></i> Match';
                }

                $matO = ucfirst($match['materia_offerta'] ?? '');
                $matC = ucfirst($match['materia_cercata']  ?? '');

                // Recupera argomenti
                $argLoro = '';
                $argMio  = '';

                if ($matO) {
                    $st = $conn->prepare(
                        "SELECT argomento FROM voti
                         WHERE idUtente = :id AND materia_norm = :mat AND tipo = 'OFFRO' LIMIT 1"
                    );
                    $st->execute([':id' => $match['idUtente'], ':mat' => strtolower($matO)]);
                    $argLoro = $st->fetchColumn() ?: '';
                }
                if ($matC) {
                    $st2 = $conn->prepare(
                        "SELECT argomento FROM voti
                         WHERE idUtente = :id AND materia_norm = :mat AND tipo = 'OFFRO' LIMIT 1"
                    );
                    $st2->execute([':id' => $myId, ':mat' => strtolower($matC)]);
                    $argMio = $st2->fetchColumn() ?: '';
                }

                // Messaggio precompilato
                $msg = '';
                if ($matO && $matC) {
                    $dL = $argLoro ? "$matO ($argLoro)" : $matO;
                    $dM = $argMio  ? "$matC ($argMio)"  : $matC;
                    $msg = urlencode("Ciao! Ti propongo uno SkillSwap: io ti insegno $dM e tu mi insegni $dL. Che ne dici?");
                } elseif ($matO) {
                    $dL  = $argLoro ? "$matO ($argLoro)" : $matO;
                    $msg = urlencode("Ciao! Ho visto che puoi aiutarmi con $dL. Possiamo organizzarci?");
                } elseif ($matC) {
                    $dM  = $argMio ? "$matC ($argMio)" : $matC;
                    $msg = urlencode("Ciao! Posso aiutarti con $dM. Ti interessa?");
                }
            ?>
                <div class="match-card">
                    <span class="match-badge <?php echo $badgeClass; ?>"><?php echo $badgeLabel; ?></span>
                    <h3><?php echo htmlspecialchars($match['username']); ?></h3>
                    <p><strong>Istituto:</strong> <?php echo htmlspecialchars($match['nomeIstituto'] ?? '—'); ?></p>
                    <p><strong>Classe:</strong> <?php echo htmlspecialchars($match['classe'] ?? '—'); ?>^</p>

                    <?php if ($matO): ?>
                        <p><strong>Ti insegna:</strong><br>
                            <?php echo htmlspecialchars($matO); ?>
                            <?php if ($argLoro): ?>
                                <span style="color:#555;font-size:13px;">— <?php echo htmlspecialchars($argLoro); ?></span>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($matC): ?>
                        <p><strong>Vuole imparare:</strong><br>
                            <?php echo htmlspecialchars($matC); ?>
                            <?php if ($argMio): ?>
                                <span style="color:#555;font-size:13px;">— <?php echo htmlspecialchars($argMio); ?></span>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>

                    <div style="margin-top:12px;">
                        <a href="../userpages/chat.php?con=<?php echo intval($match['idUtente']); ?>&msg=<?php echo $msg; ?>"
                           class="btn btn-sm">
                            <i class="fa-regular fa-comment"></i> Contatta
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>

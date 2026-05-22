<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "include/dbHandler.php";
$conn = DBHandler::getPDO();

date_default_timezone_set('Europe/Rome');
$oggi = date("Y-m-d");

// Stress: voto dell'utente oggi
$voto_utente = null;
if (isset($_SESSION['idUtente'])) {
    $sv = $conn->prepare("SELECT livello, data_voto FROM stress_log WHERE idUtente = :id AND DATE(data_voto) = :oggi");
    $sv->execute([':id' => $_SESSION['idUtente'], ':oggi' => $oggi]);
    $voto_utente = $sv->fetch();
}

// Media stress community
$sm = $conn->prepare("SELECT AVG(livello) AS media, COUNT(*) AS n FROM stress_log WHERE DATE(data_voto) = CURRENT_DATE");
$sm->execute();
$dato       = $sm->fetch();
$media      = ($dato && $dato['media']) ? round($dato['media'], 1) : 0;
$percentuale = $media * 10;
$colore      = ($media > 7) ? "#e05252" : (($media > 4) ? "#f0a500" : "#4caf50");

// Match preview homepage (solo per loggati — max 8, solo TOP e PERFECT)
$matchPreview = [];
if (isset($_SESSION['idUtente'])) {
    $mp = $conn->prepare("CALL get_matches_scored(:myId)");
    $mp->execute([':myId' => $_SESSION['idUtente']]);
    $righePreview = $mp->fetchAll(PDO::FETCH_ASSOC);
    $mp->closeCursor();

    // Raggruppa per utente (stessa logica di match.php)
    $utentiMapPreview = [];
    foreach ($righePreview as $r) {
        $id = $r['idUtente'];
        if (!isset($utentiMapPreview[$id])) {
            $utentiMapPreview[$id] = [
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
        if (!empty($r['materia_offerta'])) {
            if (is_null($utentiMapPreview[$id]['voto_loro']) || $r['voto_loro'] > $utentiMapPreview[$id]['voto_loro']) {
                $utentiMapPreview[$id]['materia_offerta'] = $r['materia_offerta'];
                $utentiMapPreview[$id]['voto_loro']       = $r['voto_loro'];
            }
        }
        if (!empty($r['materia_cercata'])) {
            if (is_null($utentiMapPreview[$id]['voto_mio']) || $r['voto_mio'] > $utentiMapPreview[$id]['voto_mio']) {
                $utentiMapPreview[$id]['materia_cercata'] = $r['materia_cercata'];
                $utentiMapPreview[$id]['voto_mio']        = $r['voto_mio'];
            }
        }
    }

    foreach ($utentiMapPreview as $m) {
        $haOfferta = !is_null($m['materia_offerta']);
        $haCercata = !is_null($m['materia_cercata']);
        $votoLoro  = floatval($m['voto_loro'] ?? 0);
        $votoMio   = floatval($m['voto_mio']  ?? 0);

        if ($haOfferta && $haCercata && $votoLoro >= 8 && $votoMio >= 8) {
            $m['tipo_match'] = 'TOP';
        } elseif ($haOfferta && $haCercata) {
            $m['tipo_match'] = 'PERFECT';
        } else {
            continue; // Homepage mostra solo TOP e PERFECT
        }
        $matchPreview[] = $m;
        if (count($matchPreview) >= 8) break;
    }
}
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
<?php require_once 'include/navbar.php'; ?>

<!-- Hero -->
<div class="hero">
    <h1>Skill<span>Swap</span></h1>
    <p>Smetti di studiare da solo. Insegna quello che sai, impara quello che non capisci.</p>
    <?php if (!isset($_SESSION['idUtente'])): ?>
    <div class="hero-buttons">
        <a href="include/loginForm.php" class="btn">Accedi</a>
        <a href="include/signupForm.php" class="btn btn-outline">Registrati</a>
    </div>
    <?php endif; ?>
</div>

<div class="page-content">

    <!-- Sezione match preview -->
    <div class="section-title">
        <span>I tuoi Match migliori</span>
        <?php if (isset($_SESSION['idUtente'])): ?>
        <a href="include/match.php">Mostra tutti →</a>
        <?php endif; ?>
    </div>

    <?php if (!isset($_SESSION['idUtente'])): ?>
        <div class="login-prompt">
            <p>Accedi per vedere i match disponibili con altri studenti.</p>
            <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                <a href="include/loginForm.php" class="btn">Accedi</a>
                <a href="include/signupForm.php" class="btn btn-outline">Registrati</a>
            </div>
        </div>
    <?php elseif (empty($matchPreview)): ?>
        <div class="login-prompt">
            <p>Nessun match trovato. Aggiungi i tuoi voti nel profilo per trovare compagni di studio!</p>
            <a href="userpages/profile.php" class="btn">Vai al profilo</a>
        </div>
    <?php else: ?>
        <div class="match-container" style="margin-bottom:8px;">
            <?php foreach ($matchPreview as $match):
                $tipo = $match['tipo_match'] ?? 'MATCH';
                if ($tipo === 'TOP') { $bc = 'top'; $bl = '<i class="fa-solid fa-star"></i> Top Match'; }
                else { $bc = 'perfect'; $bl = '<i class="fa-solid fa-rotate"></i> Perfect Match'; }

                $matO = ucfirst($match['materia_offerta'] ?? '');
                $matC = ucfirst($match['materia_cercata']  ?? '');
                $msg  = '';
                // Recupera argomenti dai voti
                $stmtArg = $conn->prepare(
                    "SELECT argomento FROM voti WHERE idUtente = :id AND materia_norm = :mat AND tipo = 'OFFRO' LIMIT 1"
                );
                $stmtArg->execute([':id' => $match['idUtente'], ':mat' => strtolower($matO)]);
                $argLoro = $stmtArg->fetchColumn() ?: '';

                $stmtArgMio = $conn->prepare(
                    "SELECT argomento FROM voti WHERE idUtente = :id AND materia_norm = :mat AND tipo = 'OFFRO' LIMIT 1"
                );
                $stmtArgMio->execute([':id' => $_SESSION['idUtente'], ':mat' => strtolower($matC)]);
                $argMio = $stmtArgMio->fetchColumn() ?: '';

                if ($matO && $matC) {
                    $dettaglioLoro = $argLoro ? "$matO ($argLoro)" : $matO;
                    $dettaglioMio  = $argMio  ? "$matC ($argMio)"  : $matC;
                    $username = htmlspecialchars($match['username']);
                    $msg = urlencode("Ciao $username! Ti propongo uno SkillSwap: io ti insegno $dettaglioMio e tu mi insegni $dettaglioLoro. Che ne dici?");
                }
            ?>
                <div class="match-card">
                    <span class="match-badge <?php echo $bc; ?>"><?php echo $bl; ?></span>
                    <h3><?php echo htmlspecialchars($match['username']); ?></h3>
                    <p><strong>Ti insegna:</strong> <br> <?php echo htmlspecialchars($dettaglioLoro); ?></p>
                    <?php if ($matC): ?>
                    <p><strong>Vuole imparare:</strong> <br> <?php echo htmlspecialchars($dettaglioMio); ?></p>
                    <?php endif; ?>
                    <div style="margin-top:12px;">
                        <a href="userpages/chat.php?con=<?php echo intval($match['idUtente']); ?>&msg=<?php echo $msg; ?>"
                           class="btn btn-sm">
                            <i class="fa-regular fa-comment"></i> Contatta
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <p style="text-align:center;margin-bottom:30px;">
            <a href="include/match.php" class="btn btn-outline">Mostra tutti i match</a>
        </p>
    <?php endif; ?>

    <hr>

    <!-- Card funzionalità -->
    <div class="section-title"><span>Esplora SkillSwap</span></div>
    <div class="features-grid">
        <a href="include/match.php" class="feature-card">
            <span class="feature-icon"><i class="fa-solid fa-comment-nodes"></i></span>
            Trova un Match
        </a>
        <a href="userpages/lista_chat.php" class="feature-card">
            <span class="feature-icon"><i class="fa-regular fa-comments"></i></span>
            Messaggi
        </a>
        <a href="userpages/domanda.php" class="feature-card">
            <span class="feature-icon"><i class="fa-solid fa-circle-question"></i></span>
            Domanda del Giorno
        </a>
        <a href="userpages/profile.php" class="feature-card">
            <span class="feature-icon"><i class="fa-solid fa-user"></i></span>
            Il mio Profilo
        </a>
    </div>

    <hr>

    <!-- Stress bar -->
    <div class="section-title"><span>Stress della community</span></div>

    <div class="stress-input-box">
        <h3>Come ti senti oggi?</h3>
        <?php if (!isset($_SESSION['idUtente'])): ?>
            <p style="color:#555;">Accedi per condividere il tuo livello di stress.</p>
        <?php elseif ($voto_utente): ?>
            <div class="alert alert-success">
                Hai votato oggi: <b><?php echo $voto_utente['livello']; ?>/10</b>
            </div>
        <?php else: ?>
            <form action="include/salva_stress.php" method="POST">
                <label for="livello">Livello di stress: <b id="valoreStress">5</b></label>
                <input type="range" name="livello" id="livello" min="1" max="10" value="5"
                       oninput="document.getElementById('valoreStress').innerText = this.value">
                <button type="submit" class="btn">Invia</button>
            </form>
        <?php endif; ?>
        <div class="stress-indicator">
            Media community oggi: <b><?php echo $media; ?>/10</b>
            &nbsp;<small>(<?php echo $dato['n']; ?> vot<?php echo $dato['n'] == 1 ? 'o' : 'i'; ?>)</small>
        </div>
    </div>

    <div class="stress-section">
        <h4>Stress della community oggi</h4>
        <div class="stress-container">
            <div class="stress-bar" style="width:<?php echo $percentuale; ?>%;background-color:<?php echo $colore; ?>;">
                <?php if ($percentuale > 0) echo $percentuale . "%"; ?>
            </div>
        </div>
    </div>

</div>

<!-- Footer -->
<footer class="footer">
    <div class="footer-links">
        <a href="include/loginForm.php">Login</a>
        <a href="include/signupForm.php">Registrati</a>
        <a href="userpages/profile.php">Profilo</a>
        <a href="include/match.php">Match</a>
        <a href="pages/come-funziona.php">SkillSwap</a>
        <a href="pages/contattaci.php">Contattaci</a>
    </div>
    <p>© <?php echo date('Y'); ?> SkillSwap — Fatto dagli studenti, per gli studenti</p>
    <p style="font-size:12px;margin-top:6px;opacity:0.6;">Progetto finale di Informatica &middot; <?php echo date('Y')-1; ?>/<?php echo date('Y'); ?></p>
</footer>



</body>
</html>

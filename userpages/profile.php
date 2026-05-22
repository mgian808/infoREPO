<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['idUtente'])) {
    header('Location: ../include/loginForm.php');
    exit();
}

require_once '../include/dbHandler.php';
$conn = DBHandler::getPDO();
$id   = $_SESSION['idUtente'];

$stmt = $conn->prepare("SELECT * FROM utenti WHERE idUtente = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: ../include/logout.php');
    exit();
}

$stmtVoti = $conn->prepare("SELECT * FROM voti WHERE idUtente = :id ORDER BY idVoto DESC");
$stmtVoti->execute([':id' => $id]);
$voti = $stmtVoti->fetchAll();

$stmtMat = $conn->prepare("SELECT * FROM materie WHERE idUtente = :id ORDER BY materia ASC");
$stmtMat->execute([':id' => $id]);
$materie = $stmtMat->fetchAll();

$punti = intval($user['punti'] ?? 0);
if ($punti >= 100)    { $livello = 'Mentor'; }
elseif ($punti >= 51) { $livello = 'Tutor'; }
elseif ($punti >= 21) { $livello = 'Compagno'; }
else                  { $livello = 'Studente'; }

$msg = $_GET['msg'] ?? '';
$msgMap = [
    'voto_aggiunto'    => ['success', 'Voto aggiunto con successo!'],
    'voto_modificato'  => ['success', 'Voto modificato con successo!'],
    'voto_eliminato'   => ['success', 'Voto eliminato con successo!'],
    'materia_aggiunta' => ['success', 'Materia aggiunta con successo!'],
    'errore'           => ['error',   'Si e verificato un errore. Riprova.'],
];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap — Profilo</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/e9e5938e26.js" crossorigin="anonymous"></script>
</head>
<body>
<?php require_once '../include/navbar.php'; ?>

<div class="page-content">

    <?php if ($msg && isset($msgMap[$msg])): ?>
        <div class="alert alert-<?php echo $msgMap[$msg][0]; ?>">
            <?php echo $msgMap[$msg][1]; ?>
        </div>
    <?php endif; ?>

    <!-- Header profilo -->
    <div class="profile-header">
        <h1><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($user['nome'] . ' ' . $user['cognome']); ?></h1>
        <p>
            @<?php echo htmlspecialchars($user['username']); ?>
            &nbsp;·&nbsp;
            <?php echo htmlspecialchars($user['nomeIstituto'] ?? '—'); ?>
            &nbsp;·&nbsp;
            Classe <?php echo htmlspecialchars($user['classe'] ?? '—'); ?>^
        </p>
        <span class="livello-badge"><?php echo $livello; ?></span>
        <span class="punti-label"><?php echo $punti; ?> punti</span>
        <?php if (isset($user['ruolo']) && $user['ruolo'] === 'admin'): ?>
            <span class="livello-badge" style="background:#2e2e2e;margin-left:6px;">ADMIN</span>
        <?php endif; ?>
    </div>

    <!-- Dati personali -->
    <div class="myDiv" style="max-width:100%;margin-bottom:20px;">
        <h3 style="margin-bottom:14px;">Dati personali</h3>
        <p><b>Nome:</b> <?php echo htmlspecialchars($user['nome']); ?></p>
        <p><b>Cognome:</b> <?php echo htmlspecialchars($user['cognome']); ?></p>
        <p><b>Username:</b> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><b>Data di nascita:</b> <?php echo date('d/m/Y', strtotime($user['dataNascita'])); ?></p>
        <p><b>Provincia istituto:</b> <?php echo htmlspecialchars($user['provinciaIstituto']); ?></p>
        <p><b>Comune istituto:</b> <?php echo htmlspecialchars($user['comuneIstituto']); ?></p>
        <p><b>Istituto:</b> <?php echo htmlspecialchars($user['nomeIstituto']); ?></p>
        <p><b>Classe:</b> <?php echo htmlspecialchars($user['classe']); ?>^</p>
        <p><b>Telefono:</b> <?php echo htmlspecialchars($user['telefono']); ?></p>
        <p><b>Email:</b> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><b>Password:</b> ******** <a href="../include/editPassword.php" style="font-size:13px;">(Modifica)</a></p>
    </div>

    <hr>

    <!-- Voti -->
    <h3 style="margin-bottom:12px;">I miei Voti</h3>
    <?php if (empty($voti)): ?>
        <p style="color:#555;margin-bottom:16px;">Nessun voto inserito ancora.</p>
    <?php else: ?>
        <div style="overflow-x:auto;margin-bottom:20px;">
            <table class="voti-table">
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th>Argomento</th>
                        <th>Voto</th>
                        <th>Tipo</th>
                        <th>Data</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($voti as $v): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(ucfirst($v['materia'])); ?></td>
                            <td><?php echo htmlspecialchars($v['argomento']); ?></td>
                            <td><b><?php echo htmlspecialchars($v['voto']); ?></b></td>
                            <td>
                                <?php
                                $t   = strtolower($v['tipo'] ?? 'neutro');
                                $lab = strtoupper($t);
                                echo "<span class='tipo-badge $t'>$lab</span>";
                                ?>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($v['data_inserimento'])); ?></td>
                            <td style="white-space:nowrap;">
                                <a href="#"
                                   onclick="apriModifica(
                                       <?php echo $v['idVoto']; ?>,
                                       '<?php echo htmlspecialchars($v['materia'],   ENT_QUOTES); ?>',
                                       '<?php echo htmlspecialchars($v['argomento'], ENT_QUOTES); ?>',
                                       '<?php echo $v['voto']; ?>',
                                       '<?php echo $v['tipo']; ?>'
                                   )"
                                   style="font-size:13px;color:#774caf;margin-right:10px;">
                                    <i class="fa-solid fa-pen-to-square"></i> Modifica
                                </a>
                                <a href="../include/deleteGrade.php?id=<?php echo $v['idVoto']; ?>"
                                   onclick="return confirm('Sei sicuro di voler eliminare questo voto?')"
                                   style="font-size:13px;color:#cc0000;">
                                    <i class="fa-solid fa-trash"></i> Elimina
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Form aggiungi voto -->
    <div class="myDiv" style="max-width:100%;margin-bottom:20px;">
        <h3>Aggiungi Voto</h3>
        <form action="../include/addGrade.php" method="POST">
            <label for="materia">Materia</label>
            <select id="materia" name="materia" required>
                <option value="" disabled selected>Seleziona una materia...</option>
                <?php foreach ($materie as $m): ?>
                    <option value="<?php echo htmlspecialchars($m['materia']); ?>">
                        <?php echo htmlspecialchars($m['materia']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="argomento">Argomento</label>
            <input type="text" id="argomento" name="argomento"
                   placeholder="Es: Integrali, Canto V Inferno..." required>

            <label for="voto">Voto</label>
            <input type="number" id="voto" name="voto" min="1" max="10" step="0.25" required>

            <label for="data">Data</label>
            <input type="date" id="data" name="data" required>

            <label for="tipo">Cosa vuoi fare?</label>
            <select id="tipo" name="tipo" required>
                <option value="OFFRO">Posso aiutare (OFFRO)</option>
                <option value="CERCO">Ho bisogno di aiuto (CERCO)</option>
                <option value="NEUTRO">Solo per archivio (NEUTRO)</option>
            </select>

            <input type="submit" value="Aggiungi Voto">
        </form>
    </div>

    <hr>

    <!-- Materie -->
    <h3 style="margin-bottom:12px;">Le mie Materie</h3>
    <?php if (empty($materie)): ?>
        <p style="color:#555;margin-bottom:16px;">Nessuna materia aggiunta.</p>
    <?php else: ?>
        <ul style="margin-bottom:16px;padding-left:20px;">
            <?php foreach ($materie as $m): ?>
                <li style="margin-bottom:6px;">
                    <b><?php echo htmlspecialchars(ucfirst($m['materia'])); ?></b>
                    <?php if ($m['descrizione']): ?>
                        — <span style="color:#555;font-size:13px;">
                            <?php echo htmlspecialchars($m['descrizione']); ?>
                          </span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Form aggiungi materia -->
    <div class="myDiv" style="max-width:100%;margin-bottom:20px;">
        <h3>Aggiungi Materia</h3>
        <form action="../include/addSubject.php" method="POST">
            <label for="mat_nome">Materia</label>
            <input type="text" id="mat_nome" name="materia" required>

            <label for="descrizione">Descrizione</label>
            <textarea name="descrizione" rows="3"></textarea>

            <input type="submit" value="Aggiungi Materia">
        </form>
    </div>

    <hr>
    <a href="../include/logout.php" class="AmyTitle">
        <i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out
    </a>

</div>

<!-- Modale modifica voto -->
<div id="modale-modifica" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;
     background:rgba(0,0,0,0.5);z-index:200;overflow-y:auto;">
    <div style="background:#fff;border-radius:10px;padding:24px;max-width:420px;
                margin:80px auto;position:relative;">
        <h3 style="margin-bottom:16px;">Modifica Voto</h3>
        <form action="../include/updateGrade.php" method="POST">
            <input type="hidden" name="idVoto" id="mod-id">

            <label>Materia</label>
            <input type="text" name="materia" id="mod-materia" required>

            <label>Argomento</label>
            <input type="text" name="argomento" id="mod-argomento" required>

            <label>Voto</label>
            <input type="number" name="voto" id="mod-voto" min="1" max="10" step="0.25" required>

            <label>Tipo</label>
            <select name="tipo" id="mod-tipo">
                <option value="OFFRO">Posso aiutare (OFFRO)</option>
                <option value="CERCO">Ho bisogno di aiuto (CERCO)</option>
                <option value="NEUTRO">Solo per archivio (NEUTRO)</option>
            </select>

            <div style="display:flex;gap:10px;margin-top:16px;">
                <button type="submit" class="btn">Salva</button>
                <button type="button" onclick="chiudiModifica()"
                        style="padding:10px 20px;border:1.5px solid #2e2e2e;border-radius:10px;
                               background:#fff;font-family:inherit;font-weight:700;
                               font-size:15px;cursor:pointer;">
                    Annulla
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function apriModifica(id, materia, argomento, voto, tipo) {
    document.getElementById('mod-id').value        = id;
    document.getElementById('mod-materia').value   = materia;
    document.getElementById('mod-argomento').value = argomento;
    document.getElementById('mod-voto').value      = voto;
    document.getElementById('mod-tipo').value      = tipo;
    document.getElementById('modale-modifica').style.display = 'block';
}
function chiudiModifica() {
    document.getElementById('modale-modifica').style.display = 'none';
}
// Chiudi il modale cliccando fuori
document.getElementById('modale-modifica').addEventListener('click', function(e) {
    if (e.target === this) chiudiModifica();
});
</script>

</body>
</html>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap — Come funziona</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/e9e5938e26.js" crossorigin="anonymous"></script>
</head>
<body>
<?php require_once '../include/navbar.php'; ?>

<div class="page-content-narrow">

    <h1 style="margin-bottom:6px; color:#2e2e2e;">Come funziona <span style="color:#774caf;">SkillSwap</span></h1>
    <p style="color:#888;margin-bottom:30px;">Tutto quello che devi sapere sulla piattaforma.</p>

    <div class="myDiv" style="max-width:100%;margin-bottom:20px;">
        <h3 style="color:#774caf;">Il Matching</h3>
        <p style="margin-top:10px;">SkillSwap analizza i voti che hai inserito nel profilo e cerca altri studenti con cui fare uno scambio utile. Per ogni voto puoi specificare se sei bravo in quell'argomento (<strong>OFFRO</strong>), se hai difficoltà (<strong>CERCO</strong>), o se vuoi tenerlo solo per archivio (<strong>NEUTRO</strong>).</p>
        <p style="margin-top:10px;">Il sistema trova studenti con cui c'è una corrispondenza in almeno una direzione: l'altro offre qualcosa che tu cerchi, oppure tu offri qualcosa che l'altro cerca.</p>
    </div>

    <div class="myDiv" style="max-width:100%;margin-bottom:20px;">
        <h3 style="color:#774caf;">I tipi di Match</h3>
        <p style="margin-top:10px;">Non tutti i match sono uguali. Il sistema li classifica in tre livelli:</p>
        <ul style="margin-top:10px;padding-left:20px;line-height:2;">
            <li><strong>Top Match</strong> — scambio reciproco in cui entrambi avete voto 8 o superiore nella materia che offrite. Lo scambio è di alta qualità per entrambi.</li>
            <li><strong>Perfect Match</strong> — scambio reciproco: tu offri qualcosa che l'altro cerca e l'altro offre qualcosa che tu cerchi, senza vincoli sul voto.</li>
            <li><strong>Match</strong> — corrispondenza in una sola direzione: l'altro può aiutarti oppure tu puoi aiutare lui, ma non entrambi.</li>
        </ul>
        <p style="margin-top:10px;">I match vengono ordinati per punteggio. A parità di tipo, vengono favoriti gli studenti dello stesso istituto (+50 punti) o della stessa classe (+20 punti).</p>
    </div>

    <div class="myDiv" style="max-width:100%;margin-bottom:20px;">
        <h3 style="color:#774caf;">Il Punteggio</h3>
        <p style="margin-top:10px;">Ogni utente accumula punti partecipando attivamente alla piattaforma:</p>
        <ul style="margin-top:10px;padding-left:20px;line-height:2;">
            <li>+10 punti alla registrazione</li>
            <li>+2 punti per ogni voto OFFRO o CERCO inserito (fino a 50 punti da voti)</li>
            <li>+5 punti quando proponi uno SkillSwap a un nuovo studente</li>
        </ul>
        <p style="margin-top:10px;">I punti determinano il tuo livello, visibile nel profilo:</p>
        <ul style="margin-top:10px;padding-left:20px;line-height:2;">
            <li>0–20 punti → <strong>Studente</strong></li>
            <li>21–50 punti → <strong>Compagno</strong></li>
            <li>51–100 punti → <strong>Tutor</strong></li>
            <li>100+ punti → <strong>Mentor</strong></li>
        </ul>
    </div>

    <div class="myDiv" style="max-width:100%;margin-bottom:20px;">
        <h3 style="color:#774caf;">La Stress Bar</h3>
        <p style="margin-top:10px;">Ogni giorno puoi votare il tuo livello di stress scolastico da 1 a 10. La barra nella homepage mostra la media di tutti gli studenti registrati per la giornata corrente. Puoi votare una sola volta al giorno.</p>
    </div>

    <div class="myDiv" style="max-width:100%;margin-bottom:20px;">
        <h3 style="color:#774caf;">La Domanda del Giorno</h3>
        <p style="margin-top:10px;">Ogni giorno viene proposta una domanda diversa a tutti gli studenti. Dopo aver risposto puoi vedere le risposte degli altri in percentuale. Le domande sono salvate nel database e ruotano automaticamente ogni giorno.</p>
    </div>

    <div class="myDiv" style="max-width:100%;margin-bottom:20px;">
        <h3 style="color:#774caf;">La Chat</h3>
        <p style="margin-top:10px;">Quando trovi un match interessante puoi contattare lo studente direttamente tramite la chat integrata. Se premi "Contatta" da un match, la chat si apre con un messaggio precompilato che propone lo SkillSwap con materia e argomento specifici — puoi modificarlo o inviarlo direttamente.</p>
    </div>

</div>

</body>
</html>

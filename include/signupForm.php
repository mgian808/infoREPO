<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap — Registrazione</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/e9e5938e26.js" crossorigin="anonymous"></script>
</head>
<body>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'navbar.php';
?>
<div class="page-content-narrow">
    <div class="myDiv" style="max-width:100%;">
        <h2>Crea un account</h2>
        <p style="font-size:14px;color:#888;margin-top:4px;margin-bottom:6px;">
            Hai già un account? <a href="loginForm.php" style="color:#774caf;font-weight:700;">Accedi</a>
        </p>
        <hr>
        <form action="signup.php" method="POST">

            <label for="first_name">Nome</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Cognome</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="dob">Data di Nascita</label>
            <input type="date" id="dob" name="dob" required>

            <label for="address">Indirizzo</label>
            <input type="text" id="address" name="address">

            <label for="province">Provincia dell'Istituto</label>
            <select name="province" id="province" required>
                <option value="" selected disabled>Seleziona provincia...</option>
                <option value="AGRIGENTO">Agrigento</option>
                <option value="ALESSANDRIA">Alessandria</option>
                <option value="ANCONA">Ancona</option>
                <option value="AOSTA">Aosta</option>
                <option value="AREZZO">Arezzo</option>
                <option value="ASCOLI PICENO">Ascoli Piceno</option>
                <option value="ASTI">Asti</option>
                <option value="AVELLINO">Avellino</option>
                <option value="BARI">Bari</option>
                <option value="BARLETTA-ANDRIA-TRANI">Barletta-Andria-Trani</option>
                <option value="BELLUNO">Belluno</option>
                <option value="BENEVENTO">Benevento</option>
                <option value="BERGAMO">Bergamo</option>
                <option value="BIELLA">Biella</option>
                <option value="BOLOGNA">Bologna</option>
                <option value="BOLZANO">Bolzano</option>
                <option value="BRESCIA">Brescia</option>
                <option value="BRINDISI">Brindisi</option>
                <option value="CAGLIARI">Cagliari</option>
                <option value="CALTANISSETTA">Caltanissetta</option>
                <option value="CAMPOBASSO">Campobasso</option>
                <option value="CASERTA">Caserta</option>
                <option value="CATANIA">Catania</option>
                <option value="CATANZARO">Catanzaro</option>
                <option value="CHIETI">Chieti</option>
                <option value="COMO">Como</option>
                <option value="COSENZA">Cosenza</option>
                <option value="CREMONA">Cremona</option>
                <option value="CROTONE">Crotone</option>
                <option value="CUNEO">Cuneo</option>
                <option value="ENNA">Enna</option>
                <option value="FERMO">Fermo</option>
                <option value="FERRARA">Ferrara</option>
                <option value="FIRENZE">Firenze</option>
                <option value="FOGGIA">Foggia</option>
                <option value="FORLI'-CESENA">Forli-Cesena</option>
                <option value="FROSINONE">Frosinone</option>
                <option value="GENOVA">Genova</option>
                <option value="GORIZIA">Gorizia</option>
                <option value="GROSSETO">Grosseto</option>
                <option value="IMPERIA">Imperia</option>
                <option value="ISERNIA">Isernia</option>
                <option value="LA SPEZIA">La Spezia</option>
                <option value="L'AQUILA">L'Aquila</option>
                <option value="LATINA">Latina</option>
                <option value="LECCE">Lecce</option>
                <option value="LECCO">Lecco</option>
                <option value="LIVORNO">Livorno</option>
                <option value="LODI">Lodi</option>
                <option value="LUCCA">Lucca</option>
                <option value="MACERATA">Macerata</option>
                <option value="MANTOVA">Mantova</option>
                <option value="MASSA-CARRARA">Massa-Carrara</option>
                <option value="MATERA">Matera</option>
                <option value="MESSINA">Messina</option>
                <option value="MILANO">Milano</option>
                <option value="MODENA">Modena</option>
                <option value="MONZA E DELLA BRIANZA">Monza e della Brianza</option>
                <option value="NAPOLI">Napoli</option>
                <option value="NOVARA">Novara</option>
                <option value="NUORO">Nuoro</option>
                <option value="ORISTANO">Oristano</option>
                <option value="PADOVA">Padova</option>
                <option value="PALERMO">Palermo</option>
                <option value="PARMA">Parma</option>
                <option value="PAVIA">Pavia</option>
                <option value="PERUGIA">Perugia</option>
                <option value="PESARO E URBINO">Pesaro e Urbino</option>
                <option value="PESCARA">Pescara</option>
                <option value="PIACENZA">Piacenza</option>
                <option value="PISA">Pisa</option>
                <option value="PISTOIA">Pistoia</option>
                <option value="PORDENONE">Pordenone</option>
                <option value="POTENZA">Potenza</option>
                <option value="PRATO">Prato</option>
                <option value="RAGUSA">Ragusa</option>
                <option value="RAVENNA">Ravenna</option>
                <option value="REGGIO CALABRIA">Reggio Calabria</option>
                <option value="REGGIO EMILIA">Reggio Emilia</option>
                <option value="RIETI">Rieti</option>
                <option value="RIMINI">Rimini</option>
                <option value="ROMA">Roma</option>
                <option value="ROVIGO">Rovigo</option>
                <option value="SALERNO">Salerno</option>
                <option value="SASSARI">Sassari</option>
                <option value="SAVONA">Savona</option>
                <option value="SIENA">Siena</option>
                <option value="SIRACUSA">Siracusa</option>
                <option value="SONDRIO">Sondrio</option>
                <option value="SUD SARDEGNA">Sud Sardegna</option>
                <option value="TARANTO">Taranto</option>
                <option value="TERAMO">Teramo</option>
                <option value="TERNI">Terni</option>
                <option value="TORINO">Torino</option>
                <option value="TRAPANI">Trapani</option>
                <option value="TRENTO">Trento</option>
                <option value="TREVISO">Treviso</option>
                <option value="TRIESTE">Trieste</option>
                <option value="UDINE">Udine</option>
                <option value="VARESE">Varese</option>
                <option value="VENEZIA">Venezia</option>
                <option value="VERBANO-CUSIO-OSSOLA">Verbano-Cusio-Ossola</option>
                <option value="VERCELLI">Vercelli</option>
                <option value="VERONA">Verona</option>
                <option value="VIBO VALENTIA">Vibo Valentia</option>
                <option value="VICENZA">Vicenza</option>
                <option value="VITERBO">Viterbo</option>
            </select>

            <label for="comune">Comune dell'Istituto</label>
            <select id="comune" name="comune" disabled>
                <option value="">Seleziona prima una provincia</option>
            </select>

            <label for="listaScuole">Nome Istituto</label>
            <select id="listaScuole" name="school" disabled>
                <option value="">Seleziona prima un comune</option>
            </select>

            <label for="classe">Classe</label>
            <select id="classe" name="classe" required>
                <option value="" selected disabled>Seleziona...</option>
                <option value="1">1^</option>
                <option value="2">2^</option>
                <option value="3">3^</option>
                <option value="4">4^</option>
                <option value="5">5^</option>
            </select>

            <label for="phone">Numero di Telefono</label>
            <input type="tel" id="phone" name="phone">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autocomplete="email">

            <label for="psw">Password <span style="font-weight:400;font-size:12px;color:#888;">(minimo 8 caratteri)</span></label>
            <input type="password" id="psw" name="psw" required autocomplete="new-password">

            <input type="submit" value="Registrati">
        </form>
    </div>
</div>

<script type="module">
    import { caricaComuni, caricaScuolePerComune } from '../js/scuole.js?v=1.3';
    document.getElementById('province').addEventListener('change', caricaComuni);
    document.getElementById('comune').addEventListener('change', caricaScuolePerComune);
</script>

</body>
</html>
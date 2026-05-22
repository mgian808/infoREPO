<?php
// navbar.php — inclusa in TUTTE le pagine
// Legge $_SESSION per capire se loggato e il ruolo
$paginaCorrente = basename($_SERVER['PHP_SELF']);
$profondita = substr_count(str_replace('\\', '/', $_SERVER['PHP_SELF']), '/') - 2;
$base = str_repeat('../', max(0, $profondita));
?>
<nav class="navbar">
    <a href="<?php echo $base; ?>index.php" class="navbar-logo">
        <img src="<?php echo $base; ?>images/logo_bozza.png" alt="SkillSwap">
    </a>

    <button class="navbar-toggle" onclick="toggleMenu()" aria-label="Menu">
        <i class="fa-solid fa-bars"></i>
    </button>

    <ul class="navbar-links" id="navbar-links">
        <?php if (isset($_SESSION['idUtente'])): ?>
            <li><a href="<?php echo $base; ?>include/match.php"
                <?php echo ($paginaCorrente === 'match.php') ? 'class="active"' : ''; ?>>
                <i class="fa-solid fa-comment-nodes"></i> Match
            </a></li>
            <li><a href="<?php echo $base; ?>userpages/lista_chat.php"
                <?php echo ($paginaCorrente === 'lista_chat.php') ? 'class="active"' : ''; ?>>
                <i class="fa-regular fa-comments"></i> Messaggi
            </a></li>
            <li><a href="<?php echo $base; ?>userpages/profile.php"
                <?php echo ($paginaCorrente === 'profile.php') ? 'class="active"' : ''; ?>>
                <i class="fa-solid fa-user"></i> Profilo
            </a></li>
            <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] === 'admin'): ?>
            <li><a href="<?php echo $base; ?>adminpages/stats.php"
                <?php echo ($paginaCorrente === 'stats.php') ? 'class="active"' : ''; ?>>
                <i class="fa-solid fa-chart-bar"></i> Admin
            </a></li>
            <?php endif; ?>
            <li><a href="<?php echo $base; ?>include/logout.php" class="link-esci">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Esci
            </a></li>
        <?php else: ?>
            <li><a href="<?php echo $base; ?>include/loginForm.php">
                <i class="fa-solid fa-arrow-right-to-bracket"></i> Login
            </a></li>
            <li><a href="<?php echo $base; ?>include/signupForm.php">
                <i class="fa-solid fa-user-plus"></i> Registrati
            </a></li>
        <?php endif; ?>
    </ul>
</nav>

<script>
function toggleMenu() {
    var links = document.getElementById('navbar-links');
    links.classList.toggle('aperto');
}
</script>

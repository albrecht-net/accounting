<?php
if (__FILE__ != $_SERVER['SCRIPT_FILENAME']) {
    // Applikation Basisordner definieren
    $docRoot = $_SERVER['DOCUMENT_ROOT'];
    $curPath = __DIR__;
    if (substr($curPath, 0, strlen($docRoot)) == $docRoot) {
        $curPath = substr($curPath, strlen($docRoot));
    }
    $bf = substr($curPath, 0, -strlen('/core'));

    // Dateiname wenn eingebunden
    $bn = pathinfo($_SERVER['PHP_SELF'], PATHINFO_BASENAME);
    ?>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Buchhaltung</a>
        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-collapse collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item<?php echo ($bn == 'index.php' ? ' active' : ''); ?>">
                    <a class="nav-link" href="<?php echo $bf; ?>/index.php">Home <?php echo ($bn == 'index.php' ? '<span class="sr-only">(current)</span>' : ''); ?></a>
                </li>
                <li class="nav-item<?php echo ($bn == 'buchung.php' ? ' active' : ''); ?>">
                    <?php if (intval(json_decode($_COOKIE['standingOrder'], TRUE)['count']) > 0): ?>
                    <a class="nav-link" href="<?php echo $bf; ?>/buchung.php">Neue Buchung <span class="badge badge-warning"><?php echo intval(json_decode($_COOKIE['standingOrder'], TRUE)['count']); ?></span> <?php echo ($bn == 'buchung.php' ? '<span class="sr-only">(current)</span>' : ''); ?></a>
                    <?php else: ?>
                    <a class="nav-link" href="<?php echo $bf; ?>/buchung.php">Neue Buchung <?php echo ($bn == 'buchung.php' ? '<span class="sr-only">(current)</span>' : ''); ?></a>
                    <?php endif; ?>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Weitere erfassen
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="<?php echo $bf; ?>/standingOrder.php">Dauerauftrag</a>
                        <a class="dropdown-item disabled" href="#">Konto</a>
                        <a class="dropdown-item" href="<?php echo $bf; ?>/recipient.php">Empfänger</a>
                        <a class="dropdown-item" href="<?php echo $bf; ?>/classification.php">Klassifikation</a>
                    </div>
                </li>
                <li class="nav-item<?php echo ($bn == 'templates.php' ? ' active' : ''); ?>">
                    <a class="nav-link" href="<?php echo $bf; ?>/templates.php">Vorlagen <?php echo ($bn == 'templates.php' ? '<span class="sr-only">(current)</span>' : ''); ?></a>
                </li>
            </ul>
            <ul class="navbar-nav navbar-right">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item disabled" href="#">Mein Profil</a>
                        <a class="dropdown-item" href="<?php echo $bf; ?>/settings/account.php">Einstellungen</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo $bf; ?>/logout.php">Abmelden</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

<?php
} else {
    http_response_code(204);
}
?>
<?php
$title = "Vérification de l'installation de Xdebug";
echo "<h1>$title</h1>\n";

// Vérification de l'installation de Xdebug
if (extension_loaded('xdebug')) {
    echo "Xdebug est installé !\n";
    echo "Version : " . phpversion('xdebug') . "\n";
    $title = "Dian";
    echo "Mode : " . ini_get('xdebug.mode') . "\n";
} else {
    echo "Xdebug n'est pas installé.\n";
}
$_SESSION['title'] = $title;
// Affichage de la version de PHP
echo "Version de PHP : " . phpversion() . "\n";
// Affichage de la configuration PHP
echo "<h2>Configuration PHP</h2>\n";
echo "<pre>" . print_r(ini_get_all(), true) . "</pre>\n";
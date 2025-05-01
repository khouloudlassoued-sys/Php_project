<?php
// Générer les captures d'écran pour le README

// Vérifier si GD est disponible
if (!extension_loaded('gd')) {
    die("Extension GD non disponible. Impossible de générer les captures d'écran.");
}

echo "Génération des captures d'écran pour le README...\n";

// Capture 1: Page d'accueil
echo "Génération de screenshot1.png...\n";
$output = shell_exec('php assets/images/screenshot1.php > assets/images/screenshot1.png');
if (file_exists('assets/images/screenshot1.png')) {
    echo "Screenshot 1 généré avec succès!\n";
} else {
    echo "Erreur lors de la génération du screenshot 1.\n";
}

// Capture 2: Page de réservation
echo "Génération de screenshot2.png...\n";
$output = shell_exec('php assets/images/screenshot2.php > assets/images/screenshot2.png');
if (file_exists('assets/images/screenshot2.png')) {
    echo "Screenshot 2 généré avec succès!\n";
} else {
    echo "Erreur lors de la génération du screenshot 2.\n";
}

echo "Génération des captures d'écran terminée.\n";

// Supprimer les fichiers PHP temporaires
unlink('assets/images/screenshot1.php');
unlink('assets/images/screenshot2.php');

echo "Nettoyage des fichiers temporaires terminé.\n";
?> 
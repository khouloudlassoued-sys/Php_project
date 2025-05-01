<?php
// Fichier pour générer une capture d'écran de la page d'accueil
header('Content-Type: image/jpeg');
$im = @imagecreate(1200, 800)
    or die("Impossible de créer une image");
    
// Définir les couleurs
$background_color = imagecolorallocate($im, 3, 53, 128); // bleu foncé
$text_color = imagecolorallocate($im, 255, 255, 255);
$accent_color = imagecolorallocate($im, 245, 166, 35);  // orange

// Remplir l'arrière-plan
imagefill($im, 0, 0, $background_color);

// Ajouter un titre
$title = "Gestion Hôtel - Page d'Accueil";
imagestring($im, 5, 450, 100, $title, $text_color);

// Ajouter la représentation du menu
imagefilledrectangle($im, 0, 0, 1200, 70, imagecolorallocate($im, 0, 35, 80));
imagestring($im, 4, 100, 25, "Gestion Hotel", $text_color);
imagestring($im, 4, 900, 25, "Accueil", $accent_color);
imagestring($im, 4, 1000, 25, "Réservations", $text_color);
imagestring($im, 4, 1100, 25, "Connexion", $text_color);

// Ajouter une représentation du carrousel
imagefilledrectangle($im, 150, 200, 1050, 500, imagecolorallocate($im, 51, 51, 51));
imagestring($im, 5, 550, 340, "Carrousel de Chambres", $text_color);

// Ajouter des cartes de chambres
for ($i = 0; $i < 3; $i++) {
    $x = 150 + $i * 350;
    imagefilledrectangle($im, $x, 550, $x + 300, 750, imagecolorallocate($im, 255, 255, 255));
    imagefilledrectangle($im, $x, 550, $x + 300, 650, imagecolorallocate($im, 200, 200, 200));
    imagestring($im, 4, $x + 100, 670, "Chambre " . ($i+101), imagecolorallocate($im, 0, 0, 0));
    imagestring($im, 3, $x + 80, 700, $i==0 ? "Simple" : ($i==1 ? "Double" : "Suite"), imagecolorallocate($im, 100, 100, 100));
    imagestring($im, 3, $x + 80, 720, ($i+1)*100 . " EUR/nuit", $accent_color);
}

// Afficher l'image
imagejpeg($im);
imagedestroy($im);
?> 
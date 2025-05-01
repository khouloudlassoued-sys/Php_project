<?php
// Fichier pour générer une capture d'écran de la page de réservation
header('Content-Type: image/jpeg');
$im = @imagecreate(1200, 800)
    or die("Impossible de créer une image");
    
// Définir les couleurs
$background_color = imagecolorallocate($im, 245, 245, 245); // gris très clair
$text_color = imagecolorallocate($im, 0, 0, 0);
$primary_color = imagecolorallocate($im, 0, 53, 128);  // bleu foncé
$secondary_color = imagecolorallocate($im, 0, 113, 194); // bleu clair
$accent_color = imagecolorallocate($im, 245, 166, 35);  // orange
$white = imagecolorallocate($im, 255, 255, 255);

// Remplir l'arrière-plan
imagefill($im, 0, 0, $background_color);

// Ajouter un titre
imagefilledrectangle($im, 0, 0, 1200, 70, $primary_color);
imagestring($im, 4, 100, 25, "Gestion Hotel", $white);
imagestring($im, 4, 900, 25, "Accueil", $white);
imagestring($im, 4, 1000, 25, "Réservations", $white);
imagestring($im, 4, 1100, 25, "Déconnexion", $white);

// Ajouter une bannière
imagefilledrectangle($im, 0, 70, 1200, 170, $secondary_color);
imagestring($im, 5, 500, 110, "Réserver une Chambre", $white);

// Partie gauche: Détails de la chambre
imagefilledrectangle($im, 100, 200, 550, 700, $white);
imagefilledrectangle($im, 100, 200, 550, 350, imagecolorallocate($im, 200, 200, 200));
imagestring($im, 5, 210, 220, "Chambre Double #102", $primary_color);
imagestring($im, 3, 150, 370, "Description: Chambre double avec lit king size", $text_color);
imagestring($im, 3, 150, 400, "Capacité: 2 personnes", $text_color);
imagestring($im, 3, 150, 430, "Prix: 150.00 EUR/nuit", $text_color);
imagestring($im, 3, 150, 460, "Équipements: WiFi, TV, Salle de bain", $text_color);

// Circle price tag
imagefilledellipse(480, 230, 80, 80, $accent_color);
imagestring($im, 3, 455, 225, "150 €", $white);

// Partie droite: Formulaire de réservation
imagefilledrectangle($im, 650, 200, 1100, 700, $white);
imagestring($im, 5, 750, 220, "Formulaire de Réservation", $primary_color);

// Formulaire
imagestring($im, 3, 700, 270, "Date d'arrivée:", $text_color);
imagefilledrectangle($im, 700, 300, 1050, 340, imagecolorallocate($im, 240, 240, 240));

imagestring($im, 3, 700, 370, "Date de départ:", $text_color);
imagefilledrectangle($im, 700, 400, 1050, 440, imagecolorallocate($im, 240, 240, 240));

// Résumé
imagefilledrectangle($im, 700, 470, 1050, 550, imagecolorallocate($im, 217, 237, 247));
imagestring($im, 3, 720, 490, "Nombre de nuits: 3", $text_color);
imagestring($im, 3, 720, 520, "Prix total: 450.00 EUR", $text_color);

// Bouton de confirmation
imagefilledrectangle($im, 750, 600, 1000, 650, $secondary_color);
imagestring($im, 3, 780, 620, "Confirmer la réservation", $white);

// Afficher l'image
imagejpeg($im);
imagedestroy($im);
?> 
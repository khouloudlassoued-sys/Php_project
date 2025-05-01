<?php
// URLs des images à télécharger
$images = [
    'room-simple.jpg' => 'https://images.unsplash.com/photo-1566669437685-7724851aebd4?auto=format&fit=crop&w=800&q=80',
    'room-double.jpg' => 'https://images.unsplash.com/photo-1566669437685-7724851aebd4?auto=format&fit=crop&w=800&q=80',
    'room-suite.jpg' => 'https://images.unsplash.com/photo-1566669437685-7724851aebd4?auto=format&fit=crop&w=800&q=80'
];

// Créer le dossier images s'il n'existe pas
if (!file_exists('assets/images')) {
    mkdir('assets/images', 0777, true);
}

// Télécharger chaque image
foreach ($images as $filename => $url) {
    $filepath = 'assets/images/' . $filename;
    
    // Vérifier si l'image existe déjà
    if (!file_exists($filepath)) {
        $image_data = file_get_contents($url);
        if ($image_data !== false) {
            file_put_contents($filepath, $image_data);
            echo "Image $filename téléchargée avec succès.\n";
        } else {
            echo "Erreur lors du téléchargement de $filename.\n";
        }
    } else {
        echo "L'image $filename existe déjà.\n";
    }
}

echo "Téléchargement des images terminé.\n";
?> 
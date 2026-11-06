<?php
require_once 'config.php';

$upload_dir = __DIR__ . '/../assets/images/';

// Liste des images valides (noms corrects)
$valid_images = [
    'creme_hydratante.jpg',
    'serum_eclat.jpg',
    'fond_teint.jpg',
    'rouge_levres.jpg',
    'mascara.jpg',
    'gommage_visage.jpg',
    'demaquillant.jpg',
    'baume_levres.jpg',
    'contour_yeux.jpg'
];

echo "<h2>🧹 Nettoyage des images</h2>";

// Supprimer les fichiers avec double extension
$files = scandir($upload_dir);
foreach ($files as $file) {
    if ($file == '.' || $file == '..') continue;
    
    // Supprimer les fichiers avec double extension
    if (strpos($file, '.jpg.jpg') !== false || strpos($file, '.jpeg') !== false) {
        unlink($upload_dir . $file);
        echo "🗑️ Supprimé: $file<br>";
    }
}

// Créer des images par défaut pour les produits sans image
$products = $pdo->query("SELECT id, nom, image FROM produits")->fetchAll();

foreach ($products as $p) {
    $img_path = $upload_dir . $p['image'];
    
    // Si l'image n'existe pas, créer une image par défaut
    if (empty($p['image']) || !file_exists($img_path)) {
        $new_name = 'prod_' . $p['id'] . '.jpg';
        
        // Créer une image simple
        $img = imagecreatetruecolor(400, 400);
        $bg = imagecolorallocate($img, 232, 67, 147);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $bg);
        
        $text = mb_substr($p['nom'], 0, 20);
        imagestring($img, 5, 50, 190, $text, $white);
        imagejpeg($img, $upload_dir . $new_name, 80);
        imagedestroy($img);
        
        // Mettre à jour la base
        $stmt = $pdo->prepare("UPDATE produits SET image = ? WHERE id = ?");
        $stmt->execute([$new_name, $p['id']]);
        echo "✅ Image créée pour: {$p['nom']} → $new_name<br>";
    }
}

echo "<hr>";
echo "<a href='products.php' class='btn btn-em'>← Voir les produits</a>";
?>
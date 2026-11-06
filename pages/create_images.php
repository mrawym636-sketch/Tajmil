<?php
require_once '../php_files/config.php';

$images = [
    'creme_hydratante.jpg' => 'https://images.unsplash.com/photo-1570194065650-d99fb4d8a609?w=500',
    'serum_vitamine_c.jpg' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=500',
    'masque_argile.jpg' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=500',
    'fond_teint.jpg' => 'https://images.unsplash.com/photo-1631730250918-6b87f400b5c9?w=500',
    'rouge_a_levres.jpg' => 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?w=500',
    'mascara_volume.jpg' => 'https://images.unsplash.com/photo-1512496015851-a90c38f634e2?w=500',
    'gommage_visage.jpg' => 'https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?w=500',
    'contour_yeux.jpg' => 'https://images.unsplash.com/photo-1612817288484-6f916006741a?w=500',
    'brume_fixatrice.jpg' => 'https://images.unsplash.com/photo-1596462502278-27bfdc1e2695?w=500',
    'huile_demaquillante.jpg' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?w=500'
];

$dir = __DIR__ . '/../assets/images/';
if (!is_dir($dir)) mkdir($dir, 0777, true);

foreach ($images as $name => $url) {
    $data = @file_get_contents($url);
    if ($data) {
        file_put_contents($dir . $name, $data);
        echo "✅ " . $name . " téléchargée<br>";
    } else {
        echo "❌ " . $name . " échec<br>";
    }
}

echo "<hr><a href='products.php' style='background:#e84393; color:white; padding:10px 20px; border-radius:40px; text-decoration:none;'>Voir produits</a>";
?>
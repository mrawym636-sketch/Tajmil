<?php
$dir = __DIR__ . '/../assets/images/';
if (!is_dir($dir)) mkdir($dir, 0777, true);

$images = [
    'creme_hydratante.jpg' => [232, 67, 147],  // rose
    'serum_eclat.jpg' => [16, 185, 129],      // vert
    'fond_teint.jpg' => [245, 158, 11],       // orange
    'rouge_levres.jpg' => [239, 68, 68],      // rouge
    'mascara.jpg' => [59, 130, 246],          // bleu
    'gommage_visage.jpg' => [139, 92, 246],   // violet
    'demaquillant.jpg' => [6, 182, 212],      // cyan
    'baume_levres.jpg' => [249, 115, 22],     // orange clair
    'contour_yeux.jpg' => [168, 85, 247]      // pourpre
];

foreach ($images as $name => $rgb) {
    $file = $dir . $name;
    if (!file_exists($file)) {
        $img = imagecreate(300, 300);
        $bg = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
        $white = imagecolorallocate($img, 255, 255, 255);
        $text = str_replace('_', ' ', pathinfo($name, PATHINFO_FILENAME));
        imagestring($img, 5, 50, 140, $text, $white);
        imagejpeg($img, $file, 80);
        imagedestroy($img);
        echo "✅ Créé: $name<br>";
    }
}

echo "<br><a href='products.php'>← Voir les produits</a>";
?>
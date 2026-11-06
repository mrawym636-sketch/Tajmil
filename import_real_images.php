<?php
require_once 'config.php';

$upload_dir = __DIR__ . '/../assets/images/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

// Liste des vraies images (télécharge-les d'abord dans assets/images/)
$real_images = [
    'creme_hydratante.jpg' => 'https://images.unsplash.com/photo-1570194065650-d99fb4d8a609?w=400',
    'serum_eclat.jpg' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=400',
    'fond_teint.jpg' => 'https://images.unsplash.com/photo-1631730250918-6b87f400b5c9?w=400',
    'rouge_levres.jpg' => 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?w=400',
    'mascara.jpg' => 'https://images.unsplash.com/photo-1512496015851-a90c38f634e2?w=400',
    'gommage_visage.jpg' => 'https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?w=400',
    'demaquillant.jpg' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?w=400',
    'baume_levres.jpg' => 'https://images.unsplash.com/photo-1586495777600-05c794d1ff8b?w=400',
    'contour_yeux.jpg' => 'https://images.unsplash.com/photo-1612817288484-6f916006741a?w=400'
];

foreach ($real_images as $filename => $url) {
    $filepath = $upload_dir . $filename;
    
    // Télécharger l'image depuis Unsplash
    $image_data = @file_get_contents($url);
    if ($image_data !== false) {
        file_put_contents($filepath, $image_data);
        echo "✅ Téléchargé: $filename<br>";
    } else {
        // Si téléchargement échoue, créer une image colorée avec le nom
        $rgb = [
            'creme_hydratante.jpg' => [232, 67, 147],
            'serum_eclat.jpg' => [16, 185, 129],
            'fond_teint.jpg' => [245, 158, 11],
            'rouge_levres.jpg' => [239, 68, 68],
            'mascara.jpg' => [59, 130, 246],
            'gommage_visage.jpg' => [139, 92, 246],
            'demaquillant.jpg' => [6, 182, 212],
            'baume_levres.jpg' => [249, 115, 22],
            'contour_yeux.jpg' => [168, 85, 247]
        ];
        
        $img = imagecreate(400, 400);
        $bg = imagecolorallocate($img, $rgb[$filename][0], $rgb[$filename][1], $rgb[$filename][2]);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $bg);
        
        $text = str_replace('_', ' ', pathinfo($filename, PATHINFO_FILENAME));
        imagestring($img, 5, 50, 190, $text, $white);
        imagejpeg($img, $filepath, 80);
        imagedestroy($img);
        echo "⚠️ Créé (fallback): $filename<br>";
    }
}

echo "<hr>";
echo "<a href='products.php' class='btn btn-em' style='background:#10b981; color:white; padding:10px 20px; border-radius:8px; text-decoration:none;'>← Voir les produits</a>";
?>
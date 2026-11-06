<?php
$upload_dir = __DIR__ . '/../assets/images/';

for ($i = 1; $i <= 9; $i++) {
    $file = $upload_dir . 'image' . $i . '.jpg';
    if (file_exists($file)) {
        unlink($file);
        echo "🗑️ Supprimé: image$i.jpg<br>";
    } else {
        echo "⚠️ Non trouvé: image$i.jpg<br>";
    }
}
echo "<br><a href='products.php'>← Retour</a>";
?>
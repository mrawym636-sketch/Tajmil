<?php
echo "<h2>🔍 Vérification complète</h2>";

// Vérifier la base de données
require_once 'config.php';
$stmt = $pdo->query("SELECT COUNT(*) FROM produits");
$count = $stmt->fetchColumn();
echo "<p>📊 Produits en base: <strong>$count</strong></p>";

// Vérifier les images
$dir = __DIR__ . '/../assets/images/';
echo "<h3>🖼️ Images trouvées:</h3>";
$images = ['creme_hydratante.jpg', 'serum_eclat.jpg', 'fond_teint.jpg', 'rouge_levres.jpg', 'mascara.jpg', 'gommage_visage.jpg', 'demaquillant.jpg', 'baume_levres.jpg', 'contour_yeux.jpg'];

foreach ($images as $img) {
    if (file_exists($dir . $img)) {
        echo "<div style='color:green;'>✅ $img - <img src='../assets/images/$img' style='height:40px; margin-left:10px;'></div>";
    } else {
        echo "<div style='color:red;'>❌ $img manquant</div>";
    }
}

echo "<br><a href='products.php'>← Aller à la liste des produits</a>";
?>
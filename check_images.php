<?php
$dir = __DIR__ . '/../assets/images/';
echo "<h2>🔍 Vérification des images</h2>";

for ($i = 1; $i <= 10; $i++) {
    $file = $dir . 'image' . $i . '.jpg';
    if (file_exists($file)) {
        echo "<div style='color:green;'>✅ image$i.jpg existe - <img src='../assets/images/image$i.jpg' style='height:50px; margin-left:10px;'></div>";
    } else {
        echo "<div style='color:red;'>❌ image$i.jpg n'existe PAS</div>";
    }
}

echo "<hr>";
echo "<h3>Fichiers dans le dossier:</h3>";
$files = scandir($dir);
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "- $file<br>";
    }
}
?>
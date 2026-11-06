<?php
require_once 'config.php';

$upload_dir = __DIR__ . '/../assets/images/';
$fixed = 0;

// Parcourir tous les fichiers
$files = scandir($upload_dir);
foreach ($files as $file) {
    if ($file == '.' || $file == '..') continue;
    
    // Corriger les doubles extensions .jpg.jpg
    if (preg_match('/\.jpg\.jpg$/i', $file)) {
        $new_name = preg_replace('/\.jpg\.jpg$/i', '.jpg', $file);
        rename($upload_dir . $file, $upload_dir . $new_name);
        echo "✅ Renommé: $file → $new_name<br>";
        $fixed++;
    }
    // Corriger .jpeg
    elseif (preg_match('/\.jpeg$/i', $file)) {
        $new_name = preg_replace('/\.jpeg$/i', '.jpg', $file);
        rename($upload_dir . $file, $upload_dir . $new_name);
        echo "✅ Renommé: $file → $new_name<br>";
        $fixed++;
    }
    // Corriger les noms avec chiffres (produits uploadés via formulaire)
    elseif (preg_match('/^[0-9]+_[0-9]+\.jpg$/i', $file)) {
        // Garder le nom, c'est correct
        echo "✓ Nom correct: $file<br>";
    }
}

// Mettre à jour la base de données avec les nouveaux noms
echo "<hr><h3>Mise à jour de la base de données...</h3>";

$products = $pdo->query("SELECT id, image FROM produits")->fetchAll();
foreach ($products as $p) {
    $old_image = $p['image'];
    if (empty($old_image)) continue;
    
    $new_image = $old_image;
    // Corriger dans la base
    if (preg_match('/\.jpg\.jpg$/i', $new_image)) {
        $new_image = preg_replace('/\.jpg\.jpg$/i', '.jpg', $new_image);
    }
    if (preg_match('/\.jpeg$/i', $new_image)) {
        $new_image = preg_replace('/\.jpeg$/i', '.jpg', $new_image);
    }
    
    if ($new_image != $old_image) {
        $stmt = $pdo->prepare("UPDATE produits SET image = ? WHERE id = ?");
        $stmt->execute([$new_image, $p['id']]);
        echo "✅ Base de données: ID {$p['id']} → $new_image<br>";
    }
}

echo "<hr>";
echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px;'>";
echo "<h3>✨ Correction terminée! ($fixed fichiers renommés)</h3>";
echo "<a href='products.php' style='background:#10b981; color:white; padding:10px 20px; border-radius:8px; text-decoration:none;'>← Retour aux produits</a>";
echo "</div>";
?>
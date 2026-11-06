<?php
require_once 'config.php';

echo "<h2>🔧 Fix complet</h2>";

// 1. Vérifier les colonnes de produits
$columns = $pdo->query("SHOW COLUMNS FROM produits")->fetchAll(PDO::FETCH_COLUMN);
echo "<h3>Colonnes dans produits:</h3>";
echo "<ul>";
foreach(['id', 'nom', 'marque', 'prix', 'ancien_prix', 'description', 'image', 'ml', 'stock', 'prix_achat', 'category_id', 'brand_id'] as $col) {
    if(in_array($col, $columns)) {
        echo "<li style='color:green'>✅ $col</li>";
    } else {
        echo "<li style='color:red'>❌ $col manquant</li>";
    }
}
echo "</ul>";

// 2. Vérifier la table categories
$catCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
echo "<p>📊 Catégories: $catCount</p>";

// 3. Vérifier la table brands
$brandCount = $pdo->query("SELECT COUNT(*) FROM brands")->fetchColumn();
echo "<p>📊 Marques: $brandCount</p>";

// 4. Vérifier la table settings
$settings = $pdo->query("SELECT * FROM settings WHERE id=1")->fetch();
echo "<pre>";
print_r($settings);
echo "</pre>";

echo "<hr>";
echo "<a href='products.php' class='btn btn-em'>← Voir les produits</a>";
?>
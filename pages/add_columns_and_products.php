<?php
require_once '../php_files/config.php';

try {
    // 1. Ajouter les colonnes manquantes
    $pdo->exec("ALTER TABLE `produits` ADD COLUMN IF NOT EXISTS `date_fin_promo` DATE NULL AFTER `prix_achat`");
    $pdo->exec("ALTER TABLE `produits` ADD COLUMN IF NOT EXISTS `ancien_prix` DECIMAL(10,2) DEFAULT 0 AFTER `prix`");
    echo "✅ Colonnes ajoutées avec succès<br>";
    
    // 2. Vider la table (optionnel)
    $pdo->exec("TRUNCATE TABLE `produits`");
    echo "✅ Table produits vidée<br>";
    
    // 3. Insérer les nouveaux produits
    $produits = [
        [1, 'Crème Hydratante Visage 24H', 'Crème hydratante intense pour tous types de peau...', 250.00, 350.00, 'creme_hydratante.jpg', 'Tajmil Beauty', '50ml', 25, 120.00, '2026-12-31'],
        [2, 'Sérum Éclat Vitaminé C', 'Sérum illuminatrice à la vitamine C pure...', 380.00, 450.00, 'serum_vitamine_c.jpg', 'Tajmil Pro', '30ml', 15, 200.00, '2026-12-31'],
        [3, 'Masque Visage Argile Rose', 'Masque purifiant et adoucissant à l\'argile rose...', 180.00, 250.00, 'masque_argile.jpg', 'Tajmil Nature', '75ml', 30, 90.00, NULL],
        [4, 'Fond de Teint Haute Tenue', 'Fond de teint fluide à la couvrance modulable...', 220.00, 320.00, 'fond_teint.jpg', 'Tajmil Pro', '30ml', 20, 130.00, '2026-11-30'],
        [5, 'Rouge à Lèvres Mat Intense', 'Rouge à lèvres longue tenue, texture veloutée...', 120.00, 180.00, 'rouge_a_levres.jpg', 'Tajmil Beauty', '4g', 40, 60.00, NULL],
        [6, 'Mascara Volume Extrême', 'Mascara au formulaire waterproof...', 95.00, 150.00, 'mascara_volume.jpg', 'Tajmil Pro', '10ml', 50, 45.00, NULL],
        [7, 'Gommage Visage Doux', 'Gommage exfoliant à base de microbilles naturelles...', 150.00, 220.00, 'gommage_visage.jpg', 'Tajmil Nature', '75ml', 25, 80.00, NULL],
        [8, 'Soin Contour des Yeux', 'Soin contour des yeux à la caféine...', 210.00, 290.00, 'contour_yeux.jpg', 'Tajmil Beauty', '15ml', 18, 110.00, '2026-12-31'],
        [9, 'Brume Fixatrice Maquillage', 'Brume légère qui fixe le maquillage...', 130.00, 190.00, 'brume_fixatrice.jpg', 'Tajmil Pro', '50ml', 35, 70.00, NULL],
        [10, 'Huile Démaquillante Douceur', 'Huile démaquillante qui se transforme en lait...', 160.00, 230.00, 'huile_demaquillante.jpg', 'Tajmil Nature', '150ml', 22, 85.00, NULL]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO `produits` (`id`, `nom`, `description`, `prix`, `ancien_prix`, `image`, `marque`, `ml`, `stock`, `prix_achat`, `date_fin_promo`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($produits as $p) {
        $stmt->execute($p);
    }
    
    echo "✅ " . count($produits) . " produits insérés avec succès<br>";
    echo "<hr>";
    echo "<a href='products.php' style='background:#e84393; color:white; padding:12px 25px; border-radius:40px; text-decoration:none;'>Voir les produits cosmétiques</a>";
    
} catch(PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>
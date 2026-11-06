<?php
require_once '../php_files/config.php';

try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS parfumerie_db");
    $pdo->exec("USE parfumerie_db");
    
    // Table commandes
    $sql = "CREATE TABLE IF NOT EXISTS commandes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        numero VARCHAR(50) NOT NULL,
        user_id INT NULL,
        nom_client VARCHAR(100) NOT NULL,
        email_client VARCHAR(100) NOT NULL,
        telephone_client VARCHAR(20),
        total DECIMAL(10,2) NOT NULL,
        produits TEXT NOT NULL,
        statut VARCHAR(50) DEFAULT 'En traitement',
        date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    // Table avis
    $sql_avis = "CREATE TABLE IF NOT EXISTS avis (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        nom VARCHAR(100) NOT NULL,
        note INT NOT NULL CHECK (note BETWEEN 1 AND 5),
        commentaire TEXT NOT NULL,
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        statut VARCHAR(20) DEFAULT 'approuve',
        FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql_avis);
    
    // Table utilisateurs (si elle n'existe pas)
    $sql_users = "CREATE TABLE IF NOT EXISTS utilisateurs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        telephone VARCHAR(20) NULL,
        photo VARCHAR(255) DEFAULT 'default.png',
        role VARCHAR(20) DEFAULT 'user',
        date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_users);
    
    // Table produits
    $sql_produits = "CREATE TABLE IF NOT EXISTS produits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        description TEXT,
        prix DECIMAL(10,2) NOT NULL,
        ancien_prix DECIMAL(10,2) DEFAULT 0,
        image VARCHAR(255),
        marque VARCHAR(50),
        ml VARCHAR(20),
        stock INT DEFAULT 10,
        prix_achat DECIMAL(10,2) DEFAULT 0,
        date_fin_promo DATE NULL,
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_produits);
    
    // Table messages (contact)
    $sql_messages = "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        lu TINYINT DEFAULT 0
    )";
    $pdo->exec($sql_messages);
    
    // Table newsletter_subscribers
    $sql_newsletter = "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL UNIQUE,
        date_subscribed TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_newsletter);
    
    // Ajouter des produits par défaut si la table est vide
    $check_produits = $pdo->query("SELECT COUNT(*) FROM produits")->fetchColumn();
    if ($check_produits == 0) {
        $produits_default = [
            ['Crème Hydratante Visage', 'Crème hydratante naturelle pour tous types de peau', 250, 350, 'image1.jpg', 'Tajmil Beauty', '50ml'],
            ['Sérum Éclat Vitaminé', 'Sérum illuminatrice à la vitamine C', 380, 450, 'image2.jpg', 'Tajmil Beauty', '30ml'],
            ['Maquillage Professionnel - Fond de teint', 'Fond de teint longue tenue', 180, 250, 'image3.jpg', 'Tajmil Pro', '30ml'],
            ['Rouge à Lèvres Mat', 'Rouge à lèvres longue tenue', 120, 180, 'image4.jpg', 'Tajmil Beauty', '4g'],
            ['Mascara Volume', 'Mascara intense volume', 95, 150, 'image5.jpg', 'Tajmil Pro', '10ml'],
            ['Gommage Visage', 'Gommage doux pour une peau lisse', 150, 220, 'image6.jpg', 'Tajmil Beauty', '75ml']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO produits (nom, description, prix, ancien_prix, image, marque, ml) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($produits_default as $p) {
            $stmt->execute([$p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6]]);
        }
    }
    
    echo "<!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Installation - Tajmil Lwajh</title>
        <link href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css'>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #1a1a2e 0%, #2d1b36 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
            .card { background: white; border-radius: 30px; padding: 50px; max-width: 550px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
            .card i { font-size: 80px; color: #2ed573; margin-bottom: 20px; }
            .card h1 { font-size: 32px; color: #1a1a2e; margin-bottom: 15px; }
            .card p { color: #6c757d; margin-bottom: 10px; }
            .card .success { color: #2ed573; font-weight: 600; margin: 20px 0; text-align: left; background:#f0fff4; padding:15px; border-radius:15px; }
            .btn-group { display: flex; gap: 15px; justify-content: center; margin-top: 30px; flex-wrap:wrap; }
            .btn { padding: 12px 25px; border-radius: 50px; text-decoration: none; font-weight: 600; transition: 0.3s; }
            .btn-primary { background: #e84393; color: white; }
            .btn-primary:hover { background: #c2185b; transform: translateY(-2px); }
            .btn-secondary { background: #f0f0f5; color: #1a1a2e; }
            .btn-secondary:hover { background: #e8e8f0; transform: translateY(-2px); }
        </style>
    </head>
    <body>
        <div class='card'>
            <i class='fas fa-check-circle'></i>
            <h1>✅ Installation réussie !</h1>
            <p>Votre site Tajmil Lwajh est prêt.</p>
            <div class='success'>
                <i class='fas fa-database'></i> Tables créées :<br>
                ✓ utilisateurs<br>
                ✓ produits<br>
                ✓ commandes<br>
                ✓ avis<br>
                ✓ messages<br>
                ✓ newsletter_subscribers
            </div>
            <div class='btn-group'>
                <a href='home.php' class='btn btn-primary'>🏠 Accueil</a>
                <a href='products.php' class='btn btn-secondary'>💄 Voir les produits</a>
                <a href='admin/login.php' class='btn btn-secondary'>👑 Administration</a>
            </div>
        </div>
    </body>
    </html>";
    
} catch(PDOException $e) {
    echo "<div style='color:red; text-align:center; padding:50px; font-family:sans-serif;'>
            <h1>❌ Erreur</h1>
            <p>" . $e->getMessage() . "</p>
            <p>Vérifiez votre connexion MySQL dans config.php</p>
          </div>";
}
?>
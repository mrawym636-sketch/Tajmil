<?php
require_once '../php_files/config.php';

// ========== 1. TABLE visits ==========
$pdo->exec("CREATE TABLE IF NOT EXISTS visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_date DATE DEFAULT (CURRENT_DATE),
    visits INT DEFAULT 1,
    UNIQUE KEY unique_date (visit_date)
)");

// ========== 2. TABLE messages ==========
$pdo->exec("CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    lu TINYINT DEFAULT 0
)");

// ========== 3. TABLE commande_logs ==========
$pdo->exec("CREATE TABLE IF NOT EXISTS commande_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    admin_id INT NULL,
    admin_nom VARCHAR(100) NULL,
    ancien_statut VARCHAR(50) NULL,
    nouveau_statut VARCHAR(50) NOT NULL,
    date_modif TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// ========== 4. TABLE pages ==========
$pdo->exec("CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    content TEXT,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// ========== 5. TABLE logs ==========
$pdo->exec("CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    username VARCHAR(100) NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT NULL,
    ip_address VARCHAR(45) NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// ========== 6. TABLE newsletter_subscribers ==========
$pdo->exec("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    date_subscribed TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// ========== 7. TABLE blacklist ==========
$pdo->exec("CREATE TABLE IF NOT EXISTS blacklist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    telephone VARCHAR(20) NOT NULL UNIQUE,
    motif VARCHAR(255) NULL,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// ========== 8. CHAMPS MANQUANTS ==========
// date_inscription
$check = $pdo->query("SHOW COLUMNS FROM utilisateurs LIKE 'date_inscription'")->rowCount();
if ($check == 0) {
    $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
}

// prix_achat
$check = $pdo->query("SHOW COLUMNS FROM produits LIKE 'prix_achat'")->rowCount();
if ($check == 0) {
    $pdo->exec("ALTER TABLE produits ADD COLUMN prix_achat DECIMAL(10,2) DEFAULT 0");
}

// date_fin_promo
$check = $pdo->query("SHOW COLUMNS FROM produits LIKE 'date_fin_promo'")->rowCount();
if ($check == 0) {
    $pdo->exec("ALTER TABLE produits ADD COLUMN date_fin_promo DATE NULL");
}

echo "✅ Installation terminée ! <a href='home.php'>Accueil</a>";
?>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config.php';

// Vérifier si le panier est vide
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: ../pages/cart.php');
    exit();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $user_id = $_SESSION['user_id'] ?? null;
    
    // Validation
    if (empty($nom) || empty($email) || empty($telephone)) {
        header('Location: ../pages/checkout.php?error=1');
        exit();
    }
    
    // Calculer le total
    $total = 0;
    $produits_liste = [];
    
    foreach ($_SESSION['cart'] as $item) {
        $sous_total = $item['prix'] * $item['qty'];
        $total += $sous_total;
        $produits_liste[] = [
            'id' => $item['id'],
            'nom' => $item['nom'],
            'prix' => $item['prix'],
            'qty' => $item['qty'],
            'subtotal' => $sous_total
        ];
    }
    
    // Générer numéro de commande unique
    $numero_commande = 'CMD-' . date('Ymd') . '-' . rand(1000, 9999);
    
    // Convertir produits en JSON
    $produits_json = json_encode($produits_liste, JSON_UNESCAPED_UNICODE);
    
    try {
        // Vérifier si la table commandes existe
        $table_check = $pdo->query("SHOW TABLES LIKE 'commandes'");
        if ($table_check->rowCount() == 0) {
            // Créer la table si elle n'existe pas
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
        }
        
        // Insérer la commande
        $stmt = $pdo->prepare("INSERT INTO commandes (numero, user_id, nom_client, email_client, telephone_client, total, produits) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$numero_commande, $user_id, $nom, $email, $telephone, $total, $produits_json]);
        
        if ($result) {
            // Vider le panier après commande réussie
            unset($_SESSION['cart']);
            header('Location: ../pages/orders.php?success=1');
            exit();
        } else {
            header('Location: ../pages/checkout.php?error=2');
            exit();
        }
        
    } catch (PDOException $e) {
        header('Location: ../pages/checkout.php?error=2');
        exit();
    }
} else {
    header('Location: ../pages/checkout.php');
    exit();
}
?>
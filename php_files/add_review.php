<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['note']) || !isset($data['commentaire'])) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit();
}

$note = (int)$data['note'];
$commentaire = trim($data['commentaire']);
$user_id = $_SESSION['user_id'];
$nom = $_SESSION['user_nom'];

if ($note < 1 || $note > 5) {
    echo json_encode(['success' => false, 'message' => 'Note invalide (1-5)']);
    exit();
}

if (empty($commentaire)) {
    echo json_encode(['success' => false, 'message' => 'Commentaire vide']);
    exit();
}

try {
    // Vérifier si la table avis existe, sinon la créer
    $table_check = $pdo->query("SHOW TABLES LIKE 'avis'");
    if($table_check->rowCount() == 0) {
        $sql = "CREATE TABLE IF NOT EXISTS avis (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            nom VARCHAR(100) NOT NULL,
            note INT NOT NULL,
            commentaire TEXT NOT NULL,
            date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            statut VARCHAR(20) DEFAULT 'approuve'
        )";
        $pdo->exec($sql);
    }
    
    $stmt = $pdo->prepare("INSERT INTO avis (user_id, nom, note, commentaire, statut) VALUES (?, ?, ?, ?, 'approuve')");
    $stmt->execute([$user_id, $nom, $note, $commentaire]);
    
    echo json_encode(['success' => true, 'message' => 'Merci pour votre avis !']);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()]);
}
?>
<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

try {
    $table_check = $pdo->query("SHOW TABLES LIKE 'avis'");
    if($table_check->rowCount() == 0) {
        echo json_encode(['success' => true, 'reviews' => [], 'moyenne' => 4.8, 'total' => 0]);
        exit();
    }
    
    // Récupérer les avis avec la photo de l'utilisateur
    $stmt = $pdo->query("
        SELECT a.nom, a.note, a.commentaire, a.date_creation, u.photo 
        FROM avis a 
        LEFT JOIN utilisateurs u ON a.user_id = u.id 
        WHERE a.statut = 'approuve' 
        ORDER BY a.date_creation DESC 
        LIMIT 20
    ");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ajouter photo par défaut si pas de photo
    foreach($reviews as &$review) {
        $review['photo'] = $review['photo'] ?? 'default.png';
    }
    
    $stmt_avg = $pdo->query("SELECT AVG(note) as moyenne, COUNT(*) as total FROM avis WHERE statut = 'approuve'");
    $stats = $stmt_avg->fetch(PDO::FETCH_ASSOC);
    
    $moyenne = $stats['moyenne'] ? round($stats['moyenne'], 1) : 4.8;
    $total = $stats['total'] ? $stats['total'] : 0;
    
    echo json_encode([
        'success' => true,
        'reviews' => $reviews,
        'moyenne' => $moyenne,
        'total' => $total
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'reviews' => [], 'moyenne' => 4.8, 'total' => 0]);
}
?>
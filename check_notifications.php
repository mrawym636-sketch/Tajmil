<?php
require_once 'config.php';
header('Content-Type: application/json');
$low_stock = $pdo->query("SELECT COUNT(*) FROM produits WHERE stock <= 5 AND stock > 0")->fetchColumn();
echo json_encode(['low_stock' => (int)$low_stock]);
?>
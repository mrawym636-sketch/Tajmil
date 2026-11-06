<?php
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['nom']) && !empty($data['email']) && !empty($data['message'])) {
    $stmt = $pdo->prepare("INSERT INTO messages (nom, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$data['nom'], $data['email'], $data['message']]);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
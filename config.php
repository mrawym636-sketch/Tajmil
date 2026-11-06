<?php
require_once __DIR__ . '/../php_files/config.php';
define('ADMIN_IMG_PATH', '../assets/images/');

$current_file = basename($_SERVER['SCRIPT_FILENAME']);
if (!in_array($current_file, ['login.php', 'auth.php'])) {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        header('Location: login.php');
        exit();
    }
}

// Fonction de log général
function logAction($pdo, $action, $details = null) {
    $user_id = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['user_nom'] ?? 'Inconnu';
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("INSERT INTO logs (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $username, $action, $details, $ip]);
}

// Fonction pour log les changements de statut des commandes
function logCommandeStatus($pdo, $commande_id, $ancien, $nouveau) {
    $admin_id = $_SESSION['user_id'] ?? null;
    $admin_nom = $_SESSION['user_nom'] ?? 'Admin';
    $stmt = $pdo->prepare("INSERT INTO commande_logs (commande_id, admin_id, admin_nom, ancien_statut, nouveau_statut) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$commande_id, $admin_id, $admin_nom, $ancien, $nouveau]);
}
?>
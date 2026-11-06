<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    unset($_SESSION['cart'][$id]);
}

echo json_encode(['success' => true]);
?>
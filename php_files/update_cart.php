<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $qty = (int)$_POST['qty'];
    
    if ($qty <= 0) {
        unset($_SESSION['cart'][$id]);
    } elseif (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['qty'] = $qty;
    }
}

echo json_encode(['success' => true]);
?>
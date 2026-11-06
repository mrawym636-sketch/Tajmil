<?php
require_once 'config.php';

$response = ['success' => false, 'count' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $qty = (int)$_POST['qty'];
    $ml = isset($_POST['ml']) ? $_POST['ml'] : '100ml';
    
    if ($id > 0 && $qty > 0) {
        $product = getProductById($pdo, $id);
        
        if ($product) {
            if (!isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id] = [
                    'id' => $id,
                    'qty' => $qty,
                    'nom' => $product['nom'],
                    'prix' => floatval($product['prix']),
                    'ml' => $ml,
                    'img' => $product['image']
                ];
            } else {
                $_SESSION['cart'][$id]['qty'] += $qty;
            }
            
            $response = ['success' => true, 'count' => getCartCount()];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
<?php
require_once 'config.php';

$items = [];
$total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $subtotal = $item['prix'] * $item['qty'];
        $total += $subtotal;
        $items[] = [
            'id' => $item['id'],
            'nom' => $item['nom'],
            'prix' => $item['prix'],
            'qty' => $item['qty'],
            'subtotal' => $subtotal,
            'ml' => isset($item['ml']) ? $item['ml'] : '100ml'
        ];
    }
}

header('Content-Type: application/json');
echo json_encode(['items' => $items, 'total' => $total]);
?>
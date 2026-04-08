<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] == $data['id']) {
        $item['quantity'] += $data['quantity'];
        $found = true;
        break;
    }
}

if (!$found) {
    $_SESSION['cart'][] = [
        'id' => $data['id'],
        'name' => $data['name'],
        'price' => $data['price'],
        'quantity' => $data['quantity']
    ];
}

echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
?>
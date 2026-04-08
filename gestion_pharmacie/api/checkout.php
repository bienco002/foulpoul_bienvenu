<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$cart = $data['cart'];

if (empty($cart)) {
    http_response_code(400);
    echo json_encode(['error' => 'Panier vide']);
    exit();
}

// Vérifier les stocks
foreach ($cart as $item) {
    if (!checkStock($item['id'], $item['quantity'])) {
        echo json_encode(['error' => 'Stock insuffisant pour: ' . $item['name']]);
        exit();
    }
}

$total = array_sum(array_map(function($item) {
    return $item['price'] * $item['quantity'];
}, $cart));

$commande_id = createOrder($_SESSION['user_id'], $cart, $total);

if ($commande_id) {
    $_SESSION['cart'] = [];
    echo json_encode(['success' => true, 'commande_id' => $commande_id]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la création de la commande']);
}
?>
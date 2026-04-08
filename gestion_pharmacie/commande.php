<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('panier.php');
}

$cart = json_decode($_POST['cart'], true);
$total = floatval($_POST['total']);

if (empty($cart)) {
    setFlash('error', 'Votre panier est vide');
    redirect('panier.php');
}

// Vérifier les stocks
foreach ($cart as $item) {
    if (!checkStock($item['id'], $item['quantity'])) {
        setFlash('error', 'Stock insuffisant pour le produit: ' . $item['name']);
        redirect('panier.php');
    }
}

// Créer la commande
$commande_id = createOrder($_SESSION['user_id'], $cart, $total);

if ($commande_id) {
    // Vider le panier (côté client)
    setFlash('success', 'Commande passée avec succès !');
    redirect('mes-commandes.php');
} else {
    setFlash('error', 'Erreur lors de la création de la commande');
    redirect('panier.php');
}
?>
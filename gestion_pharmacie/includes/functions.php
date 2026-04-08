<?php
require_once 'config.php';

// Fonction pour nettoyer les entrées
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fonction pour rediriger
function redirect($url) {
    header("Location: " . SITE_URL . $url);
    exit();
}

// Fonction pour afficher les messages flash
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fonction pour vérifier si l'utilisateur est admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Fonction pour obtenir les informations de l'utilisateur
function getUserInfo($user_id = null) {
    global $pdo;
    
    if ($user_id === null) {
        $user_id = $_SESSION['user_id'] ?? null;
    }
    
    if ($user_id) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    }
    return null;
}

// Fonction pour obtenir tous les produits
function getAllProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM produits ORDER BY id DESC");
    return $stmt->fetchAll();
}

// Fonction pour obtenir un produit par ID
function getProductById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Fonction pour obtenir les produits par catégorie
function getProductsByCategory($category) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE categorie = ? AND stock > 0");
    $stmt->execute([$category]);
    return $stmt->fetchAll();
}

// Fonction pour vérifier le stock
function checkStock($product_id, $quantity) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT stock FROM produits WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    return $product && $product['stock'] >= $quantity;
}

// Fonction pour mettre à jour le stock
function updateStock($product_id, $quantity) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id = ?");
    return $stmt->execute([$quantity, $product_id]);
}

// Fonction pour créer une commande
function createOrder($user_id, $cart_items, $total) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Créer la commande
        $stmt = $pdo->prepare("INSERT INTO commandes (user_id, total, status) VALUES (?, ?, 'en_attente')");
        $stmt->execute([$user_id, $total]);
        $commande_id = $pdo->lastInsertId();
        
        // Ajouter les détails de la commande
        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare("INSERT INTO commande_details (commande_id, produit_id, quantite, prix) VALUES (?, ?, ?, ?)");
            $stmt->execute([$commande_id, $item['id'], $item['quantity'], $item['price']]);
            
            // Mettre à jour le stock
            updateStock($item['id'], $item['quantity']);
        }
        
        $pdo->commit();
        return $commande_id;
    } catch(Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

// Fonction pour obtenir les commandes d'un utilisateur
function getUserOrders($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM commandes WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Fonction pour obtenir les détails d'une commande
function getOrderDetails($commande_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT cd.*, p.nom as product_name 
        FROM commande_details cd 
        JOIN produits p ON cd.produit_id = p.id 
        WHERE cd.commande_id = ?
    ");
    $stmt->execute([$commande_id]);
    return $stmt->fetchAll();
}

// Fonction pour obtenir toutes les commandes (admin)
function getAllOrders() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT c.*, u.username, u.email 
        FROM commandes c 
        JOIN users u ON c.user_id = u.id 
        ORDER BY c.created_at DESC
    ");
    return $stmt->fetchAll();
}

// Fonction pour mettre à jour le statut d'une commande
function updateOrderStatus($commande_id, $status) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE commandes SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $commande_id]);
}

// Fonction pour obtenir les statistiques
function getStats() {
    global $pdo;
    
    $stats = [];
    
    // Nombre total de produits
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM produits");
    $stats['total_products'] = $stmt->fetch()['total'];
    
    // Nombre total d'utilisateurs
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'client'");
    $stats['total_users'] = $stmt->fetch()['total'];
    
    // Nombre total de commandes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM commandes");
    $stats['total_orders'] = $stmt->fetch()['total'];
    
    // Chiffre d'affaires total
    $stmt = $pdo->query("SELECT SUM(total) as total FROM commandes WHERE status != 'annulee'");
    $stats['total_revenue'] = $stmt->fetch()['total'] ?? 0;
    
    // Commandes en attente
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM commandes WHERE status = 'en_attente'");
    $stats['pending_orders'] = $stmt->fetch()['total'];
    
    return $stats;
}
?>
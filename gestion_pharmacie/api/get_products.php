<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

try {
    $category = isset($_GET['category']) ? $_GET['category'] : 'all';
    
    if ($category === 'all') {
        $stmt = $pdo->query("SELECT * FROM produits WHERE stock > 0 ORDER BY id DESC");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM produits WHERE categorie = ? AND stock > 0 ORDER BY id DESC");
        $stmt->execute([$category]);
    }
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($products);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
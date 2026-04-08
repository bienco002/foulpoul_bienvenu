<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$orders = getUserOrders($user_id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes commandes - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <h1><i class="fas fa-hospital-user"></i> <?php echo SITE_NAME; ?></h1>
                </div>
                <ul class="nav-links">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="index.php#produits">Produits</a></li>
                    <li><a href="mes-commandes.php" class="active">Mes commandes</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                    <li><a href="panier.php" class="cart-icon"><i class="fas fa-shopping-cart"></i> <span id="cart-count">0</span></a></li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="orders-container">
            <h2>Mes commandes</h2>
            
            <?php if(empty($orders)): ?>
                <p class="no-orders">Vous n'avez pas encore passé de commande.</p>
            <?php else: ?>
                <?php foreach($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <strong>Commande #<?php echo $order['id']; ?></strong>
                                <br>
                                <small>Date: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></small>
                            </div>
                            <div class="order-status status-<?php echo $order['status']; ?>">
                                <?php
                                $status_labels = [
                                    'en_attente' => 'En attente',
                                    'confirmee' => 'Confirmée',
                                    'expediee' => 'Expédiée',
                                    'livree' => 'Livrée',
                                    'annulee' => 'Annulée'
                                ];
                                echo $status_labels[$order['status']];
                                ?>
                            </div>
                        </div>
                        <div class="order-details">
                            <p>Total: <?php echo number_format($order['total'], 2); ?> €</p>
                            <button class="btn-secondary" onclick="showOrderDetails(<?php echo $order['id']; ?>)">
                                Voir les détails
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
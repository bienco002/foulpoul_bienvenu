<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon panier - <?php echo SITE_NAME; ?></title>
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
                    <?php if (isLoggedIn()): ?>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="btn-login"><i class="fas fa-user"></i> Connexion</a></li>
                    <?php endif; ?>
                    <li><a href="panier.php" class="cart-icon"><i class="fas fa-shopping-cart"></i> <span id="cart-count">0</span></a></li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="cart-container">
            <h2>Mon panier</h2>
            <div id="cart-items"></div>
            <div id="cart-summary"></div>
            <div class="cart-actions">
                <a href="index.php#produits" class="btn-secondary">Continuer mes achats</a>
                <button id="checkout-btn" class="btn-primary">Passer la commande</button>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
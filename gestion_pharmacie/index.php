<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Récupérer les produits en vedette
$stmt = $pdo->query("SELECT * FROM produits WHERE stock > 0 ORDER BY id DESC LIMIT 8");
$featured_products = $stmt->fetchAll();

// Récupérer les catégories
$stmt = $pdo->query("SELECT DISTINCT categorie FROM produits");
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Votre santé notre priorité</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <h1><i class="fas fa-hospital-user"></i> <?php echo SITE_NAME; ?></h1>
                </div>
                <ul class="nav-links">
                    <li><a href="index.php" class="active">Accueil</a></li>
                    <li><a href="#produits">Produits</a></li>
                    <li><a href="#about">À propos</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="mes-commandes.php"><i class="fas fa-history"></i> Mes commandes</a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                        <?php if (isAdmin()): ?>
                            <li><a href="admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin</a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li><a href="login.php" class="btn-login"><i class="fas fa-user"></i> Connexion</a></li>
                    <?php endif; ?>
                    <li><a href="panier.php" class="cart-icon"><i class="fas fa-shopping-cart"></i> <span id="cart-count">0</span></a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h2>Votre santé, notre priorité</h2>
                <p>Commandez vos médicaments en ligne et recevez-les à domicile</p>
                <a href="#produits" class="btn-primary">Découvrir nos produits</a>
            </div>
        </div>
    </section>

    <!-- Produits Section -->
    <section id="produits" class="produits">
        <div class="container">
            <h2 class="section-title">Nos produits</h2>
            <div class="filters">
                <button class="filter-btn active" data-category="all">Tous</button>
                <?php foreach($categories as $cat): ?>
                    <button class="filter-btn" data-category="<?php echo htmlspecialchars($cat['categorie']); ?>">
                        <?php echo htmlspecialchars($cat['categorie']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <div id="products-container" class="products-grid">
                <?php foreach($featured_products as $product): ?>
                    <div class="product-card" data-category="<?php echo htmlspecialchars($product['categorie']); ?>">
                        <div class="product-image">
                            <i class="fas fa-capsules"></i>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><?php echo htmlspecialchars($product['nom']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...</p>
                            <div class="product-price"><?php echo number_format($product['prix'], 2); ?> €</div>
                            <div class="product-stock">Stock: <?php echo $product['stock']; ?></div>
                            <?php if($product['ordonnance']): ?>
                                <div class="prescription-badge">Sur ordonnance</div>
                            <?php endif; ?>
                            <button class="btn-add-cart" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['nom']); ?>', <?php echo $product['prix']; ?>)">
                                Ajouter au panier
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?php echo SITE_NAME; ?></h3>
                    <p>Votre pharmacie en ligne de confiance</p>
                </div>
                <div class="footer-section">
                    <h3>Liens utiles</h3>
                    <ul>
                        <li><a href="#">Conditions générales</a></li>
                        <li><a href="#">Politique de confidentialité</a></li>
                        <li><a href="#">Livraison</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <p><i class="fas fa-phone"></i> +33 1 23 45 67 89</p>
                    <p><i class="fas fa-envelope"></i> contact@pharmacare.fr</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
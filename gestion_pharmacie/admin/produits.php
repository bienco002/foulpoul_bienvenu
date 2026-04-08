<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

$products = getAllProducts();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des produits - Administration</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <div class="logo">
                <h2><i class="fas fa-hospital-user"></i> Admin</h2>
            </div>
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                <li><a href="produits.php" class="active"><i class="fas fa-capsules"></i> Produits</a></li>
                <li><a href="commandes.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
                <li><a href="utilisateurs.php"><i class="fas fa-users"></i> Utilisateurs</a></li>
                <li><a href="../index.php"><i class="fas fa-arrow-left"></i> Retour au site</a></li>
            </ul>
        </nav>
        
        <main class="admin-content">
            <div class="page-header">
                <h1>Gestion des produits</h1>
                <a href="ajouter-produit.php" class="btn-primary"><i class="fas fa-plus"></i> Ajouter un produit</a>
            </div>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Catégorie</th>
                        <th>Ordonnance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo htmlspecialchars($product['nom']); ?></td>
                        <td><?php echo number_format($product['prix'], 2); ?> €</td>
                        <td><?php echo $product['stock']; ?></td>
                        <td><?php echo htmlspecialchars($product['categorie']); ?></td>
                        <td><?php echo $product['ordonnance'] ? 'Oui' : 'Non'; ?></td>
                        <td>
                            <a href="modifier-produit.php?id=<?php echo $product['id']; ?>" class="btn-small btn-edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="supprimer-produit.php?id=<?php echo $product['id']; ?>" class="btn-small btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
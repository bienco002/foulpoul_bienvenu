<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = cleanInput($_POST['nom']);
    $description = cleanInput($_POST['description']);
    $prix = floatval($_POST['prix']);
    $stock = intval($_POST['stock']);
    $categorie = cleanInput($_POST['categorie']);
    $ordonnance = isset($_POST['ordonnance']) ? 1 : 0;
    
    $stmt = $pdo->prepare("INSERT INTO produits (nom, description, prix, stock, categorie, ordonnance) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$nom, $description, $prix, $stock, $categorie, $ordonnance])) {
        setFlash('success', 'Produit ajouté avec succès');
        redirect('produits.php');
    } else {
        $error = 'Erreur lors de l\'ajout du produit';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit - Administration</title>
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
            </ul>
        </nav>
        
        <main class="admin-content">
            <h1>Ajouter un produit</h1>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" class="admin-form">
                <div class="form-group">
                    <label for="nom">Nom du produit</label>
                    <input type="text" id="nom" name="nom" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5" required></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="prix">Prix (€)</label>
                        <input type="number" id="prix" name="prix" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="categorie">Catégorie</label>
                        <select id="categorie" name="categorie" required>
                            <option value="Médicaments">Médicaments</option>
                            <option value="Antibiotiques">Antibiotiques</option>
                            <option value="Compléments">Compléments</option>
                            <option value="Parapharmacie">Parapharmacie</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="ordonnance"> Sur ordonnance
                        </label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Ajouter le produit</button>
                    <a href="produits.php" class="btn-secondary">Annuler</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
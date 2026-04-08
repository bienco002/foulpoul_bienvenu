<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

$stats = getStats();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Administration</title>
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
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                <li><a href="produits.php"><i class="fas fa-capsules"></i> Produits</a></li>
                <li><a href="commandes.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
                <li><a href="utilisateurs.php"><i class="fas fa-users"></i> Utilisateurs</a></li>
                <li><a href="../index.php"><i class="fas fa-arrow-left"></i> Retour au site</a></li>
            </ul>
        </nav>
        
        <main class="admin-content">
            <h1>Tableau de bord</h1>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-capsules"></i>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_products']; ?></h3>
                        <p>Produits</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_users']; ?></h3>
                        <p>Utilisateurs</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-shopping-cart"></i>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_orders']; ?></h3>
                        <p>Commandes</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <i class="fas
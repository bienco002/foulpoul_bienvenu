<?php
require_once 'config.php';
require_once 'functions.php';

// Vérifier si l'utilisateur est connecté
function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('error', 'Vous devez être connecté pour accéder à cette page');
        redirect('login.php');
    }
}

// Vérifier si l'utilisateur est admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        setFlash('error', 'Accès non autorisé');
        redirect('index.php');
    }
}

// Fonction de connexion
function login($email, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        return true;
    }
    
    return false;
}

// Fonction d'inscription
function register($username, $email, $password) {
    global $pdo;
    
    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return 'email_exists';
    }
    
    // Vérifier si le nom d'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        return 'username_exists';
    }
    
    // Créer l'utilisateur
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$username, $email, $hashed_password])) {
        return true;
    }
    
    return false;
}

// Fonction de déconnexion
function logout() {
    session_destroy();
    redirect('index.php');
}
?>
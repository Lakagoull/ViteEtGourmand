<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: index.php'); exit(); }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Utilisateur | Admin</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-links">
        <a href="index.php">🏠 Site</a>
        <a href="admin-menus.php">🍴 Menus</a>
        <a href="admin-reservations.php">📅 Réservations</a>
        <a href="admin-users.php" class="active">👥 Utilisateurs</a>
    </div>
    <div class="nav-admin-info">ADMINISTRATEUR</div>
</nav>

<div class="container">
    <div class="admin-form-container">
        <h1>👥 Nouvel Administrateur</h1>
        
        <form action="traitement-ajouter-utilisateur.php" method="POST">
            <div class="form-group">
                <label>Nom complet</label>
                <input type="text" name="nom" placeholder="Ex: José Garcia" required>
            </div>

            <div class="form-group">
                <label>Adresse Email</label>
                <input type="email" name="email" placeholder="email@exemple.com" required 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" placeholder="Mot de passe secret" required 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div class="form-group">
                <label>Rôle</label>
                <select name="role">
                    <option value="admin">Administrateur</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">✅ Créer le compte</button>
        </form>
        
        <a href="admin-users.php" class="btn-back">← Retour à la liste</a>
    </div>
</div>

</body>
</html>
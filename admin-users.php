<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: index.php'); exit(); }
require_once('config.php');

// On récupère la liste des utilisateurs
$sql = "SELECT id, nom, email, role FROM utilisateurs ORDER BY nom ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Utilisateurs | Admin</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-links">
        <a href="index.php">🏠 Site</a>
        <a href="admin-menus.php">🍴 Menus</a>
        <a href="admin-reservations.php">📅 Réservations</a>
        <a href="admin-users.php" class="active">👥 Utilisateurs</a>
        <a href="admin-avis.php" class="active">⭐ Avis</a>
        <a href="admin-stats.php">📊 Statistiques</a>
    </div>
    <div class="nav-admin-info">
        ADMINISTRATEUR
    </div>
</nav>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>👥 Gestion des Utilisateurs</h1>
        <a href="ajouter-utilisateur.php" style="background: #27ae60; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">+ Ajouter un admin</a>
    </div>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($user = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td>#<?php echo $user['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($user['nom']); ?></strong></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td>
                    <a href="supprimer-utilisateur.php?id=<?php echo $user['id']; ?>" 
                       style="color: #e74c3c; text-decoration: none; font-weight: bold;" 
                       onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
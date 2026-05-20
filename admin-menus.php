<?php
session_start();
// Vérification de sécurité : si pas admin, on dégage !
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once('config.php');
$result = mysqli_query($conn, "SELECT * FROM menus");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Vite & Gourmand</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .admin-table th, .admin-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .btn-add { background: #27ae60; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-bottom: 10px;}
        .btn-edit { color: #2980b9; margin-right: 10px; font-weight: bold; text-decoration: none; }
        .btn-delete { color: #c0392b; font-weight: bold; text-decoration: none; }
        .btn-delete:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <nav class="navbar">
    <div class="nav-links">
        <a href="index.php">🏠 Site</a>
        <a href="admin-menus.php" class="active">🍴 Menus</a>
        <a href="admin-reservations.php">📅 Réservations</a>
        <a href="admin-users.php">👥 Utilisateurs</a>
        <a href="admin-avis.php" class="active">⭐ Avis</a>
        <a href="admin-stats.php">📊 Statistiques</a>
    </div>
    <div class="nav-admin-info">
        ADMINISTRATEUR
    </div>
</nav>
    <div class="container">
        <h1>Gestion des Menus</h1>

        <?php if(isset($_GET['msg'])): ?>
            <div id="message-alerte" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center; font-weight: bold; 
                <?php 
                    if($_GET['msg'] == 'supprime') {
                        echo 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;'; 
                    } else {
                        echo 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;'; 
                    }
                ?>">
                
                <?php 
                    if($_GET['msg'] == 'ajoute') echo "✅ Le menu a été ajouté avec succès !";
                    if($_GET['msg'] == 'modifie') echo "📝 Le menu a été mis à jour !";
                    if($_GET['msg'] == 'supprime') echo "🗑️ Le menu a bien été supprimé.";
                ?>
            </div>

            <script>
                // Le message disparaît après 3 secondes
                setTimeout(function() {
                    var msg = document.getElementById('message-alerte');
                    if(msg) msg.style.display = 'none';
                }, 3000);
            </script>
        <?php endif; ?>

        <a href="ajouter-menus.php" class="btn-add">+ Ajouter un menu</a>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Titre</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><img src="assets/<?php echo $row['image']; ?>" width="50" style="border-radius: 4px;"></td>
                    <td><?php echo htmlspecialchars($row['titre']); ?></td>
                    <td><?php echo $row['prix_min_pers']; ?>€</td>
                    <td><?php echo $row['stock_dispo']; ?></td>
                    <td>
                        <a href="modifier-menu.php?id=<?php echo $row['id']; ?>" class="btn-edit">Modifier</a>
                        
                        <a href="supprimer-menu.php?id=<?php echo $row['id']; ?>" 
                           class="btn-delete" 
                           onclick="return confirm('Es-tu sûr de vouloir supprimer ce menu définitivement ?');">
                           Supprimer
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
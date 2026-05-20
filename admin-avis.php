<?php
session_start();
// Vérification de sécurité (identique à tes autres pages admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once('config.php');

// Traitement des actions (Validation ou Suppression)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] == 'valider') {
        mysqli_query($conn, "UPDATE avis SET valide = 1 WHERE id = $id");
        header("Location: admin-avis.php?msg=valide");
        exit();
    } elseif ($_GET['action'] == 'refuser') {
        mysqli_query($conn, "DELETE FROM avis WHERE id = $id");
        header("Location: admin-avis.php?msg=supprime");
        exit();
    }
}

// Récupération des avis en attente
$result = mysqli_query($conn, "SELECT * FROM avis WHERE valide = 0 ORDER BY date_avis DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration Avis - Vite & Gourmand</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .admin-table th, .admin-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .btn-valid { color: #27ae60; margin-right: 15px; font-weight: bold; text-decoration: none; }
        .btn-delete { color: #c0392b; font-weight: bold; text-decoration: none; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-links">
            <a href="index.php">🏠 Site</a>
            <a href="admin-menus.php">🍴 Menus</a>
            <a href="admin-reservations.php">📅 Réservations</a>
            <a href="admin-users.php">👥 Utilisateurs</a>
            <a href="admin-avis.php" class="active">⭐ Avis</a>
            <a href="admin-stats.php">📊 Statistiques</a>
        </div>
        <div class="nav-admin-info">ADMINISTRATEUR</div>
    </nav>

    <div class="container">
        <h1>Modération des Avis</h1>

        <?php if(isset($_GET['msg'])): ?>
            <div id="message-alerte" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center; font-weight: bold;
                <?php echo ($_GET['msg'] == 'supprime') ? 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;' : 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;'; ?>">
                <?php 
                    echo ($_GET['msg'] == 'valide') ? "✅ L'avis a été publié avec succès !" : "🗑️ L'avis a été supprimé.";
                ?>
            </div>
            <script>setTimeout(function() { document.getElementById('message-alerte').style.display = 'none'; }, 3000);</script>
        <?php endif; ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Note</th>
                    <th>Commentaire</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nom_client']); ?></td>
                    <td><?php echo $row['note']; ?>/5</td>
                    <td><?php echo htmlspecialchars($row['commentaire']); ?></td>
                    <td>
                        <a href="admin-avis.php?id=<?php echo $row['id']; ?>&action=valider" class="btn-valid">Valider</a>
                        <a href="admin-avis.php?id=<?php echo $row['id']; ?>&action=refuser" class="btn-delete" onclick="return confirm('Supprimer cet avis ?');">Refuser</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
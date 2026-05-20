<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: index.php'); exit(); }
require_once('config.php');

$id = $_GET['id'];
$res = mysqli_query($conn, "SELECT * FROM menus WHERE id = $id");
$menu = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Menu | Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        /* Exactement le même style pour la cohérence */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f4f7f6; color: #333; }
        .navbar { background: #2c3e50; padding: 15px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; margin-right: 20px; font-weight: bold; }
        .container { max-width: 800px; margin: 40px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { color: #2980b9; margin-top: 0; text-align: center; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; }
        input[type="text"], input[type="number"], textarea, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .form-row { display: flex; gap: 20px; }
        .form-row > div { flex: 1; }
        .btn-submit { background: #2980b9; color: white; border: none; padding: 15px 25px; border-radius: 8px; cursor: pointer; width: 100%; font-size: 18px; font-weight: bold; }
        .current-img { margin-top: 10px; font-style: italic; color: #666; display: block; }
    </style>
</head>
<body>

    <nav style="background: #2c3e50; padding: 15px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
    <div style="display: flex; gap: 20px;">
        <a href="index.php" style="color: white; text-decoration: none; font-weight: bold;">🏠 Voir le site</a>
        <a href="admin-menus.php" style="color: #deff9a; text-decoration: none; font-weight: bold; border-bottom: 2px solid #deff9a;">🍴 Gestion Menus</a>
        <a href="admin-reservations.php" style="color: white; text-decoration: none;">📅 Réservations</a>
        <a href="admin-users.php" style="color: white; text-decoration: none;">👥 Utilisateurs</a>
    </div>
    </nav>

    <div class="container">
        <h1>📝 Modifier le menu</h1>
        <form action="traitement-modifier-menu.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $menu['id']; ?>">

            <div class="form-row">
                <div class="form-group">
                    <label>Titre</label>
                    <input type="text" name="titre" value="<?php echo htmlspecialchars($menu['titre']); ?>">
                </div>
                <div class="form-group">
                    <label>Thème</label>
                    <input type="text" name="theme" value="<?php echo htmlspecialchars($menu['theme']); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Accroche</label>
                <input type="text" name="accroche" value="<?php echo htmlspecialchars($menu['description']); ?>">
            </div>

            <div class="form-group">
                <label>Composition</label>
                <textarea name="composition"><?php echo htmlspecialchars($menu['plats']); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Prix (€)</label>
                    <input type="number" name="prix" step="0.01" value="<?php echo $menu['prix_min_pers']; ?>">
                </div>
                <div class="form-group">
                    <label>Min. Pers</label>
                    <input type="number" name="min_pers" value="<?php echo $menu['nb_pers_min']; ?>">
                </div>
            </div>

            <div class="form-group">
               <label>Allergènes</label>
               <input type="text" name="allergenes" placeholder="Ex: Gluten, Fruits à coque, Lactose...">
            </div>

            <div class="form-group">
                <label>Changer l'image (optionnel)</label>
                <input type="file" name="image">
                <span class="current-img">Actuelle : <?php echo $menu['image']; ?></span>
            </div>

            <button type="submit" class="btn-submit">💾 Enregistrer les modifications</button>
        </form>
    </div>

</body>
</html>
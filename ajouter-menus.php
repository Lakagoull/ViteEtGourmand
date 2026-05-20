<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: index.php'); exit(); }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Menu | Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f4f7f6; color: #333; }
        .navbar { background: #2c3e50; padding: 15px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; margin-right: 20px; font-weight: bold; }
        
        .container { max-width: 800px; margin: 40px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; margin-top: 0; text-align: center; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #444; }
        input[type="text"], input[type="number"], textarea, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 16px; }
        textarea { height: 100px; resize: vertical; }
        
        .form-row { display: flex; gap: 20px; }
        .form-row > div { flex: 1; }
        
        .btn-submit { background: #27ae60; color: white; border: none; padding: 15px 25px; border-radius: 8px; cursor: pointer; width: 100%; font-size: 18px; font-weight: bold; transition: background 0.3s; margin-top: 10px; }
        .btn-submit:hover { background: #219150; }
        .btn-back { display: block; text-align: center; margin-top: 20px; color: #7f8c8d; text-decoration: none; }
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
        <h1>🆕 Nouveau Menu</h1>
        <form action="traitement-ajouter-menus.php" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label>Titre du menu</label>
                    <input type="text" name="titre" required>
                </div>
                <div class="form-group">
                    <label>Thème</label>
                    <input type="text" name="theme" placeholder="Mariage, Noël...">
                </div>
            </div>

            <div class="form-group">
                <label>Régime</label>
                <select name="regime">
                    <option value="Normal">Normal</option>
                    <option value="Végétarien">Végétarien</option>
                    <option value="Végan">Végan</option>
                </select>
            </div>

            <div class="form-group">
                <label>Phrase d'accroche (Description)</label>
                <input type="text" name="accroche">
            </div>

            <div class="form-group">
                <label>Composition (Plats)</label>
                <textarea name="composition"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Prix / pers (€)</label>
                    <input type="number" name="prix" step="0.01">
                </div>
                <div class="form-group">
                    <label>Min. personnes</label>
                    <input type="number" name="min_pers" value="1">
                </div>
            </div>

            <div class="form-group">
               <label>Allergènes</label>
               <input type="text" name="allergenes" placeholder="Ex: Gluten, Fruits à coque, Lactose...">
            </div>

            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image" required>
            </div>

            <button type="submit" class="btn-submit">🚀 Enregistrer le menu</button>
        </form>
        <a href="admin-menus.php" class="btn-back">← Retour à la gestion</a>
    </div>

</body>
</html>
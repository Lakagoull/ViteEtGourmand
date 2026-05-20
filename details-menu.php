<?php
session_start();
require_once('config.php');

// 1. On récupère l'ID du menu dans l'URL
$id = $_GET['id'];

// 2. On va chercher les infos de CE menu précis
$query = "SELECT * FROM menus WHERE id = $id";
$result = mysqli_query($conn, $query);
$menu = mysqli_fetch_assoc($result);

// 3. On prépare la galerie : on transforme la chaîne "img1,img2" en tableau PHP
$images_galerie = [];
if (!empty($menu['galerie'])) {
    $images_galerie = explode(',', $menu['galerie']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails - <?php echo $menu['titre']; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .galerie-container { display: flex; gap: 15px; margin-top: 20px; flex-wrap: wrap; }
        .galerie-img { width: 200px; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid var(--orange); }
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; padding: 40px 0; }
    </style>
</head>
<body>
    <main class="container">
        <div class="details-grid">
            <div>
                <img src="assets/<?php echo $menu['image']; ?>" style="width:100%; border-radius:15px;">
                
                <h3>Galerie de photos</h3>
                <div class="galerie-container">
                    <?php foreach($images_galerie as $img): ?>
                        <img src="assets/<?php echo trim($img); ?>" class="galerie-img">
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <h1><?php echo $menu['titre']; ?></h1>
                <p class="theme-badge"><?php echo $menu['theme']; ?> - <?php echo $menu['regime']; ?></p>
                <p><?php echo $menu['description']; ?></p>
                
                <hr>
                <h4>Composition :</h4>
                <ul>
                    <li><strong>Entrées :</strong> <?php echo $menu['entrees']; ?></li>
                    <li><strong>Plats :</strong> <?php echo $menu['plats']; ?></li>
                    <li><strong>Desserts :</strong> <?php echo $menu['desserts']; ?></li>
                </ul>

                <hr>
                <p><strong>Allergènes :</strong> <?php echo $menu['allergenes']; ?></p>
                <p><strong>Stock restant :</strong> <?php echo $menu['stock_dispo']; ?> commandes</p>
                <h2 style="color:var(--orange)"><?php echo $menu['prix_min_pers']; ?>€ <small>/ personne</small></h2>
                <p><small>(Minimum <?php echo $menu['nb_pers_min']; ?> personnes)</small></p>
            </div>
        </div>
    </main>

    </body>
</html>
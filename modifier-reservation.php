<?php
session_start();
// Sécurité Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: connexion.php');
    exit();
}

require_once('config.php');

// Récupération de l'ID depuis l'URL
if(!isset($_GET['id'])) { header('Location: admin-reservations.php'); exit(); }
$id_res = $_GET['id'];

// Requête pour les détails de la réservation
$sql = "SELECT r.*, m.titre 
        FROM reservations r 
        LEFT JOIN menus m ON r.id_menu = m.id 
        WHERE r.id = $id_res";
$result = mysqli_query($conn, $sql);
$res = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer la réservation | Administrateur</title>
    <link rel="stylesheet" href="css/admin.css"> 
    <style>
        /* Styles spécifiques pour la carte centrale tout en respectant ton design */
        html, body { height: 100%; margin: 0; background-color: #fffaf0; }
        body { display: flex; flex-direction: column; }
        .main-content { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 0; }

        .admin-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        .admin-box h1 { color: #2c3e50; font-family: 'serif'; margin-bottom: 25px; }
        .details-client { text-align: left; margin-bottom: 20px; color: #333; line-height: 1.6; }
        
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 15px 0;
            font-size: 16px;
        }
        .btn-submit {
            background: #27ae60;
            color: white;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }
        .btn-submit:hover { background: #219150; }
        .link-annuler { display: block; margin-top: 15px; color: #800020; text-decoration: underline; font-size: 14px; }
    </style>
</head>
<body>

    <header class="main-header" style="border-bottom: 2px solid #800020;">
        <nav class="navbar">
    <div class="nav-links">
        <a href="index.php">🏠 Site</a>
        <a href="admin-menus.php">🍴 Menus</a>
        <a href="admin-reservations.php">📅 Réservations</a>
        <a href="admin-users.php" class="active">👥 Utilisateurs</a>
    </div>
    <div class="nav-admin-info">ADMINISTRATEUR</div>
</nav>
    </header>

    <main class="main-content">
        <div class="admin-box">
            <h1>Traiter la réservation</h1>
            
            <div class="details-client">
                <strong>Client :</strong> <?php echo htmlspecialchars($res['nom_client']); ?><br>
                <strong>Menu :</strong> <?php echo htmlspecialchars($res['titre']); ?><br>
                <strong>Date :</strong> <?php echo date('d/m/Y', strtotime($res['date_evenement'])); ?>
            </div>

            <form action="traitement-modifier-reservation.php" method="POST">
                <input type="hidden" name="id_res" value="<?php echo $res['id']; ?>">
                
                <label>Changer le statut :</label>
                <select name="statut" required>
                     <option value="En attente">En attente</option>
                     <option value="Accepté">Accepté</option>
                     <option value="En préparation">En préparation</option>
                     <option value="En cours de livraison">En cours de livraison</option>
                     <option value="Livré">Livré</option>
                     <option value="En attente retour matériel">En attente retour matériel</option>
                     <option value="Terminée">Terminée</option>
                     <option value="Annulée">Annulée</option>
                </select>

                <button type="submit" class="btn-submit">Mettre à jour</button>
                <a href="admin-reservations.php" class="link-annuler">Annuler</a>
            </form>
        </div>
    </main>

    <footer class="main-footer" style="background: white; border-top: 1px solid #eee; padding: 20px; text-align: center;">
        <p>© 2024 Vite & Gourmand - Interface d'administration</p>
    </footer>

</body>
</html>
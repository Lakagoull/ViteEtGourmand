<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: connexion.php'); exit(); }

require_once('config.php');
$user_id = $_SESSION['user_id'];

// 1. On récupère les infos actuelles de l'utilisateur
$user_query = mysqli_query($conn, "SELECT * FROM utilisateurs WHERE id = $user_id");
$user_data = mysqli_fetch_assoc($user_query);

// 2. On récupère ses réservations
$res_query = mysqli_query($conn, "SELECT r.*, m.titre FROM reservations r LEFT JOIN menus m ON r.id_menu = m.id WHERE r.email_client = '{$user_data['email']}' ORDER BY r.date_evenement DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace Client | Vite & Gourmand</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/menus.css">
    <style>
        /* --- LA SOLUTION POUR LE FOOTER --- */
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1; /* Pousse le footer en bas */
        }
        /* ---------------------------------- */

        .account-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 40px; max-width: 1200px; margin: 50px auto; padding: 20px; }
        .profile-card, .history-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); height: fit-content; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .btn-update { background: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; width: 100%; font-weight: bold; transition: 0.3s; }
        .btn-update:hover { background: #219150; }
        .res-item { border-left: 4px solid #800020; padding: 15px; background: #f9f9f9; margin-bottom: 15px; border-radius: 0 8px 8px 0; display: flex; justify-content: space-between; align-items: center; }
        
        /* Styles des Badges mis à jour pour TOUS les statuts du sujet */
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; white-space: nowrap; display: inline-block; }
        .status-en-attente { background: #fff9e6; color: #f39c12; }
        .status-accepté { background: #e3f2fd; color: #2196f3; }
        .status-en-préparation { background: #f3e5f5; color: #9c27b0; }
        .status-en-cours-de-livraison { background: #fff3e0; color: #ff9800; }
        .status-livré { background: #e0f2f1; color: #009688; }
        .status-en-attente-du-retour-de-matériel { background: #ffebee; color: #f44336; }
        .status-terminée { background: #eafaf1; color: #2ecc71; }
        .status-annulée { background: #fce4e4; color: #c0392b; }
    </style>
</head>
<body>

<div class="main-content">
    <header class="main-header">
        <nav class="navbar">
            <div class="logo"><img src="assets/Logo.png" alt="Logo"></div>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="menus.php">Menus</a></li>
                <li><a href="reservation.php">Réservation</a></li>
                <li><a href="contact.php">Contact</a></li>
                
                <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'employe')): ?>
                    <li>
                        <a href="admin-reservations.php" style="color: var(--orange); font-weight: bold;">
                            🔧 <?php echo ($_SESSION['role'] === 'admin') ? 'Admin' : 'Gestion'; ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="nav-auth">
                <a href="deconnexion.php" class="logout-link">Déconnexion</a>
            </div>
        </nav>
    </header>

    <?php if(isset($_GET['success'])): ?>
        <div style="max-width: 1200px; margin: 20px auto; padding: 15px; background: #d4edda; color: #155724; border-radius: 8px; text-align: center; font-weight: bold;">
            ✅ Votre profil a été mis à jour avec succès !
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['msg']) && $_GET['msg'] === 'annule_ok'): ?>
        <div style="max-width: 1200px; margin: 20px auto; padding: 15px; background: #d4edda; color: #155724; border-radius: 8px; text-align: center; font-weight: bold;">
            ❌ La commande a été annulée avec succès et le stock a été mis à jour.
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['error']) && $_GET['error'] === 'deja_acceptee'): ?>
        <div style="max-width: 1200px; margin: 20px auto; padding: 15px; background: #f8d7da; color: #721c24; border-radius: 8px; text-align: center; font-weight: bold;">
            ⚠️ Impossible d'annuler cette commande, elle a déjà été prise en charge par notre équipe.
        </div>
    <?php endif; ?>

    <div class="account-grid">
        <aside class="profile-card">
            <h2 style="font-family: 'Playfair Display'; margin-bottom: 20px;">Mes Informations</h2>
            <form action="traitement-profil.php" method="POST">
                <div class="form-group">
                    <label>Nom complet</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($user_data['nom']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                </div>
                <p style="font-size: 12px; color: #666; margin-top: 20px;">Pour modifier votre mot de passe, remplissez le champ ci-dessous :</p>
                <div class="form-group">
                    <label>Nouveau mot de passe (optionnel)</label>
                    <input type="password" name="new_password" placeholder="Laisser vide pour ne pas changer">
                </div>
                <button type="submit" class="btn-update">Mettre à jour mon profil</button>
            </form>
        </aside>

        <section class="history-card">
            <h2 style="font-family: 'Playfair Display'; margin-bottom: 20px;">Mon Historique de Réservations</h2>
            
            <?php if(mysqli_num_rows($res_query) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($res_query)): ?>
                    <div class="res-item">
                        <div>
                            <strong style="color: #800020;"><?php echo htmlspecialchars($row['titre']); ?></strong><br>
                            <small>📅 <?php echo date('d/m/Y', strtotime($row['date_evenement'])); ?> — 👥 <?php echo $row['nb_personnes']; ?> pers.</small>
                            <br>
                            <small style="font-weight: bold; color: #333;">💰 Prix total : <?php echo number_format($row['prix_total'], 2, ',', ' '); ?> €</small>
                            
                            <br><br>
                            <a href="detail-reservation.php?id=<?php echo $row['id']; ?>" style="color: #800020; font-weight: bold; text-decoration: none; font-size: 13px; margin-right: 15px;">
                                📜 Détails & Paiement
                            </a>

                            <a href="suivi-commande.php?id=<?php echo $row['id']; ?>" style="color: #2980b9; font-weight: bold; text-decoration: none; font-size: 13px; margin-right: 15px;">
                                👁️ Suivre ma commande
                            </a>

                            <?php if ($row['statut'] === 'En attente'): ?>
                                <a href="annuler-commande.php?id=<?php echo $row['id']; ?>" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?');" 
                                   style="color: #e74c3c; font-weight: bold; text-decoration: none; font-size: 13px;">
                                   ❌ Annuler
                                </a>
                            <?php endif; ?>
                        </div>
                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $row['statut'])); ?>">
                            <?php echo htmlspecialchars($row['statut']); ?>
                        </span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Aucune réservation pour le moment.</p>
            <?php endif; ?>
        </section>
    </div>
</div> 

<footer class="main-footer">
    <div class="container footer-grid">
        <div class="footer-about">
            <h3>Vite&Gourmand</h3>
            <p>Votre traiteur bordelais d'exception pour tous vos événements.</p>
        </div>
        <div class="footer-hours">
            <h4>Nos Horaires</h4>
            <p>Lundi au Dimanche<br>08h00 — 22h00</p>
        </div>
        <div class="footer-links">
            <h4>Informations</h4>
            <ul>
                <li><a href="mentions-legales.php">Mentions Légales</a></li>
                <li><a href="cgv.php">CGV</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2024 Vite & Gourmand. Tous droits réservés.</p>
    </div>
</footer>

</body>
</html>
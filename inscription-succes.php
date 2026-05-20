<?php 
session_start(); 
$prenom = isset($_GET['prenom']) ? htmlspecialchars($_GET['prenom']) : 'Client';
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Réussie | Vite & Gourmand</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/inscription.css">
</head>
<body>

    <header class="main-header">
        <nav class="navbar">
            <div class="logo"><img src="assets/Logo.png" alt="Logo"></div>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="menus.php">Menus</a></li>
                <li><a href="reservation.php">Réservation</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main class="login-container" style="margin-top: 50px; margin-bottom: 50px;">
        <div class="login-card" style="max-width: 500px; margin: 0 auto; text-align: center; padding: 40px;">
            <div style="font-size: 50px; margin-bottom: 20px;">🎉</div>
            <h1 style="color: #800020; font-family: 'Playfair Display', serif; margin-bottom: 15px;">Inscription réussie !</h1>
            <p style="font-size: 16px; color: #555; line-height: 1.6; margin-bottom: 30px;">
                Bienvenue <strong><?php echo $prenom; ?></strong> ! <br>
                Un e-mail de confirmation est en cours d'envoi à l'adresse <span style="color: #800020; font-weight: bold;"><?php echo $email; ?></span>.
            </p>
            
            <a href="connexion.php" class="btn-orange" style="display: block; text-decoration: none; text-align: center; padding: 12px; font-weight: bold;">
                Se connecter à mon compte
            </a>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container footer-grid">
            <div class="footer-about">
                <h3>Vite&Gourmand</h3>
                <p>Votre traiteur bordelais d'exception pour tous vos événements.</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2024 Vite & Gourmand. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
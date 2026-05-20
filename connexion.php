<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | Vite & Gourmand</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/connexion.css">
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
    
    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <li><a href="admin-menus.php" style="color: var(--orange); font-weight: bold;">🔧 Admin</a></li>
    <?php endif; ?>
</ul>
     <div class="nav-auth">
            <?php if(isset($_SESSION['prenom'])): ?>
                <div class="user-control">
                    <a href="mon-compte.php" class="user-badge">
                        <?php echo htmlspecialchars($_SESSION['prenom']); ?>
                    </a>
                    <a href="deconnexion.php" class="logout-link">Déconnexion</a>
                </div>
            <?php else: ?>
                <a href="connexion.php" class="btn-connexion">Connexion</a>
            <?php endif; ?>
        </div>
        </nav>
    </header>

    <main class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Bon retour !</h1>
                <p>Connectez-vous pour accéder à votre espace.</p>
            </div>
<?php if(isset($_GET['error'])): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-size: 14px;">
        <?php 
            if($_GET['error'] == 'mdp') echo "Mot de passe incorrect.";
            if($_GET['error'] == 'email') echo "Aucun compte trouvé avec cet email.";
        ?>
    </div>
<?php endif; ?>
            <form action="traitement-connexion.php" method="POST">
                <div class="form-group">
                    <label for="email">Adresse Email</label>
                    <input type="email" id="email" name="email" placeholder="votre@email.com" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>

                <div class="login-options">
                    <label class="checkbox-container">
                        <input type="checkbox" name="remember"> Se souvenir de moi
                    </label>
                    <a href="mdp-oublie.php" class="forgot-pass">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="btn-orange w-100">Se connecter</button>
            </form>

            <div class="login-footer">
                <p>Nouveau ici ? <a href="inscription.php">Créer un compte</a></p>
            </div>
        </div>
    </main>

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
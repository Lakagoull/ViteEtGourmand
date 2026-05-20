<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription | Vite & Gourmand</title>
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
        <div class="login-card" style="max-width: 600px;"> <div class="login-header">
                <h1>Créer un compte</h1>
                <p>Rejoignez Vite & Gourmand pour commander vos repas.</p>
            </div>

            <form action="traitement-inscription.php" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" name="nom" id="nom" placeholder="Votre nom" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom">Prénom</label>
                        <input type="text" name="prenom" id="prenom" placeholder="Votre prénom" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Adresse Email</label>
                    <input type="email" name="email" id="email" placeholder="votre@email.com" required>
                </div>

                <div class="form-group">
                    <label for="tel">Téléphone (GSM)</label>
                    <input type="tel" name="tel" id="tel" placeholder="06 00 00 00 00" required>
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse de livraison</label>
                    <input type="text" name="adresse" id="adresse" placeholder="15 Rue de la Paix, Bordeaux" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password" placeholder="8 caractères minimum" required>
                </div>

                <button type="submit" class="btn-orange w-100">S'inscrire</button>
            </form>

            <div class="login-footer">
                <p>Déjà un compte ? <a href="connexion.html">Se connecter</a></p>
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
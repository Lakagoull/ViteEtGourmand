<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-nous | Vite & Gourmand</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/contact.css">
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
    
    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'employe')): ?>
        <li>
            <a href="admin-reservations.php" style="color: var(--orange); font-weight: bold;">
                🔧 <?php echo ($_SESSION['role'] === 'admin') ? 'Admin' : 'Gestion'; ?>
            </a>
        </li>
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
    <main class="container" style="min-height: 80vh;">
        <section class="contact-section">
            <div class="contact-grid">
                
                <div class="contact-info">
                    <h1>Contactez-nous</h1>
                    <p>Une question pour un événement ? Julie et José vous répondent sous 24h.</p>
                    
                    <div class="info-item">
                        <span>📍</span>
                        <p>15 Rue Gastronomique, 33000 Bordeaux</p>
                    </div>
                    <div class="info-item">
                        <span>📞</span>
                        <p>05 56 00 00 00</p>
                    </div>
                    <div class="info-item">
                        <span>✉️</span>
                        <p>contact@viteetgourmand.fr</p>
                    </div>
                </div>

                <div class="contact-form">
                    <form action="#">
                        <div class="form-group">
                            <label for="name">Nom complet</label>
                            <input type="text" id="name" placeholder="Votre nom" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" placeholder="votre@email.com" required>
                        </div>

                        <div class="form-group">
                            <label for="message">Votre message</label>
                            <textarea id="message" rows="5" placeholder="Comment pouvons-nous vous aider ?" required></textarea>
                        </div>

                        <button type="submit" class="btn-orange">Envoyer le message</button>
                    </form>
                </div>

            </div>
        </section>
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
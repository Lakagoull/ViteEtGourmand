<?php 
session_start(); 
require_once('config.php'); // Connexion à la base de données

// Récupération des 3 derniers avis autorisés (valide = 1)
$sql_avis = "SELECT * FROM avis WHERE valide = 1 ORDER BY id DESC LIMIT 3";
$result_avis = mysqli_query($conn, $sql_avis);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vite & Gourmand | Traiteur Bordeaux</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="main-header">
    <nav class="navbar">
        <div class="logo">
            <img src="assets/Logo.png" alt="Logo">
        </div>
        
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

<main>
    <section class="hero">
        <div class="hero-content">
            <h1>L’excellence gastronomique à Bordeaux depuis 25 ans</h1>
            <p>Des menus de saison et une équipe passionnée pour vos événements</p>
            <a href="menus.php" class="btn-orange">Découvrir nos menus</a>
        </div>
    </section>

    <section class="presentation">
        <div class="container presentation-flex">
            <div class="presentation-img">
                <img src="assets/julie et son gas.png" alt="Julie et José en cuisine">
            </div>
            <div class="presentation-text">
                <h2>Vite & Gourmand : 25 ans de passion</h2>
                <p>Julie et José vous accueillent à Bordeaux pour transformer vos repas de fêtes en moments inoubliables. Que ce soit pour Noël, Pâques ou un événement classique, nos menus évoluent au fil des saisons pour vous offrir le meilleur de notre savoir-faire artisanal.</p>
            </div>
        </div>
    </section>

    <section class="arguments">
        <div class="container">
            <div class="arguments-grid">
                <div class="arg-card">
                    <div class="arg-icon">📅</div>
                    <h3>25 Ans</h3>
                    <p>D'expertise culinaire à Bordeaux et sa région.</p>
                </div>
                <div class="arg-card">
                    <div class="arg-icon">🥗</div>
                    <h3>100% Frais</h3>
                    <p>Des produits locaux sélectionnés chaque matin au marché.</p>
                </div>
                <div class="arg-card">
                    <div class="arg-icon">👨‍🍳</div>
                    <h3>Sur Mesure</h3>
                    <p>Des menus adaptés à tous vos régimes alimentaires.</p>
                </div>
                <div class="arg-card">
                    <div class="arg-icon">⭐</div>
                    <h3>Qualité Pro</h3>
                    <p>Équipements et service conformes aux normes HACCP.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="reviews">
        <div class="container">
            <h2 class="section-title">Ce que nos clients dicen de nous</h2>
            <div class="reviews-grid">
                
                <?php if ($result_avis && mysqli_num_rows($result_avis) > 0): ?>
                    <?php while($row_avis = mysqli_fetch_assoc($result_avis)): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <span class="user-icon">👤</span>
                                <div class="user-info">
                                    <strong><?php echo htmlspecialchars($row_avis['nom_client']); ?></strong>
                                    <div class="stars">
                                        <?php
                                        $note = intval($row_avis['note']);
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo ($i <= $note) ? '★' : '☆';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <p>"<?php echo nl2br(htmlspecialchars($row_avis['commentaire'])); ?>"</p>
                            <span class="badge-verifie">Avis vérifié</span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="review-card">
                        <div class="review-header">
                            <span class="user-icon">👤</span>
                            <div class="user-info">
                                <strong>Jean-Marc L.</strong>
                                <div class="stars">★★★★★</div>
                            </div>
                        </div>
                        <p>"Une prestation de haute volée pour notre repas de Noël. Julie et José sont adorables !"</p>
                        <span class="badge-verifie">Avis vérifié</span>
                    </div>
                    <div class="review-card">
                        <div class="review-header">
                            <span class="user-icon">👤</span>
                            <div class="user-info">
                                 <strong>Sophie R.</strong>
                                 <div class="stars">★★★★☆</div>
                            </div>
                        </div>
                        <p>"Tout était frais, élégant et surtout délicieux. Mention spéciale pour les mini pâtisseries !"</p>
                        <span class="badge-verifie">Avis vérifié</span>
                    </div>
                    <div class="review-card">
                        <div class="review-header">
                            <span class="user-icon">👤</span>
                            <div class="user-info">
                                 <strong>Antoine B.</strong>
                                 <div class="stars">★★★★☆</div>
                            </div>
                        </div>
                        <p>"La qualité gastronomique est constante et le service de livraison à Bordeaux est très ponctuel."</p>
                        <span class="badge-verifie">Avis vérifié</span>
                    </div>
                <?php endif; ?>

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
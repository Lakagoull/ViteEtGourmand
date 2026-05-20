<?php
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
// Connexion à la base de données
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "vite_gourmand";
require_once('config.php');

// On récupère tous les menus
$sql = "SELECT * FROM menus";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Menus de Saison | Vite & Gourmand</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/menus.css">
    <style>
        /* Styles rapides pour les boutons ajoutés */
        .btn-reserver-grid {
            flex: 1;
            text-align: center;
            text-decoration: none;
            background-color: #800020; /* Bordeaux */
            color: white;
            border-radius: 5px;
            font-weight: bold;
            padding: 10px;
            font-size: 14px;
            transition: 0.3s;
        }
    </style>
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

    <main class="container">
        <section class="menu-intro">
            <h1>Nos Menus de Saison</h1>
            <p>Découvrez les créations de Julie & José, élaborées avec des produits frais du marché bordelais.</p>
        </section>

        <section class="filters-section container">
            <div class="search-filter-bar">
                <div class="search-input">
                    <input type="text" id="searchName" placeholder="Rechercher un menu...">
                </div>

                <select id="filterTheme" class="filter-select">
                    <option value="all">Tous les thèmes</option>
                    <option value="Noël">Noël</option>
                    <option value="Pâques">Pâques</option>
                    <option value="Classique">Classique</option>
                    <option value="Évènement">Évènement</option>
                </select>

                <select id="filterRegime" class="filter-select">
                    <option value="all">Tous les régimes</option>
                    <option value="Classique">Classique</option>
                    <option value="Végétarien">Végétarien</option>
                    <option value="Vegan">Vegan</option>
                </select>

                <div class="filter-price">
                    <label for="filterPrice">Prix max : <span id="priceValue">100</span>€</label>
                    <input type="range" id="filterPrice" min="0" max="200" step="5" value="100">
                </div>
            </div>
        </section>

        <div class="menu-grid">
            <?php while($menu = mysqli_fetch_assoc($result)): ?>
                <div class="menu-card" 
                     data-price="<?php echo $menu['prix_min_pers']; ?>" 
                     data-theme="<?php echo $menu['theme']; ?>" 
                     data-regime="<?php echo $menu['regime']; ?>">
                    
                    <div class="card-img">
                        <img src="assets/<?php echo $menu['image'] ? $menu['image'] : 'default-menu.jpg'; ?>" alt="<?php echo $menu['titre']; ?>">
                        <span class="badge-theme"><?php echo $menu['theme']; ?></span>
                    </div>

                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($menu['titre']); ?></h3>
                        <p class="description"><?php echo htmlspecialchars($menu['description']); ?></p>
                        
                        <div class="menu-info-bulles">
                            <span>👥 Min. <?php echo $menu['nb_pers_min']; ?> pers.</span>
                            <span class="stock-info">📦 Stock : <?php echo $menu['stock_dispo']; ?></span>
                        </div>

                        <span class="menu-price"><?php echo $menu['prix_min_pers']; ?>€ <small>/ pers.</small></span>
                        
                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                            <button type="button" class="btn-details" style="flex: 1;" onclick='openModal(<?php echo json_encode($menu, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                Détails
                            </button>
                            <a href="reservation.php?id_menu=<?php echo $menu['id']; ?>" class="btn-reserver-grid">
                                Réserver
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <div id="menuModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div id="modalBody"></div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchName = document.getElementById('searchName');
    const filterTheme = document.getElementById('filterTheme');
    const filterRegime = document.getElementById('filterRegime');
    const filterPrice = document.getElementById('filterPrice');
    const priceValue = document.getElementById('priceValue');
    const cards = document.querySelectorAll('.menu-card');

    function filterMenus() {
        const nameQuery = searchName.value.toLowerCase();
        const themeQuery = filterTheme.value;
        const regimeQuery = filterRegime.value;
        const maxPrice = parseFloat(filterPrice.value);

        priceValue.textContent = maxPrice;

        cards.forEach(card => {
            const cardName = card.querySelector('h3').textContent.toLowerCase();
            const cardTheme = card.getAttribute('data-theme');
            const cardRegime = card.getAttribute('data-regime');
            const cardPrice = parseFloat(card.getAttribute('data-price'));

            const matchesName = cardName.includes(nameQuery);
            const matchesTheme = (themeQuery === 'all' || cardTheme === themeQuery);
            const matchesRegime = (regimeQuery === 'all' || cardRegime === regimeQuery);
            const matchesPrice = (cardPrice <= maxPrice);

            if (matchesName && matchesTheme && matchesRegime && matchesPrice) {
                card.style.display = 'block';
                card.style.opacity = '1';
            } else {
                card.style.display = 'none';
                card.style.opacity = '0';
            }
        });
    }

    searchName.addEventListener('input', filterMenus);
    filterTheme.addEventListener('change', filterMenus);
    filterRegime.addEventListener('change', filterMenus);
    filterPrice.addEventListener('input', filterMenus);
});

function openModal(menu) {
    const modal = document.getElementById("menuModal");
    const modalBody = document.getElementById("modalBody");
    
    let galerieHtml = '';
    if(menu.galerie) {
        const images = menu.galerie.split(',');
        images.forEach(img => {
            if(img.trim() !== "") {
                galerieHtml += `<img src="assets/${img.trim()}" style="width:120px; height:90px; object-fit:cover; border-radius:5px; border:1px solid #ddd;">`;
            }
        });
    }

    modalBody.innerHTML = `
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <img src="assets/${menu.image || 'default-menu.jpg'}" style="width:100%; border-radius:10px;">
            <div>
                <h2 style="color:#800000; margin-bottom:10px;">${menu.titre}</h2>
                <p><strong>Thème :</strong> ${menu.theme} | <strong>Régime :</strong> ${menu.regime}</p>
                <p style="margin:10px 0;">${menu.description}</p>
                <hr>
                <p><strong>Composition :</strong> ${menu.entrees} / ${menu.plats} / ${menu.desserts}</p>
                <p style="color:#E67E22"><strong>Allergènes :</strong> ${menu.allergenes || 'Aucun'}</p>
                <h3>Prix : ${menu.prix_min_pers}€ / pers.</h3>
                <p><small>(Min. ${menu.nb_pers_min} personnes)</small></p>
                
                <a href="reservation.php?id_menu=${menu.id}" 
                   style="display: block; background: #800020; color: white; text-align: center; padding: 12px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 15px;">
                   Réserver ce menu
                </a>
            </div>
        </div>
        <h4 style="margin-top:20px;">Galerie photos</h4>
        <div style="display:flex; gap:10px; margin-top:10px; flex-wrap:wrap;">
            ${galerieHtml || '<p>Aucune photo supplémentaire</p>'}
        </div>
    `;
    
    modal.style.display = "block";
    document.body.style.overflow = "hidden";
}

document.addEventListener('click', function(e) {
    const modal = document.getElementById("menuModal");
    if (e.target.classList.contains('close-modal') || e.target === modal) {
        modal.style.display = "none";
        document.body.style.overflow = "auto";
    }
});
</script>

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
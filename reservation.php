<?php
session_start();
require_once('config.php');

if (!$conn) {
    die("Connexion à la base de données échouée : " . mysqli_connect_error());
}

// Protection : si l'utilisateur n'est pas connecté, on le renvoie à la page de connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

// RÉSERVATION CORRIGÉE : Utilisation de tes vrais noms de colonnes 'prix_min_pers' et 'nb_pers_min'
$menus_query = mysqli_query($conn, "SELECT id, titre, prix_min_pers, nb_pers_min FROM menus");

if (!$menus_query) {
    die("Erreur dans la requête SQL : " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver | Vite & Gourmand</title>
    <link rel="stylesheet" href="css/connexion.css"> 
    <link rel="stylesheet" href="css/menus.css"> 
    <style>
        /* Conteneur principal pour forcer le footer en bas */
        .page-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content {
            flex: 1; /* Pousse le footer vers le bas */
        }
        .res-container { max-width: 800px; margin: 50px auto; padding: 20px; }
        .res-card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: span 2; }
        .btn-reserve { 
            background: #800020; color: white; border: none; padding: 15px; 
            width: 100%; border-radius: 8px; font-weight: bold; cursor: pointer;
            font-size: 18px; transition: 0.3s; margin-top: 20px;
        }
        .btn-reserve:hover { background: #deff9a; color: #1a1a1a; }
        label { font-weight: bold; color: #333; display: block; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        
        /* Alertes de validation */
        .alert { padding: 15px; border-radius: 8px; text-align: center; font-weight: bold; margin-bottom: 20px; }
        .alert-error { background: #f8d7da; color: #721c24; }
        .alert-warning { background: #fff3cd; color: #856404; }

        /* Style pour l'affichage des prix en temps réel */
        .price-summary {
            background: #f9f9f9;
            border: 2px dashed #800020;
            border-radius: 8px;
            padding: 20px;
            margin-top: 25px;
        }
        .price-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 16px;
        }
        .price-total {
            font-weight: bold;
            font-size: 18px;
            color: #800020;
            border-top: 1px solid #ddd;
            padding-top: 8px;
            margin-top: 8px;
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <div class="content">
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

        <div class="res-container">
            <div class="res-card">
                <h1 style="text-align: center; color: #800020; font-family: 'Playfair Display', serif;">Réserver une prestation</h1>
                <p style="text-align: center; margin-bottom: 30px;">Julie & José s'occupent de tout pour votre événement.</p>

                <?php if(isset($_GET['error']) && $_GET['error'] === 'stock_epuise'): ?>
                    <div class="alert alert-error">
                        ⚠️ Désolé, ce menu n'est plus disponible en stock pour le moment.
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['error']) && $_GET['error'] === 'min_convives'): ?>
                    <div class="alert alert-warning">
                        ⚠️ Le nombre de convives est insuffisant pour ce menu (Minimum requis : <?php echo isset($_GET['min']) ? intval($_GET['min']) : 'X'; ?> personnes).
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['error']) && $_GET['error'] === 'menu_introuvable'): ?>
                    <div class="alert alert-error">
                        ⚠️ Une erreur est survenue lors de la sélection du menu. Veuillez réessayer.
                    </div>
                <?php endif; ?>

                <form action="traitement-reservation.php" method="POST">
                    <div class="form-grid">
                        
                        <div class="form-group full-width">
                            <label>Choisissez votre menu</label>
                            <select name="menu_id" id="menu-select" required onchange="calculerPrix()">
                                <option value="">-- Sélectionner un menu --</option>
                                <?php while($m = mysqli_fetch_assoc($menus_query)): ?>
                                    <option value="<?php echo $m['id']; ?>" data-prix="<?php echo $m['prix_min_pers']; ?>" data-min="<?php echo $m['nb_pers_min']; ?>">
                                        <?php echo htmlspecialchars($m['titre']); ?> (<?php echo $m['prix_min_pers']; ?>€/pers)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Date de l'événement</label>
                            <input type="date" name="date_evenement" required>
                        </div>

                        <div class="form-group">
                            <label>Heure souhaitée de livraison</label>
                            <input type="time" name="heure_livraison" required>
                        </div>

                        <div class="form-group">
                            <label>Nombre de convives</label>
                            <input type="number" name="nb_pers" id="nb-pers" min="1" placeholder="Ex: 10" required oninput="calculerPrix()">
                        </div>

                        <div class="form-group">
                            <label>Zone de livraison (Ville)</label>
                            <select name="ville" required id="ville-select" onchange="toggleKm(); calculerPrix();">
                                <option value="Bordeaux">Bordeaux</option>
                                <option value="Hors-Bordeaux">Hors-Bordeaux (Frais supplémentaires)</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label>Adresse exacte de la prestation</label>
                            <input type="text" name="adresse_prestation" placeholder="Ex: 12 Rue de la Paix, 33000 Bordeaux" required>
                        </div>

                        <div class="form-group full-width" id="km-group" style="display: none;">
                            <label>Nombre de Kilomètres depuis Bordeaux (Calcul des frais de livraison)</label>
                            <input type="number" name="km" id="km-input" step="0.1" value="0" placeholder="Ex: 15.5" oninput="calculerPrix()">
                        </div>
                    </div>

                    <div class="price-summary">
                        <h3 style="color: #800020; margin-top: 0; margin-bottom: 15px;">Estimation de votre commande</h3>
                        <div class="price-line">
                            <span>Prix du menu (hors livraison) :</span>
                            <span id="display-prix-menu">0.00 €</span>
                        </div>
                        <div class="price-line">
                            <span>Frais de livraison :</span>
                            <span id="display-prix-livraison">0.00 €</span>
                        </div>
                        <div class="price-line price-total">
                            <span>Montant Total Estimé :</span>
                            <span id="display-prix-total">0.00 €</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-reserve">Confirmer la demande de réservation</button>
                </form>
            </div>
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
</div>

<script>
function toggleKm() {
    var ville = document.getElementById('ville-select').value;
    var kmGroup = document.getElementById('km-group');
    if(ville === 'Hors-Bordeaux') {
        kmGroup.style.display = 'block';
    } else {
        kmGroup.style.display = 'none';
        document.getElementById('km-input').value = 0; 
    }
}

// CALCUL DYNAMIQUE EN JAVASCRIPT
function calculerPrix() {
    var menuSelect = document.getElementById('menu-select');
    var selectedOption = menuSelect.options[menuSelect.selectedIndex];
    
    var nbPersInput = document.getElementById('nb-pers').value;
    var nbPers = nbPersInput ? parseInt(nbPersInput) : 0;
    
    var ville = document.getElementById('ville-select').value;
    
    var kmInput = document.getElementById('km-input').value;
    var km = kmInput ? parseFloat(kmInput) : 0;

    if (!selectedOption || menuSelect.value === "" || nbPers <= 0) {
        document.getElementById('display-prix-menu').innerText = "0.00 €";
        document.getElementById('display-prix-livraison').innerText = "0.00 €";
        document.getElementById('display-prix-total').innerText = "0.00 €";
        return;
    }

    // Récupération correcte des attributs liés à tes vraies colonnes BDD
    var prixUnitaire = parseFloat(selectedOption.getAttribute('data-prix'));
    var nbPersMin = parseInt(selectedOption.getAttribute('data-min'));

    // 1. Calcul du prix des menus
    var prixBaseMenu = prixUnitaire * nbPers;

    // Calcul de la remise de 10% (si convives >= min + 5)
    var remise = 0;
    if (nbPers >= (nbPersMin + 5)) {
        remise = prixBaseMenu * 0.10;
    }
    var prixMenuFinal = prixBaseMenu - remise;

    // 2. Calcul des frais de livraison
    var fraisLivraison = 5;
    if (ville === "Hors-Bordeaux") {
        fraisLivraison = 5 + (km * 0.59);
    }

    // 3. Somme totale
    var prixTotal = prixMenuFinal + fraisLivraison;

    // Affichage mis à jour en temps réel
    document.getElementById('display-prix-menu').innerText = prixMenuFinal.toFixed(2) + " €" + (remise > 0 ? " (Remise 10% incluse)" : "");
    document.getElementById('display-prix-livraison').innerText = fraisLivraison.toFixed(2) + " €";
    document.getElementById('display-prix-total').innerText = prixTotal.toFixed(2) + " €";
}
</script>
</body>
</html>
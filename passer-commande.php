<?php
session_start();
require_once('config.php');

// Sécurité : il faut être connecté
if (!isset($_SESSION['user_id'])) { header('Location: connexion.php'); exit(); }

$menu_id = $_GET['menu_id'];
$res = mysqli_query($conn, "SELECT * FROM menus WHERE id = $menu_id");
$menu = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finaliser ma commande</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .checkout-container { max-width: 800px; margin: 50px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); font-family: sans-serif; }
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        .price-box { background: #fff3e0; padding: 20px; border-radius: 8px; margin-top: 20px; border-left: 5px solid #ff9800; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .total { font-size: 20px; font-weight: bold; color: #e67e22; border-top: 1px solid #ddd; padding-top: 10px; }
        .btn-reserver { background: #ff9800; color: white; border: none; padding: 15px; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="checkout-container">
        <h1>Ma Commande : <?php echo $menu['titre']; ?></h1>
        
        <form action="traitement-reservation.php" method="POST" id="orderForm">
            <input type="hidden" name="menu_id" value="<?php echo $menu['id']; ?>">
            <input type="hidden" id="prix_unitaire" value="<?php echo $menu['prix']; ?>">
            <input type="hidden" id="min_pers" value="<?php echo $menu['nb_pers_min']; ?>">

            <div class="form-group">
                <label>Nombre de personnes (Minimum : <?php echo $menu['nb_pers_min']; ?>)</label>
                <input type="number" name="nb_pers" id="nb_pers" value="<?php echo $menu['nb_pers_min']; ?>" min="<?php echo $menu['nb_pers_min']; ?>" oninput="calculerPrix()">
            </div>

            <div class="form-group">
                <label>Date de la prestation</label>
                <input type="date" name="date_evenement" required>
            </div>

            <div class="form-group">
                <label>Heure souhaitée de livraison</label>
                <input type="time" name="heure_livraison" required>
            </div>

            <div class="form-group">
                <label>Ville de livraison</label>
                <select name="ville" id="ville" onchange="calculerPrix()">
                    <option value="Bordeaux">Bordeaux (Gratuit)</option>
                    <option value="Hors-Bordeaux">Hors Bordeaux (+5€ + 0.59€/km)</option>
                </select>
            </div>

            <div class="form-group" id="km_div" style="display:none;">
                <label>Distance estimée depuis Bordeaux (km)</label>
                <input type="number" name="km" id="km" value="0" min="0" oninput="calculerPrix()">
            </div>

            <div class="form-group">
                <label>Adresse complète de la prestation</label>
                <textarea name="adresse_prestation" required placeholder="Ex: 12 Rue des Lilas, 33000 Bordeaux" style="height: 60px; resize: none;"></textarea>
            </div>

            <div class="price-box">
                <h3>Détail du prix</h3>
                <div class="summary-item">
                    <span>Prix Menu :</span>
                    <span><span id="display_prix_menu">0.00</span>€</span>
                </div>
                <div class="summary-item" id="remise_div" style="color: green; display:none;">
                    <span>Réduction 10% (Offre +5 pers) :</span>
                    <span><span id="display_remise">-0.00</span>€</span>
                </div>
                <div class="summary-item">
                    <span>Frais de livraison :</span>
                    <span><span id="display_livraison">0.00</span>€</span>
                </div>
                <div class="summary-item total">
                    <span>TOTAL À PAYER :</span>
                    <span><span id="display_total">0.00</span>€</span>
                </div>
            </div>

            <button type="submit" class="btn-reserver" style="width:100%; margin-top:20px;">Confirmer la commande</button>
        </form>
    </div>

<script>
function calculerPrix() {
    let prixU = parseFloat(document.getElementById('prix_unitaire').value);
    let nbPers = parseInt(document.getElementById('nb_pers').value);
    let minPers = parseInt(document.getElementById('min_pers').value);
    let ville = document.getElementById('ville').value;
    let km = parseFloat(document.getElementById('km').value) || 0;

    // 1. Prix de base
    let prixBase = prixU * nbPers;

    // 2. Réduction 10% si nbPers >= minPers + 5
    let remise = 0;
    if (nbPers >= (minPers + 5)) {
        remise = prixBase * 0.10;
        document.getElementById('remise_div').style.display = 'flex';
    } else {
        document.getElementById('remise_div').style.display = 'none';
    }

    // 3. Frais de livraison
    let livraison = 0;
    if (ville === "Hors-Bordeaux") {
        document.getElementById('km_div').style.display = 'block';
        livraison = 5 + (km * 0.59);
    } else {
        document.getElementById('km_div').style.display = 'none';
        livraison = 0;
    }

    let total = (prixBase - remise) + livraison;

    // Affichage
    document.getElementById('display_prix_menu').innerText = prixBase.toFixed(2);
    document.getElementById('display_remise').innerText = "-" + remise.toFixed(2);
    document.getElementById('display_livraison').innerText = livraison.toFixed(2);
    document.getElementById('display_total').innerText = total.toFixed(2);
}

// Lancer le calcul au chargement
calculerPrix();
</script>
</body>
</html>
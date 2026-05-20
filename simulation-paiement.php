<?php
session_start();

// Force l'affichage des erreurs pour voir précisément ce qui coince si ça persiste
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('config.php');

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id']) || !isset($_GET['id']) || !isset($_GET['methode'])) {
    header('Location: mon-compte.php');
    exit();
}

$user_id = intval($_SESSION['user_id']);
$reservation_id = intval($_GET['id']);
$methode = $_GET['methode']; // "Carte Bancaire" ou "PayPal"

// --- REQUÊTE SÉCURISÉE AVEC JOINTURE ---
$query_string = "SELECT r.*, m.prix_min_pers 
                 FROM reservations r 
                 JOIN menus m ON r.id_menu = m.id 
                 WHERE r.id = $reservation_id AND r.email_client = (SELECT email FROM utilisateurs WHERE id = $user_id)";

$query = mysqli_query($conn, $query_string);

if (!$query) {
    die("Erreur dans la requête SQL : " . mysqli_error($conn));
}

$res = mysqli_fetch_assoc($query);

if (!$res) {
    die("Erreur : Réservation introuvable ou vous n'avez pas l'autorisation d'accéder à ce paiement.");
}

// --- RECALCUL DU PRIX EN SÉCURITÉ ---
$prix_unitaire = isset($res['prix_min_pers']) ? floatval($res['prix_min_pers']) : 0;
$nb_personnes = isset($res['nb_personnes']) ? intval($res['nb_personnes']) : 0;
$prix_base_menu = $prix_unitaire * $nb_personnes;

// Remise de 10% si strictement plus de 5 personnes
$remise = 0;
if ($nb_personnes > 5) {
    $remise = $prix_base_menu * 0.10;
}
$prix_menu_final = $prix_base_menu - $remise;

// Calcul du montant final
$frais_livraison = isset($res['frais_livraison']) ? floatval($res['frais_livraison']) : 0;
$montant = isset($res['prix_total']) ? floatval($res['prix_total']) : 0;

// Si le montant stocké est erroné, on force le montant recalculé
if ($montant <= $frais_livraison) {
    $montant = $prix_menu_final + $frais_livraison;
}

$simulation_reussie = false;

// --- VALIDATION DU FORMULAIRE DE PAIEMENT FICTIF ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $methode_db = mysqli_real_escape_string($conn, $methode);
    
    // On met à jour le moyen de paiement, le statut et on répare le prix total erroné
    $update_query = "UPDATE reservations 
                     SET moyen_paiement = '$methode_db', 
                         statut = 'Accepté', 
                         prix_total = $montant 
                     WHERE id = $reservation_id";
    
    if (mysqli_query($conn, $update_query)) {
        mysqli_query($conn, "INSERT INTO historique_statuts (id_reservation, statut, date_modification) VALUES ($reservation_id, 'Payé via $methode_db', NOW())");
        $simulation_reussie = true;
        header("Refresh:3; url=mon-compte.php");
    } else {
        die("Erreur lors de la mise à jour de la commande : " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passerelle de Paiement Sécurisée (Simulation)</title>
    <link rel="stylesheet" href="css/menus.css">
    <style>
        body { background: #f4f7f6; font-family: 'Poppins', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .payment-container { background: white; max-width: 450px; width: 100%; padding: 30px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; }
        .logo-pay { font-size: 24px; font-weight: bold; margin-bottom: 20px; color: <?php echo ($methode === 'PayPal') ? '#003087' : '#800020'; ?>; }
        .amount-box { background: #fdf2f4; padding: 15px; border-radius: 8px; font-size: 20px; font-weight: bold; color: #800020; margin-bottom: 20px; }
        .paypal-box { background: #f2f5f8; color: #003087; }
        .form-pay-group { text-align: left; margin-bottom: 15px; }
        .form-pay-group label { display: block; font-size: 13px; font-weight: bold; margin-bottom: 5px; color: #555; }
        .form-pay-group input { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 15px; }
        .btn-pay { width: 100%; padding: 14px; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; color: white; transition: 0.2s; background: <?php echo ($methode === 'PayPal') ? '#0070ba' : '#800020'; ?>; }
        .btn-pay:hover { opacity: 0.9; }
        .success-screen { color: #27ae60; }
        .success-screen h2 { margin-bottom: 10px; }
        .inline-inputs { display: flex; gap: 10px; }
    </style>
</head>
<body>

<div class="payment-container">
    
    <?php if ($simulation_reussie): ?>
        <div class="success-screen">
            <span style="font-size: 50px;">✅</span>
            <h2>Paiement Autorisé !</h2>
            <p>Merci, votre règlement de <strong><?php echo number_format($montant, 2, ',', ' '); ?> €</strong> a été validé avec succès.</p>
            <p style="font-size: 13px; color: #666; font-style: italic; margin-top: 20px;">Redirection vers votre espace client...</p>
        </div>
    <?php else: ?>

        <?php if ($methode === "Carte Bancaire"): ?>
            <div class="logo-pay">💳 Paiement par Carte Bancaire</div>
            <div class="amount-box">Montant à débiter : <?php echo number_format($montant, 2, ',', ' '); ?> €</div>
            
            <p style="font-size: 12px; color: #7f8c8d; margin-bottom: 20px;">ℹ️ *Mode Simulation : Vous pouvez entrer n'importe quel numéro de carte factice pour tester.*</p>
            
            <form action="" method="POST">
                <div class="form-pay-group">
                    <label>Nom du titulaire de la carte</label>
                    <input type="text" placeholder="M. Jean Dupont" required>
                </div>
                <div class="form-pay-group">
                    <label>Numéro de carte</label>
                    <input type="text" placeholder="4970 1234 5678 9012" maxlength="19" required>
                </div>
                <div class="inline-inputs">
                    <div class="form-pay-group" style="flex: 2;">
                        <label>Date d'expiration</label>
                        <input type="text" placeholder="MM/AA" maxlength="5" required>
                    </div>
                    <div class="form-pay-group" style="flex: 1;">
                        <label>Cryptogramme (CVV)</label>
                        <input type="text" placeholder="123" maxlength="3" required>
                    </div>
                </div>
                <button type="submit" class="btn-pay">Confirmer le paiement de <?php echo number_format($montant, 2, ',', ' '); ?> €</button>
            </form>

        <?php else: ?>
            <div class="logo-pay" style="color: #0070ba;"><i>PayPal</i></div>
            <div class="amount-box paypal-box">Montant : <?php echo number_format($montant, 2, ',', ' '); ?> €</div>
            
            <p style="font-size: 12px; color: #7f8c8d; margin-bottom: 20px;">ℹ️ *Mode Simulation : Connectez-vous avec un compte fictif pour valider.*</p>
            
            <form action="" method="POST">
                <div class="form-pay-group">
                    <label>Adresse email PayPal</label>
                    <input type="email" placeholder="test-acheteur@paypal.com" required>
                </div>
                <div class="form-pay-group">
                    <label>Mot de passe</label>
                    <input type="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-pay">Payer avec PayPal</button>
            </form>
        <?php endif; ?>
        
        <br>
        <a href="detail-reservation.php?id=<?php echo $reservation_id; ?>" style="color: #555; text-decoration: underline; font-size: 13px;">Annuler et retourner au récapitulatif</a>

    <?php endif; ?>

</div>

</body>
</html>
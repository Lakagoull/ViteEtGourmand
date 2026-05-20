<?php
session_start();

// Affichage des erreurs au cas où
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('config.php');

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

// Sécurité : l'utilisateur doit être connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

$user_id = intval($_SESSION['user_id']);

// Vérifier qu'un ID de réservation est bien fourni
if (!isset($_GET['id'])) {
    header('Location: mon-compte.php');
    exit();
}

$reservation_id = intval($_GET['id']);

// --- TRAITEMENT DU FORMULAIRE DE PAIEMENT (AVEC REDIRECTION) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['moyen_paiement'])) {
    $moyen_paiement = $_POST['moyen_paiement'];
    
    if ($moyen_paiement === "Espèces") {
        // Pour les espèces, on valide directement en BDD comme avant
        $moyen_sec = mysqli_real_escape_string($conn, $moyen_paiement);
        $update_pay = "UPDATE reservations SET moyen_paiement = '$moyen_sec', statut = 'Accepté' WHERE id = $reservation_id AND user_id = $user_id";
        
        if (mysqli_query($conn, $update_pay)) {
            mysqli_query($conn, "INSERT INTO historique_statuts (id_reservation, statut, date_modification) VALUES ($reservation_id, 'Payé en espèces / Accepté', NOW())");
            $success_msg = "Réservation validée ! Règlement prévu en espèces à la livraison.";
            // On rafraîchit la page pour afficher les modifications
            header("Refresh:2; url=detail-reservation.php?id=".$reservation_id);
        } else {
            $error_msg = "Erreur : " . mysqli_error($conn);
        }
    } else {
        // Pour Carte Bancaire ou PayPal, on redirige vers la page de simulation
        // On passe l'ID de la résa et le type de paiement dans l'URL
        header("Location: simulation-paiement.php?id=" . $reservation_id . "&methode=" . urlencode($moyen_paiement));
        exit();
    }
}

// --- RÉCUPÉRATION DES DÉTAILS DE LA RÉSERVATION ---
$query_string = "SELECT r.*, m.titre, m.prix_min_pers 
                 FROM reservations r 
                 JOIN menus m ON r.id_menu = m.id 
                 WHERE r.id = $reservation_id AND r.email_client = (SELECT email FROM utilisateurs WHERE id = $user_id)";

$result = mysqli_query($conn, $query_string);
$res = mysqli_fetch_assoc($result);

// Si la réservation n'existe pas ou n'appartient pas à ce client
if (!$res) {
    die("Réservation introuvable ou accès non autorisé.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Réservation #<?php echo $res['id']; ?></title>
    <link rel="stylesheet" href="css/menus.css">
    <style>
        .detail-container { max-width: 700px; margin: 50px auto; padding: 20px; font-family: 'Poppins', sans-serif; }
        .detail-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); border-top: 5px solid #800020; }
        h2 { color: #800020; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .info-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed #eee; }
        .info-row:last-child { border-bottom: none; }
        .label { font-weight: bold; color: #555; }
        .value { color: #1a1a1a; font-weight: 500; }
        .total-highlight { background: #fdf2f4; padding: 15px; border-radius: 8px; font-size: 18px; font-weight: bold; color: #800020; margin-top: 15px; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 14px; font-weight: bold; }
        .badge-attente { background: #ffeeba; color: #856404; }
        .badge-valide { background: #d4edda; color: #155724; }
        .badge-annule { background: #f8d7da; color: #721c24; }
        
        .payment-box { background: #f9f9f9; border: 1px solid #ddd; padding: 20px; border-radius: 8px; margin-top: 25px; }
        .btn-submit { background: #800020; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; transition: 0.2s; }
        .btn-submit:hover { background: #deff9a; color: black; }
        .btn-back { display: inline-block; margin-bottom: 20px; color: #800020; text-decoration: none; font-weight: bold; }
        .alert { padding: 12px; border-radius: 5px; margin-bottom: 15px; text-align: center; font-weight: bold; }
        .alert-success { background: #d4edda; color: #155724; }
        select { padding: 10px; width: 100%; max-width: 300px; margin-right: 10px; border-radius: 5px; border: 1px solid #ccc; }
        .form-inline { display: flex; align-items: center; margin-top: 10px; }
    </style>
</head>
<body>

<div class="detail-container">
    <a href="mon-compte.php" class="btn-back">← Retour à mon compte</a>

    <div class="detail-card">
        <h2>Détails de la réservation #<?php echo $res['id']; ?></h2>

        <?php if(isset($success_msg)): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <div class="info-row">
            <span class="label">Statut actuel :</span>
            <span class="value">
                <span class="badge badge-<?php echo strtolower(str_replace(' ', '', $res['statut'])); ?>">
                    <?php echo htmlspecialchars($res['statut']); ?>
                </span>
            </span>
        </div>

        <div class="info-row">
            <span class="label">Menu sélectionné :</span>
            <span class="value"><?php echo htmlspecialchars($res['titre']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Nombre de convives :</span>
            <span class="value"><?php echo $res['nb_personnes']; ?> personnes</span>
        </div>

        <?php 
        // On récupère le prix unitaire depuis la table menus
        $prix_unitaire = floatval($res['prix_min_pers']);
        $nb_personnes = intval($res['nb_personnes']);
        $prix_base_menu = $prix_unitaire * $nb_personnes;

        // MISE A JOUR DE LA RÈGLE : Remise de 10% si strictement plus de 5 personnes (> 5)
        $remise = 0;
        if ($nb_personnes > 5) {
            $remise = $prix_base_menu * 0.10;
        }
        $prix_menu_final = $prix_base_menu - $remise;
        ?>

        <div class="info-row">
            <span class="label">Prix du menu (<?php echo number_format($prix_unitaire, 2, ',', ' '); ?>€/pers) :</span>
            <span class="value"><?php echo number_format($prix_base_menu, 2, ',', ' '); ?> €</span>
        </div>

        <?php if($remise > 0): ?>
            <div class="info-row" style="color: #27ae60;">
                <span class="label">Remise appliquée (10% si > 5 pers.) :</span>
                <span class="value">- <?php echo number_format($remise, 2, ',', ' '); ?> €</span>
            </div>
        <?php endif; ?>

        <div class="info-row">
            <span class="label">Date de l'événement :</span>
            <span class="value"><?php echo date('d/m/Y', strtotime($res['date_evenement'])); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Heure de livraison :</span>
            <span class="value"><?php echo date('H\hi', strtotime($res['heure_livraison'])); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Lieu de livraison :</span>
            <span class="value"><?php echo htmlspecialchars($res['adresse_prestation']); ?> (<?php echo htmlspecialchars($res['ville']); ?>)</span>
        </div>

        <?php if(isset($res['km']) && floatval($res['km']) > 0): ?>
            <div class="info-row">
                <span class="label">Distance calculée :</span>
                <span class="value"><?php echo $res['km']; ?> km</span>
            </div>
        <?php endif; ?>

        <div class="info-row">
            <span class="label">Frais de livraison :</span>
            <span class="value"><?php echo number_format($res['frais_livraison'], 2, ',', ' '); ?> €</span>
        </div>

        <div class="info-row total-highlight">
            <span>Montant Total :</span>
            <span>
                <?php 
                $total_affiche = floatval($res['prix_total']);
                if ($total_affiche <= floatval($res['frais_livraison'])) {
                    $total_affiche = $prix_menu_final + floatval($res['frais_livraison']);
                }
                echo number_format($total_affiche, 2, ',', ' '); 
                ?> €
            </span>
        </div>

        <div class="payment-box">
            <h3>💳 Moyen de paiement</h3>
            <?php if (!empty($res['moyen_paiement'])): ?>
                <p>Vous avez choisi de régler par : <strong><?php echo htmlspecialchars($res['moyen_paiement']); ?></strong></p>
                <p style="font-size: 13px; color: #666; font-style: italic;">Pour modifier votre moyen de paiement, veuillez contacter Julie & José.</p>
            <?php else: ?>
                <p style="color: #c0392b; font-weight: 500;">⚠️ Aucun moyen de paiement sélectionné pour le moment.</p>
                <form action="" method="POST" class="form-inline">
                    <select name="moyen_paiement" required>
                        <option value="">-- Choisir un mode de règlement --</option>
                        <option value="Carte Bancaire">Carte Bancaire (En ligne)</option>
                        <option value="PayPal">PayPal</option>
                        <option value="Espèces">Espèces (Au retrait/livraison)</option>
                    </select>
                    <button type="submit" class="btn-submit">Valider</button>
                </form>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>
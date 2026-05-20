<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: connexion.php'); exit(); }

require_once('config.php');
$reservation_id = intval($_GET['id']);

// Vérification que la commande appartient bien au client connecté
$user_id = $_SESSION['user_id'];
$user_query = mysqli_query($conn, "SELECT email FROM utilisateurs WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_query);

$check_query = mysqli_query($conn, "SELECT * FROM reservations WHERE id = $reservation_id AND email_client = '{$user['email']}'");
if (mysqli_num_rows($check_query) === 0) { die("Accès refusé."); }

// Récupération de l'historique des statuts
$history_query = mysqli_query($conn, "SELECT * FROM historique_statuts WHERE id_reservation = $reservation_id ORDER BY date_modification ASC");

// --- NOUVEAU : Récupération du statut le plus récent ---
$dernier_statut_query = mysqli_query($conn, "SELECT statut FROM historique_statuts WHERE id_reservation = $reservation_id ORDER BY date_modification DESC LIMIT 1");
$dernier_statut = mysqli_fetch_assoc($dernier_statut_query)['statut'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi de ma commande</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .tracking-container { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); font-family: sans-serif; }
        .timeline { border-left: 3px solid #ff9800; padding-left: 20px; margin-left: 10px; list-style: none; }
        .timeline-item { margin-bottom: 25px; position: relative; }
        .timeline-item::before { content: ''; position: absolute; left: -27px; top: 5px; background: #ff9800; width: 12px; height: 12px; border-radius: 50%; }
        .status-title { font-weight: bold; color: #333; font-size: 16px; text-transform: uppercase; }
        .status-date { color: #666; font-size: 13px; }
        /* Style du bouton avis */
        .btn-avis { display: inline-block; margin-top: 20px; padding: 12px 25px; background: #27ae60; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .btn-avis:hover { background: #219150; }
    </style>
</head>
<body>
    <div class="tracking-container">
        <h1>Suivi de ma commande n°<?php echo $reservation_id; ?></h1>
        <p><a href="mon-compte.php" style="color: #ff9800; text-decoration: none;">← Retour à mon compte</a></p>
        
        <ul class="timeline">
            <?php while($step = mysqli_fetch_assoc($history_query)): ?>
                <li class="timeline-item">
                    <div class="status-title">État : <?php echo htmlspecialchars($step['statut']); ?></div>
                    <div class="status-date">Le <?php echo date('d/m/Y à H\hi', strtotime($step['date_modification'])); ?></div>
                </li>
            <?php endwhile; ?>
        </ul>

        <?php if ($dernier_statut === 'Terminée'): ?>
            <div style="text-align: center; margin-top: 30px;">
                <p>La commande est terminée. Merci de votre confiance !</p>
                <a href="laisser-avis.php?id=<?php echo $reservation_id; ?>" class="btn-avis">⭐ Laisser un avis</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
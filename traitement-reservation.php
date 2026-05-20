<?php
require_once('fonctions-mail.php');
session_start();
require_once('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menu_id'])) {
    
    $user_id = $_SESSION['user_id'];
    $menu_id = intval($_POST['menu_id']);
    $nb_personnes = intval($_POST['nb_pers']);
    
    // Récupération des infos utilisateur
    $stmt_user = $conn->prepare("SELECT nom, email, telephone FROM utilisateurs WHERE id = ?");
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $user_info = $stmt_user->get_result()->fetch_assoc();

    $nom_client = $user_info['nom'] ?? 'Client';
    $email_client = $user_info['email'] ?? ''; 
    $telephone = $user_info['telephone'] ?? '';

    // Récupération menu
    $stmt_menu = $conn->prepare("SELECT * FROM menus WHERE id = ?");
    $stmt_menu->bind_param("i", $menu_id);
    $stmt_menu->execute();
    $menu = $stmt_menu->get_result()->fetch_assoc();

    if (!$menu || $menu['stock'] <= 0 || $nb_personnes < $menu['nb_pers_min']) {
        header('Location: reservation.php?error=validation');
        exit();
    }

    // Calculs
    $prix_total = ($menu['prix_min_pers'] * $nb_personnes);
    if ($nb_personnes > 5) $prix_total *= 0.9;
    if ($_POST['ville'] === "Hors-Bordeaux") $prix_total += (5 + (floatval($_POST['km']) * 0.59));

    // Insertion sécurisée de la réservation
    $sql = "INSERT INTO reservations (nom_client, email_client, telephone, date_evenement, heure_livraison, nb_personnes, prix_total, id_menu, adresse_prestation, ville, statut, date_creation) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'En attente', NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssdidsss", $nom_client, $email_client, $telephone, $_POST['date_evenement'], $_POST['heure_livraison'], $nb_personnes, $prix_total, $menu_id, $_POST['adresse_prestation'], $_POST['ville']);

    if ($stmt->execute()) {
        $reservation_id = $stmt->insert_id;
        
        // 1. Mise à jour du stock
        $conn->query("UPDATE menus SET stock = stock - 1, stock_dispo = stock_dispo - 1 WHERE id = $menu_id");

        // 2. Initialisation de l'historique des statuts
        $sql_hist = "INSERT INTO historique_statuts (id_reservation, statut, date_modification) VALUES (?, 'En attente', NOW())";
        $stmt_hist = $conn->prepare($sql_hist);
        $stmt_hist->bind_param("i", $reservation_id);
        $stmt_hist->execute();

        // Envoi des emails
        mailConfirmationCommande($email_client, "Détails de votre commande..."); 
        mailNotificationMateriel($reservation_id, $menu['titre'], $_POST['date_evenement'], $_POST['heure_livraison']);

        header('Location: mon-compte.php?success=1');
        exit();
    } else {
        die("Erreur base de données : " . $conn->error);
    }
}
?>
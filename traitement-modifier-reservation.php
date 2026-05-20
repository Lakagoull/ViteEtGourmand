<?php
session_start();
require_once('config.php');

// Sécurité Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { 
    header('Location: connexion.php'); 
    exit();
}

// Vérification que les données du formulaire ont bien été envoyées
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_res']) && isset($_POST['statut'])) {
    
    $id_res = intval($_POST['id_res']);
    $nouveau_statut = $_POST['statut'];

    // 1. Mise à jour de la table principale (reservations)
    $sql_update = "UPDATE reservations SET statut = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $nouveau_statut, $id_res);
    
    if ($stmt_update->execute()) {
        
        // 2. AJOUT DANS L'HISTORIQUE (Pour ton suivi de commande !)
        $sql_hist = "INSERT INTO historique_statuts (id_reservation, statut, date_modification) VALUES (?, ?, NOW())";
        $stmt_hist = $conn->prepare($sql_hist);
        $stmt_hist->bind_param("is", $id_res, $nouveau_statut);
        $stmt_hist->execute();

        // Redirection vers l'accueil de l'admin après le succès
        header('Location: admin-reservations.php?success=1');
        exit();
        
    } else {
        die("Erreur lors de la mise à jour : " . $conn->error);
    }
} else {
    // Si on arrive ici sans passer par le formulaire, on redirige
    header('Location: admin-reservations.php');
    exit();
}
?>
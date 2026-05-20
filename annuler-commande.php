<?php
session_start();

// Affichage des erreurs au cas où
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('config.php');

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

// Sécurité : il faut être connecté
if (!isset($_SESSION['user_id'])) { 
    header('Location: connexion.php'); 
    exit(); 
}

if (isset($_GET['id'])) {
    $reservation_id = intval($_GET['id']);
    $user_id = intval($_SESSION['user_id']);

    // 1. CORRECTION ICI : On utilise r.email_client lié à l'ID de session pour correspondre à ta BDD
    $query_string = "SELECT * FROM reservations 
                     WHERE id = $reservation_id 
                     AND email_client = (SELECT email FROM utilisateurs WHERE id = $user_id)";
    
    $query = mysqli_query($conn, $query_string);
    
    if (!$query) {
        die("Erreur SQL : " . mysqli_error($conn));
    }

    $commande = mysqli_fetch_assoc($query);

    if ($commande) {
        // 2. Vérification stricte exigée par le sujet : annulation possible UNIQUEMENT si "En attente"
        if ($commande['statut'] === 'En attente') {
            
            // 3. On passe le statut à "Annulée"
            $update = mysqli_query($conn, "UPDATE reservations SET statut = 'Annulée' WHERE id = $reservation_id");
            
            if ($update) {
                // HISTORIQUE : On enregistre l'annulation dans l'historique des statuts
                mysqli_query($conn, "INSERT INTO historique_statuts (id_reservation, statut, date_modification) VALUES ($reservation_id, 'Annulée par le client', NOW())");

                // 4. RESTOCKAGE : On conserve ton super système de +1 sur le stock du menu !
                $menu_id = intval($commande['id_menu']);
                mysqli_query($conn, "UPDATE menus SET stock = stock + 1 WHERE id = $menu_id");
                
                header('Location: mon-compte.php?msg=annule_ok');
                exit();
            }
        } else {
            // Tentative d'annulation alors que l'employé l'a déjà acceptée ou préparée
            header('Location: mon-compte.php?error=deja_acceptee');
            exit();
        }
    }
}

header('Location: mon-compte.php');
exit();
?>
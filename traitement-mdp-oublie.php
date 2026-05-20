<?php
session_start();

// Affichage des erreurs au cas où
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('config.php');

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // 1. Vérifier si l'adresse email existe bien dans la table utilisateurs
    $query = mysqli_query($conn, "SELECT id FROM utilisateurs WHERE email = '$email'");
    $user = mysqli_fetch_assoc($query);

    if ($user) {
        // 2. Générer un jeton de sécurité unique (Token)
        // bin2hex(random_bytes(32)) crée une chaîne de caractères aléatoire et sécurisée (ex: a1b2c3d4...)
        $token = bin2hex(random_bytes(32));
        
        // On définit une date d'expiration pour le lien (ex: valide pendant 1 heure)
        $expire = date("Y-m-d H:i:s", strtotime('+1 hour'));
        $user_id = $user['id'];

        // 3. Enregistrer le token en base de données
        // Note : Assure-toi d'avoir une table ou des colonnes pour stocker le token.
        // Si tu as ajouté les colonnes directement dans la table 'utilisateurs' :
        $sql_token = "UPDATE utilisateurs SET token_recup = '$token', token_expire = '$expire' WHERE id = $user_id";
        
        if (mysqli_query($conn, $sql_token)) {
            
            // ================================================================
            // ENVOI DU MAIL DE RÉINITIALISATION AVEC PHPMAILER
            // ================================================================
            require_once 'fonctions-mail.php';
            
            // Appele de ta fonction du mot de passe oublié
            mailMotDePasseOublie($email, $token);
            // ================================================================

            // Pour éviter que des personnes malveillantes sachent si un e-mail existe ou non,
            // on affiche le même message de confirmation dans tous les cas.
            header('Location: mdp-oublie.php?status=envoye');
            exit();
        } else {
            die("Erreur lors de la génération du token : " . mysqli_error($conn));
        }
    } else {
        // Même si l'email n'existe pas, on redirige avec le même message pour des raisons de sécurité
        header('Location: mdp-oublie.php?status=envoye');
        exit();
    }
} else {
    header('Location: connexion.php');
    exit();
}
mysqli_close($conn);
?>
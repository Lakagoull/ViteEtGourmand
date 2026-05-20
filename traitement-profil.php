<?php
session_start();

// Connexion à la base de données
require_once('config.php');

if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Vérifier que l'utilisateur est bien connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupération des données du formulaire
$nom = mysqli_real_escape_string($conn, $_POST['nom']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = $_POST['new_password'];

// 1. Mise à jour de base (Nom et Email)
$sql = "UPDATE utilisateurs SET nom = '$nom', email = '$email' WHERE id = $user_id";

if (mysqli_query($conn, $sql)) {
    // Mettre à jour les variables de session pour l'affichage immédiat
    $_SESSION['prenom'] = $nom; 
    $_SESSION['email'] = $email;

    // 2. Si un nouveau mot de passe a été saisi
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql_pass = "UPDATE utilisateurs SET mot_de_passe = '$hashed_password' WHERE id = $user_id";
        mysqli_query($conn, $sql_pass);
    }

    // Redirection vers la page compte avec un message de succès
    header('Location: mon-compte.php?success=1');
} else {
    echo "Erreur lors de la mise à jour : " . mysqli_error($conn);
}

mysqli_close($conn);
?>
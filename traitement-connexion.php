<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "vite_gourmand";

require_once('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM utilisateurs WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Vérification du mot de passe
        if (password_verify($password, $user['mot_de_passe'])) {
            // SUCCÈS
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['prenom'] = $user['prenom']; // Vérifie si ta colonne est 'prenom' ou 'nom'
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['role'] = $user['role'];

            // Redirection intelligente
            if ($_SESSION['role'] === 'admin') {
                header('Location: admin-menus.php'); // L'admin va direct au boulot !
            } else {
                header('Location: index.php'); // Le client va à l'accueil
            }
            exit();

        } else {
            // Mauvais mot de passe
            header("Location: connexion.php?error=mdp");
            exit();
        }
    } else {
        // Email inexistant
        header("Location: connexion.php?error=email");
        exit();
    }
}

mysqli_close($conn);
?>
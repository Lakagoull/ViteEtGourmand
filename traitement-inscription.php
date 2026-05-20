<?php
// On inclut les fichiers nécessaires
require_once('config.php');
require_once('fonctions-mail.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Protection des données (Sécurisation contre injections SQL)
    $nom     = mysqli_real_escape_string($conn, $_POST['nom']);
    $prenom  = mysqli_real_escape_string($conn, $_POST['prenom']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $tel     = mysqli_real_escape_string($conn, $_POST['tel']);
    $adresse = mysqli_real_escape_string($conn, $_POST['adresse']);
    $mdp     = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // 2. Vérification si l'utilisateur existe déjà
    $check_sql = "SELECT id FROM utilisateurs WHERE email = '$email'";
    $result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($result) > 0) {
        die("Erreur : Cet e-mail est déjà utilisé. <a href='inscription.php'>Retour</a>");
    }

    // 3. Insertion dans la base de données
    $sql = "INSERT INTO utilisateurs (nom, prenom, email, telephone, adresse, mot_de_passe) 
            VALUES ('$nom', '$prenom', '$email', '$tel', '$adresse', '$mdp')";

    if (mysqli_query($conn, $sql)) {
        
        // 4. Envoi du mail de bienvenue (si la fonction existe)
        if (function_exists('mailBienvenue')) {
            mailBienvenue($email, $prenom);
        }

        // 5. Redirection finale
        header('Location: inscription-succes.php?prenom=' . urlencode($prenom) . '&email=' . urlencode($email));
        exit();
    } else {
        echo "Erreur lors de l'inscription : " . mysqli_error($conn);
    }
}
?>
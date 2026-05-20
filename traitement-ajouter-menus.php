<?php
session_start();
require_once('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des champs du formulaire
    $titre = mysqli_real_escape_string($conn, $_POST['titre']);
    $theme = mysqli_real_escape_string($conn, $_POST['theme']);
    $regime = mysqli_real_escape_string($conn, $_POST['regime']);
    $accroche = mysqli_real_escape_string($conn, $_POST['accroche']); // Va dans 'description'
    $composition = mysqli_real_escape_string($conn, $_POST['composition']); // On peut le mettre dans 'plats' par exemple
    $allergenes = mysqli_real_escape_string($conn, $_POST['allergenes']);
    $prix = $_POST['prix'];
    $min_pers = $_POST['min_pers'];
    $stock = 12; // Valeur par défaut comme sur ta capture

    // Gestion de la photo
    $image_nom = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $destination = "assets/" . $image_nom;

    if (move_uploaded_file($image_tmp, $destination)) {
        // Requête SQL basée sur TA structure d'image_ef05bc.png
        $sql = "INSERT INTO menus (titre, description, image, theme, regime, nb_pers_min, prix_min_pers, plats, allergenes, stock_dispo) 
                VALUES ('$titre', '$accroche', '$image_nom', '$theme', '$regime', '$min_pers', '$prix', '$composition', '$allergenes', '$stock')";

        if (mysqli_query($conn, $sql)) {
            header("Location: admin-menus.php?msg=ajoute");
            exit();
        } else {
            echo "Erreur SQL : " . mysqli_error($conn);
        }
    } else {
        echo "Erreur : Impossible de télécharger l'image.";
    }
}
?>
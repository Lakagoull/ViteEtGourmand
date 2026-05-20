<?php
session_start();
require_once('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $titre = mysqli_real_escape_string($conn, $_POST['titre']);
    $theme = mysqli_real_escape_string($conn, $_POST['theme']);
    $regime = mysqli_real_escape_string($conn, $_POST['regime']);
    $accroche = mysqli_real_escape_string($conn, $_POST['accroche']);
    $composition = mysqli_real_escape_string($conn, $_POST['composition']);
    $allergenes = mysqli_real_escape_string($conn, $_POST['allergenes']);
    $prix = $_POST['prix'];
    $min_pers = $_POST['min_pers'];

    // Si l'utilisateur a sélectionné une nouvelle image
    if (!empty($_FILES['image']['name'])) {
        $image_nom = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "assets/" . $image_nom);
        
        $sql = "UPDATE menus SET 
                titre='$titre', theme='$theme', regime='$regime', description='$accroche', 
                plats='$composition', allergenes='$allergenes', prix_min_pers='$prix', 
                nb_pers_min='$min_pers', image='$image_nom' 
                WHERE id=$id";
    } else {
        // Sinon, on met à jour tout SAUF l'image
        $sql = "UPDATE menus SET 
                titre='$titre', theme='$theme', regime='$regime', description='$accroche', 
                plats='$composition', allergenes='$allergenes', prix_min_pers='$prix', 
                nb_pers_min='$min_pers' 
                WHERE id=$id";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: admin-menus.php?msg=modifie");
    } else {
        echo "Erreur SQL : " . mysqli_error($conn);
    }
}
?>
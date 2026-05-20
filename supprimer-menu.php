<?php
session_start();
// Sécurité : Vérifier si c'est bien l'admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$conn = mysqli_connect("sqlXXX.infinityfree.com", "if0_41964657", "Viteetgourmand", "if0_41964657_base_viteetgourmand");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Optionnel : Tu pourrais ici supprimer l'image du dossier assets avec unlink() 
    // mais pour ton ECF, supprimer la ligne en BDD est déjà suffisant.
    
    $sql = "DELETE FROM menus WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: admin-menus.php?msg=supprime");
    } else {
        echo "Erreur lors de la suppression : " . mysqli_error($conn);
    }
} else {
    header("Location: admin-menus.php");
}
?>
<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { exit(); }

require_once('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Récupération et protection des données
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // On ne l'échappe pas car on va le hacher
    $role = $_POST['role'];

    // 2. HACHAGE du mot de passe (Indispensable !)
    $password_hache = password_hash($password, PASSWORD_DEFAULT);

    // 3. Insertion en base de données
    $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe, role) 
            VALUES ('$nom', '$email', '$password_hache', '$role')";

    if (mysqli_query($conn, $sql)) {
        header("Location: admin-users.php?msg=success");
    } else {
        echo "Erreur lors de la création : " . mysqli_error($conn);
    }
}
?>
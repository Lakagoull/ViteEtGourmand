<?php
// Configuration de la Base de Données InfinityFree
define('DB_HOST', 'sql108.infinityfree.com');
define('DB_USER', 'if0_41964657');
define('DB_PASS', 'Viteetgourmand'); // Ton mot de passe vPanel (celui de ton compte client)
define('DB_NAME', 'if0_41964657_base_viteetgourmand');

// Connexion à la base de données
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}

// Encodage pour éviter les problèmes d'accents
mysqli_set_charset($conn, "utf8");

// Configuration SMTP pour les e-mails
define('SMTP_HOST', 'smtp.gmail.com'); 
define('SMTP_USER', 'viteetgourmand.traiteur@gmail.com');
define('SMTP_PASS', 'raku pkih ueoa prjt');
define('SMTP_PORT', 587);

// URL racine du site en ligne
define('SITE_URL', 'http://viteetgourmand-traiteur.infinityfreeapp.com/');
?>
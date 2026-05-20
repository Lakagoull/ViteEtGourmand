<?php
// 1. On démarre la session pour pouvoir y accéder
session_start();

// 2. On vide toutes les variables de session (le prénom, l'id, etc.)
$_SESSION = array();

// 3. On détruit la session sur le serveur
session_destroy();

// 4. On redirige l'utilisateur vers la page d'accueil
header("Location: index.php");
exit();
?>
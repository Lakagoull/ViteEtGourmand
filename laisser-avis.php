<?php
session_start();

// 1. Activation des erreurs (pour voir le vrai problème au lieu de l'erreur 500)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('config.php');

// Vérification de connexion
if (!isset($_SESSION['user_id']) && !isset($_SESSION['id'])) { 
    header('Location: connexion.php'); 
    exit(); 
}

// Sécurisation de l'ID passé dans l'URL
$reservation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = intval($_POST['note']);
    $commentaire = htmlspecialchars($_POST['commentaire']);
    
    // 2. Gestion intelligente du nom (évite le crash si 'nom' n'existe pas)
    if (isset($_SESSION['nom']) && !empty($_SESSION['nom'])) {
        $nom_client = $_SESSION['nom'];
    } elseif (isset($_SESSION['prenom']) && !empty($_SESSION['prenom'])) {
        $nom_client = $_SESSION['prenom'];
    } else {
        $nom_client = "Client invité";
    }

    // 3. Préparation sécurisée de la requête SQL
    $sql = "INSERT INTO avis (nom_client, note, commentaire, valide, date_avis) VALUES (?, ?, ?, 0, NOW())";
    $stmt = $conn->prepare($sql);
    
    // Si la requête échoue, on affiche l'erreur en clair au lieu de faire l'erreur 500
    if ($stmt === false) {
        die("<div style='color:red; text-align:center; padding:50px; font-family:sans-serif;'>
                <h2>🛑 Erreur SQL détectée</h2>
                <p>Ton serveur a refusé la requête car : <strong>" . $conn->error . "</strong></p>
                <p>Vérifie que ta table 'avis' possède bien les colonnes : nom_client, note, commentaire, valide, date_avis.</p>
             </div>");
    }

    $stmt->bind_param("sis", $nom_client, $note, $commentaire);
    
    if ($stmt->execute()) {
        header('Location: mon-compte.php?msg=avis_envoye');
        exit();
    } else {
        die("Erreur lors de l'enregistrement de l'avis : " . $stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Laisser un avis | Vite & Gourmand</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background-color: #fffaf0; font-family: 'Lato', sans-serif; }
        .avis-container { max-width: 500px; margin: 80px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); text-align: center; }
        .avis-container h1 { color: #800020; font-family: 'Playfair Display', serif; margin-bottom: 20px; }
        label { display: block; text-align: left; font-weight: bold; margin-top: 15px; color: #333; }
        input, select, textarea { width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; font-family: inherit; }
        .btn-submit { background: #27ae60; color: white; border: none; padding: 15px; width: 100%; margin-top: 25px; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; transition: 0.3s; }
        .btn-submit:hover { background: #219150; }
        .btn-cancel { display: inline-block; margin-top: 15px; color: #800020; text-decoration: underline; font-size: 14px; }
    </style>
</head>
<body>
    <div class="avis-container">
        <h1>Votre avis compte !</h1>
        <p style="color: #666; margin-bottom: 25px;">Partagez votre expérience avec Vite & Gourmand.</p>
        
        <form method="POST">
            <label>Votre note globale :</label>
            <select name="note" required>
                <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
                <option value="4">⭐⭐⭐⭐ - Très bien</option>
                <option value="3">⭐⭐⭐ - Bien</option>
                <option value="2">⭐⭐ - Moyen</option>
                <option value="1">⭐ - Décevant</option>
            </select>

            <label>Votre commentaire :</label>
            <textarea name="commentaire" rows="5" placeholder="Décrivez votre expérience..." required></textarea>

            <button type="submit" class="btn-submit">Envoyer mon avis</button>
            <a href="mon-compte.php" class="btn-cancel">Annuler et retourner au profil</a>
        </form>
    </div>
</body>
</html>
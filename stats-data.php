<?php
session_start();
// Sécurité : Vérifier que seul l'admin accède à ces données
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit(json_encode(["error" => "Accès refusé"]));
}

header('Content-Type: application/json');

$jsonFile = 'data/stats.json';

// Vérification si le fichier "base de données NoSQL" existe
if (file_exists($jsonFile)) {
    // Lecture directe du document JSON
    $jsonData = file_get_contents($jsonFile);
    echo $jsonData;
} else {
    // Si le fichier n'est pas encore créé, on renvoie une erreur vide
    echo json_encode(["message" => "Base NoSQL vide, veuillez cliquer sur 'Mettre à jour'"]);
}
?>
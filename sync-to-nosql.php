<?php
require_once('config.php');

// 1. Récupérer les données depuis ton SQL (Base relationnelle)
$sql = "SELECT m.titre, SUM(r.prix_total) as ca, COUNT(r.id) as nb_ventes 
        FROM reservations r 
        JOIN menus m ON r.id_menu = m.id 
        WHERE r.statut = 'Terminée' 
        GROUP BY m.titre";
$res = $conn->query($sql);
$data = $res->fetch_all(MYSQLI_ASSOC);

// 2. Sauvegarder dans ton fichier "NoSQL" (JSON)
file_put_contents('data/stats.json', json_encode($data));

header('Location: admin-stats.php?msg=sync_ok');
exit();
?>
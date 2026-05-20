<?php
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'employe')) { 
    header('Location: index.php'); 
    exit();
}
require_once('config.php');

// --- LOGIQUE DES FILTRES DE RECHERCHE ---
$where_clauses = [];

// Filtre par statut
if (!empty($_GET['filtre_statut'])) {
    $filtre_statut = mysqli_real_escape_string($conn, $_GET['filtre_statut']);
    $where_clauses[] = "r.statut = '$filtre_statut'";
}

// Filtre par recherche client (Nom ou Email)
if (!empty($_GET['recherche_client'])) {
    $recherche_client = mysqli_real_escape_string($conn, $_GET['recherche_client']);
    $where_clauses[] = "(r.nom_client LIKE '%$recherche_client%' OR r.email_client LIKE '%$recherche_client%')";
}

// Construction dynamique de la requête SQL
$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

$sql = "SELECT r.*, m.titre AS menu_titre 
        FROM reservations r 
        LEFT JOIN menus m ON r.id_menu = m.id 
        $where_sql
        ORDER BY r.date_evenement ASC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Réservations | Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .filter-bar { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-end; border: 1px solid #ddd; }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        .filter-group label { font-size: 12px; font-weight: bold; color: #555; }
        .filter-group input, .filter-group select { padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; min-width: 180px; }
        .btn-filter { background: #800020; color: white; border: none; padding: 8px 15px; border-radius: 4px; font-weight: bold; cursor: pointer; height: 36px; text-decoration: none; display: inline-flex; align-items: center; }
        .btn-reset { background: #7f8c8d; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-links">
        <a href="index.php">🏠 Site</a>
        <a href="admin-menus.php">🍴 Menus</a>
        <a href="admin-reservations.php" class="active">📅 Réservations</a>
        <a href="admin-users.php">👥 Utilisateurs</a>
        <a href="admin-avis.php" class="active">⭐ Avis</a>
        <a href="admin-stats.php">📊 Statistiques</a>
    </div>
    <div class="nav-admin-badge">
        ADMINISTRATEUR
    </div>
</nav>

<div class="container">
    <h1>📅 Liste des Réservations</h1>
    
    <form method="GET" action="admin-reservations.php" class="filter-bar">
        <div class="filter-group">
            <label>Rechercher un client</label>
            <input type="text" name="recherche_client" placeholder="Nom ou email..." value="<?php echo htmlspecialchars($_GET['recherche_client'] ?? ''); ?>">
        </div>
        
        <div class="filter-group">
            <label>Filtrer par statut</label>
            <select name="filtre_statut">
                <option value="">-- Tous les statuts --</option>
                <option value="En attente" <?php if(($_GET['filtre_statut'] ?? '') === 'En attente') echo 'selected'; ?>>En attente</option>
                <option value="accepté" <?php if(($_GET['filtre_statut'] ?? '') === 'accepté') echo 'selected'; ?>>Accepté</option>
                <option value="en préparation" <?php if(($_GET['filtre_statut'] ?? '') === 'en préparation') echo 'selected'; ?>>En préparation</option>
                <option value="en cours de livraison" <?php if(($_GET['filtre_statut'] ?? '') === 'en cours de livraison') echo 'selected'; ?>>En cours de livraison</option>
                <option value="livré" <?php if(($_GET['filtre_statut'] ?? '') === 'livré') echo 'selected'; ?>>Livré</option>
                <option value="en attente du retour de matériel" <?php if(($_GET['filtre_statut'] ?? '') === 'en attente du retour de matériel') echo 'selected'; ?>>En attente retour matériel</option>
                <option value="terminée" <?php if(($_GET['filtre_statut'] ?? '') === 'terminée') echo 'selected'; ?>>Terminée</option>
                <option value="Annulée" <?php if(($_GET['filtre_statut'] ?? '') === 'Annulée') echo 'selected'; ?>>Annulée</option>
            </select>
        </div>

        <button type="submit" class="btn-filter">🔍 Filtrer</button>
        <?php if(!empty($_GET['filtre_statut']) || !empty($_GET['recherche_client'])): ?>
            <a href="admin-reservations.php" class="btn-filter btn-reset">❌ Réinitialiser</a>
        <?php endif; ?>
    </form>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Client</th>
                <th>Menu Choisi</th>
                <th>Pers.</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($row['date_evenement'])); ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['nom_client']); ?></strong><br>
                        <small><?php echo $row['email_client']; ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($row['menu_titre'] ?? 'Menu supprimé'); ?></td>
                    <td><?php echo $row['nb_personnes']; ?></td>
                    <td>
                        <?php 
                            // Gestion étendue des couleurs selon les statuts officiels du sujet
                            $couleur = "#f1c40f"; // Jaune par défaut (En attente)
                            switch(strtolower($row['statut'])) {
                                case 'accepté':
                                    $couleur = "#3498db"; // Bleu
                                    break;
                                case 'en préparation':
                                    $couleur = "#9b59b6"; // Violet
                                    break;
                                case 'en cours de livraison':
                                    $couleur = "#e67e22"; // Orange
                                    break;
                                case 'livré':
                                    $couleur = "#1abc9c"; // Turquoise
                                    break;
                                case 'en attente du retour de matériel':
                                    $couleur = "#e74c3c"; // Rouge clignotant / alerte
                                    break;
                                case 'terminée':
                                    $couleur = "#2ecc71"; // Vert
                                    break;
                                case 'annulée':
                                    $couleur = "#95a5a6"; // Gris
                                    break;
                            }
                        ?>
                        <span style="background: <?php echo $couleur; ?>; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; color: white; display: inline-block; white-space: nowrap;">
                            <?php echo $row['statut']; ?>
                        </span>
                    </td>
                    <td>
                        <a href="modifier-reservation.php?id=<?php echo $row['id']; ?>" style="color: #800020; font-weight: bold; text-decoration: none;">Gérer</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #666; padding: 20px;">Aucune réservation ne correspond à vos critères de recherche.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
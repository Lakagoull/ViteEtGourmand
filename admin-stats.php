<?php
session_start();
// Sécurité Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: connexion.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques | Administrateur</title>
    <link rel="stylesheet" href="css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .main-content { padding: 40px; }
        .stats-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .kpi-row { display: flex; gap: 20px; margin-bottom: 30px; }
        .kpi-card { flex: 1; padding: 20px; background: #f8f9fa; border-radius: 10px; text-align: center; border-left: 5px solid #800020; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <header class="main-header" style="border-bottom: 2px solid #800020;">
        <nav class="navbar">
            <div class="nav-links">
                <a href="index.php">🏠 Site</a>
                <a href="admin-menus.php">🍴 Menus</a>
                <a href="admin-reservations.php">📅 Réservations</a>
                <a href="admin-users.php">👥 Utilisateurs</a>
                <a href="admin-avis.php">⭐ Avis</a>
                <a href="admin-stats.php" class="active">📊 Statistiques</a>
                <a href="sync-to-nosql.php" target="_blank" style="background: #3498db; color: white; padding: 10px; text-decoration: none; border-radius: 5px;">
    🔄 Mettre à jour la base NoSQL
</a>
            </div>
            <div class="nav-admin-info">ADMINISTRATEUR</div>
        </nav>
    </header>

    <main class="main-content">
        <div class="stats-container">
            <h1>Statistiques des ventes</h1>

            <form id="filterForm" style="margin-bottom: 30px;">
                <label>Du : </label>
                <input type="date" name="debut" value="<?php echo date('Y-m-01'); ?>">
                <label> Au : </label>
                <input type="date" name="fin" value="<?php echo date('Y-m-t'); ?>">
                <button type="submit" style="padding: 5px 15px; cursor:pointer;">Filtrer</button>
            </form>

            <div class="kpi-row">
                <div class="kpi-card">CA Total: <h2 id="totalCA">0€</h2></div>
                <div class="kpi-card">Total Ventes: <h2 id="totalVentes">0</h2></div>
            </div>

            <div style="width: 80%; margin: auto; height: 300px;">
                <canvas id="caChart"></canvas>
            </div>

            <table id="statsTable">
                <thead><tr><th>Menu</th><th>Nombre de ventes</th><th>CA généré</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </main>

<script>
let myChart;

async function updateDashboard() {
    const formData = new FormData(document.getElementById('filterForm'));
    const response = await fetch(`stats-data.php?debut=${formData.get('debut')}&fin=${formData.get('fin')}`);
    const data = await response.json();

    // Calculs pour les KPI
    const totalCA = data.reduce((sum, item) => sum + parseFloat(item.ca), 0);
    const totalVentes = data.reduce((sum, item) => sum + parseInt(item.nb_ventes), 0);
    document.getElementById('totalCA').innerText = totalCA.toFixed(2) + '€';
    document.getElementById('totalVentes').innerText = totalVentes;

    // Mise à jour du tableau
    const tbody = document.querySelector('#statsTable tbody');
    tbody.innerHTML = data.map(item => `<tr><td>${item.titre}</td><td>${item.nb_ventes}</td><td>${item.ca}€</td></tr>`).join('');

    // Mise à jour du graphique
    const ctx = document.getElementById('caChart').getContext('2d');
    if (myChart) myChart.destroy();

    myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.titre),
            datasets: [{
                label: 'Chiffre d\'Affaire (€)',
                data: data.map(item => item.ca),
                backgroundColor: '#800020'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
}

document.getElementById('filterForm').addEventListener('submit', (e) => {
    e.preventDefault();
    updateDashboard();
});

updateDashboard();
</script>
</body>
</html>
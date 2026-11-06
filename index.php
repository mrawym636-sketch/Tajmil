<?php
$active_page = 'dashboard';
$page_title = 'Dashboard';
require_once __DIR__ . '/config.php';

// Indicateurs
$total_orders = $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total) FROM commandes")->fetchColumn() ?? 0;
$total_users = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM produits")->fetchColumn();
$pending_orders = $pdo->query("SELECT COUNT(*) FROM commandes WHERE statut='En attente'")->fetchColumn();
$avg_cart = $pdo->query("SELECT AVG(total) FROM commandes")->fetchColumn() ?? 0;

// Taux de conversion (visites simples)
$visits = $pdo->query("SELECT SUM(visits) FROM visits")->fetchColumn() ?: 1;
$conversion_rate = round(($total_orders / $visits) * 100, 1);

// Top produits (depuis JSON)
$top_products = [];
$stmt = $pdo->query("SELECT produits FROM commandes");
while ($row = $stmt->fetch()) {
    $items = json_decode($row['produits'], true);
    if (is_array($items)) {
        foreach ($items as $item) {
            $name = $item['nom'] ?? $item['name'] ?? '';
            $qty = $item['qty'] ?? 1;
            if ($name) {
                if (!isset($top_products[$name])) $top_products[$name] = 0;
                $top_products[$name] += $qty;
            }
        }
    }
}
arsort($top_products);
$top_products = array_slice($top_products, 0, 5, true);

// Ventes mensuelles
$sales_by_month = [];
for ($i = 11; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $month_name = date('M Y', strtotime("-$i months"));
    $total = $pdo->query("SELECT SUM(total) FROM commandes WHERE DATE_FORMAT(date_commande, '%Y-%m') = '$month'")->fetchColumn() ?? 0;
    $sales_by_month[] = ['month' => $month_name, 'total' => (float)$total];
}

// Répartition des statuts
$status_stats = $pdo->query("SELECT statut, COUNT(*) as count FROM commandes GROUP BY statut")->fetchAll();
$status_labels = array_column($status_stats, 'statut');
$status_counts = array_column($status_stats, 'count');

require_once 'header.php';
?>

<div class="stats-grid">
    <div class="sc em"><i class="fas fa-chart-line sc-icon"></i><div class="sc-label">Chiffre d'affaires</div><div class="sc-val"><?= number_format($total_revenue, 0) ?> DH</div><div class="sc-sub">Total encaissé</div></div>
    <div class="sc blue"><i class="fas fa-shopping-bag sc-icon"></i><div class="sc-label">Commandes</div><div class="sc-val"><?= $total_orders ?></div><div class="sc-sub"><?= $pending_orders ?> en attente</div></div>
    <div class="sc warn"><i class="fas fa-users sc-icon"></i><div class="sc-label">Conversion</div><div class="sc-val"><?= $conversion_rate ?>%</div><div class="sc-sub">Taux de transformation</div></div>
    <div class="sc red"><i class="fas fa-shopping-cart sc-icon"></i><div class="sc-label">Panier moyen</div><div class="sc-val"><?= number_format($avg_cart, 0) ?> DH</div><div class="sc-sub">Par commande</div></div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
    <div class="panel">
        <div class="panel-head"><div class="panel-title"><i class="fas fa-chart-bar"></i> Évolution des ventes (12 mois)</div></div>
        <div class="panel-body"><canvas id="salesChart" height="200"></canvas></div>
    </div>
    <div class="panel">
        <div class="panel-head"><div class="panel-title"><i class="fas fa-chart-pie"></i> Répartition des commandes</div></div>
        <div class="panel-body"><canvas id="statusChart" height="200"></canvas></div>
    </div>
</div>

<div class="panel">
    <div class="panel-head"><div class="panel-title"><i class="fas fa-trophy"></i> Top 5 des produits les plus vendus</div></div>
    <div class="panel-body">
        <?php if(count($top_products) > 0): ?>
            <ul style="list-style: none;">
                <?php foreach($top_products as $name => $qty): ?>
                    <li style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border);">
                        <span><?= htmlspecialchars($name) ?></span>
                        <span style="color:var(--em); font-weight:bold;"><?= $qty ?> vendus</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucune donnée disponible</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: { labels: <?= json_encode(array_column($sales_by_month, 'month')) ?>, datasets: [{ label: 'Ventes (DH)', data: <?= json_encode(array_column($sales_by_month, 'total')) ?>, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)', fill: true, tension: 0.3 }] }
});
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: { labels: <?= json_encode($status_labels) ?>, datasets: [{ data: <?= json_encode($status_counts) ?>, backgroundColor: ['#f59e0b','#3b82f6','#a78bfa','#10b981','#ef4444'] }] }
});
</script>

<?php require_once 'footer.php'; ?>
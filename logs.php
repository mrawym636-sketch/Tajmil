<?php
$active_page='settings'; $page_title='Journal des actions';
require_once __DIR__ . '/config.php';
$logs = $pdo->query("SELECT * FROM logs ORDER BY date_created DESC LIMIT 200")->fetchAll();
require_once 'header.php';
?>
<div class="panel"><div class="panel-head"><div class="panel-title"><i class="fas fa-history"></i> Historique des actions</div></div><div class="tbl-wrap"><table class="dtable"><thead><tr><th>Date</th><th>Utilisateur</th><th>Action</th><th>Détails</th><th>IP</th></tr></thead><tbody><?php foreach($logs as $log): ?><tr><td><?= $log['date_created'] ?></td><td><?= htmlspecialchars($log['username']) ?></td><td><?= htmlspecialchars($log['action']) ?></td><td><?= htmlspecialchars($log['details']) ?></td><td><?= $log['ip_address'] ?></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php require_once 'footer.php'; ?>
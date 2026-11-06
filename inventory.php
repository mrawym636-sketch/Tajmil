<?php
$active_page='inventory'; $page_title='Gestion des stocks';
require_once __DIR__ . '/config.php';
if(isset($_POST['add_stock'])){
    $qty=(int)$_POST['quantity']; $pid=(int)$_POST['product_id'];
    if($qty>0) $pdo->prepare("UPDATE produits SET stock=stock+? WHERE id=?")->execute([$qty,$pid]);
    logAction($pdo, 'Ajout stock', "Produit ID $pid, +$qty");
    header("Location: inventory.php?success=stock_added"); exit();
}
require_once 'header.php';
$products=$pdo->query("SELECT id,nom,marque,prix,stock FROM produits ORDER BY stock ASC")->fetchAll();
$rupture=$pdo->query("SELECT COUNT(*) FROM produits WHERE stock=0")->fetchColumn();
$faible=$pdo->query("SELECT COUNT(*) FROM produits WHERE stock>0 AND stock<=5")->fetchColumn();
?>
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:22px"><div class="sc red"><i class="fas fa-times-circle sc-icon"></i><div class="sc-label">Rupture</div><div class="sc-val"><?= $rupture ?></div><div class="sc-sub">produits</div></div><div class="sc warn"><i class="fas fa-exclamation-triangle sc-icon"></i><div class="sc-label">Stock faible</div><div class="sc-val"><?= $faible ?></div><div class="sc-sub">≤5 unités</div></div><div class="sc em"><i class="fas fa-warehouse sc-icon"></i><div class="sc-label">Total SKU</div><div class="sc-val"><?= count($products) ?></div><div class="sc-sub">références</div></div></div>
<div class="panel"><div class="panel-head"><div class="panel-title"><i class="fas fa-warehouse"></i> Contrôle des stocks</div></div><div class="tbl-wrap"><table class="dtable"><thead><tr><th>#</th><th>Produit</th><th>Marque</th><th>Prix</th><th>Stock</th><th>Alerte</th><th>Réappro.</th></tr></thead><tbody><?php foreach($products as $p): $s=$p['stock']??0; $badge=$s==0?'badge-red':($s<=5?'badge-warn':'badge-em'); ?><tr><td style="color:var(--muted)">#<?= $p['id'] ?></td><td style="color:var(--text)"><?= htmlspecialchars($p['nom']) ?></td><td><?= htmlspecialchars($p['marque']) ?></td><td style="color:var(--em)"><?= number_format($p['prix'],2) ?> DH</td><td style="font-family:Syne,sans-serif;font-size:16px;font-weight:700;color:<?= $s==0?'var(--danger)':($s<=5?'var(--warning)':'var(--text)') ?>"><?= $s ?></td><td><span class="badge <?= $badge ?>"><?= $s==0?'Rupture':($s<=5?'Stock faible':'OK') ?></span></td><td><form method="POST" style="display:flex;gap:8px"><input type="hidden" name="product_id" value="<?= $p['id'] ?>"><select name="quantity" style="min-width:70px"><option value="5">+5</option><option value="10" selected>+10</option><option value="20">+20</option><option value="50">+50</option></select><button type="submit" name="add_stock" class="btn btn-em btn-xs"><i class="fas fa-plus"></i></button></form></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php require_once 'footer.php'; ?>
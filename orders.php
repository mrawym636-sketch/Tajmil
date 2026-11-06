<?php
$active_page='orders'; $page_title='Commandes';
require_once __DIR__ . '/config.php';
if(isset($_POST['update_status'])){
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    $stmt = $pdo->prepare("SELECT statut FROM commandes WHERE id=?");
    $stmt->execute([$order_id]);
    $old_status = $stmt->fetchColumn();
    if($old_status != $new_status) {
        $pdo->prepare("UPDATE commandes SET statut=? WHERE id=?")->execute([$new_status, $order_id]);
        logCommandeStatus($pdo, $order_id, $old_status, $new_status);
        logAction($pdo, 'Changement statut commande', "Commande #$order_id : $old_status → $new_status");
    }
    header("Location: orders.php?success=status_updated"); exit();
}
$search = $_GET['search'] ?? ''; $status = $_GET['status'] ?? ''; $date_from = $_GET['date_from'] ?? ''; $date_to = $_GET['date_to'] ?? '';
$where = []; $params = [];
if (!empty($search)) { $where[] = "(numero LIKE ? OR nom_client LIKE ? OR email_client LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
if (!empty($status)) { $where[] = "statut = ?"; $params[] = $status; }
if (!empty($date_from)) { $where[] = "DATE(date_commande) >= ?"; $params[] = $date_from; }
if (!empty($date_to)) { $where[] = "DATE(date_commande) <= ?"; $params[] = $date_to; }
$sql = "SELECT * FROM commandes";
if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY date_commande DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();
$sc = ['En attente'=>'warn','En traitement'=>'blue','Expédiée'=>'purple','Livrée'=>'em','Annulée'=>'red'];
require_once 'header.php';
?>
<div class="panel">
    <div class="panel-head"><div class="panel-title"><i class="fas fa-shopping-bag"></i> Commandes (<?= count($orders) ?>)</div></div>
    <div class="panel-body">
        <form method="GET" style="display:flex; flex-wrap:wrap; gap:10px; margin-bottom:20px;">
            <input type="text" name="search" placeholder="N° commande, client, email" value="<?= htmlspecialchars($search) ?>" style="flex:2; padding:8px;">
            <select name="status" style="padding:8px;"><option value="">Tous statuts</option><?php foreach(['En attente','En traitement','Expédiée','Livrée','Annulée'] as $s): ?><option <?= $status==$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select>
            <input type="date" name="date_from" value="<?= $date_from ?>" placeholder="Date début">
            <input type="date" name="date_to" value="<?= $date_to ?>" placeholder="Date fin">
            <button type="submit" class="btn btn-em">Filtrer</button>
            <a href="export_orders.php?search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>" class="btn btn-ghost">Exporter CSV</a>
            <a href="orders.php" class="btn btn-ghost">Réinitialiser</a>
        </form>
    </div>
    <div class="tbl-wrap"><table class="dtable"><thead><tr><th>N°</th><th>Client</th><th>Total</th><th>Date</th><th>Statut</th><th>Action</th></tr></thead><tbody>
    <?php foreach($orders as $o): $cls=$sc[$o['statut']]??'gray'; ?>
    <tr><td style="color:var(--em)"><?= htmlspecialchars($o['numero']) ?></td><td><div><?= htmlspecialchars($o['nom_client']) ?></div><div style="font-size:11px;color:var(--muted)"><?= htmlspecialchars($o['telephone_client']??'') ?></div></td><td><?= number_format($o['total'],2) ?> DH</td><td><?= date('d/m/Y H:i', strtotime($o['date_commande'])) ?></td>
    <td><form method="POST" style="display:inline;"><input type="hidden" name="order_id" value="<?= $o['id'] ?>"><select name="status" onchange="this.form.submit()" style="background:var(--surface2); border:1px solid var(--border2); padding:5px 28px 5px 10px; border-radius:20px;"><?php foreach(['En attente','En traitement','Expédiée','Livrée','Annulée'] as $s): ?><option value="<?= $s ?>" <?= $o['statut']==$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select><input type="hidden" name="update_status" value="1"></form></td>
    <td><a href="order-details.php?id=<?= $o['id'] ?>" class="btn btn-ghost btn-xs">Détails</a></td></tr>
    <?php endforeach; ?>
    </tbody></table></div></div>
<?php require_once 'footer.php'; ?>
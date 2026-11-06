<?php
$active_page='orders'; $page_title='Détail commande';
require_once __DIR__ . '/config.php';

$id = (int)($_GET['id']??0);
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE id=?");
$stmt->execute([$id]);
$o = $stmt->fetch();
if(!$o){ header('Location: orders.php'); exit(); }

$items = json_decode($o['produits'], true) ?? [];

// جلب رسوم التوصيل من قاعدة البيانات (من صفحة الإعدادات)
$stmt_delivery = $pdo->query("SELECT delivery_fee FROM settings WHERE id=1");
$delivery_fee = (float)($stmt_delivery->fetchColumn() ?? 0);

$sc = ['En attente'=>'warn','En traitement'=>'blue','Expédiée'=>'purple','Livrée'=>'em','Annulée'=>'red'];
$cls = $sc[$o['statut']]??'gray';

require_once 'header.php';
?>

<div style="margin-bottom:18px">
    <a href="orders.php" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> Retour</a>
    <button onclick="window.print()" class="btn btn-ghost btn-sm"><i class="fas fa-print"></i> Imprimer</button>
    <a href="send_whatsapp.php?order_id=<?= $id ?>" class="btn btn-em btn-sm" onclick="return confirm('Envoyer notification WhatsApp ?')"><i class="fab fa-whatsapp"></i> Notifier client</a>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:18px">
    <div class="panel">
        <div class="panel-head">
            <div class="panel-title"><i class="fas fa-user"></i> Client</div>
            <span class="badge badge-<?=$cls?>"><?= htmlspecialchars($o['statut']) ?></span>
        </div>
        <div class="panel-body">
            <?php $rows = [
                ['N° Commande','<span style="color:var(--em)">'.htmlspecialchars($o['numero']).'</span>'],
                ['Nom',htmlspecialchars($o['nom_client'])],
                ['Email',htmlspecialchars($o['email_client'])],
                ['Téléphone',htmlspecialchars($o['telephone_client']??'—')],
                ['Date',date('d/m/Y H:i', strtotime($o['date_commande']))]
            ]; 
            foreach($rows as $r): ?>
            <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--border)">
                <span style="font-size:11px; text-transform:uppercase; color:var(--muted)"><?= $r[0] ?></span>
                <span style="color:var(--text)"><?= $r[1] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="panel">
        <div class="panel-head">
            <div class="panel-title"><i class="fas fa-receipt"></i> Résumé</div>
        </div>
        <div class="panel-body">
            <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--border)">
                <span><?= count($items) ?> article(s)</span>
                <span><?= number_format($o['total'],2) ?> DH</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--border)">
                <span>Livraison</span>
                <?php if($delivery_fee > 0): ?>
                    <span style="color:var(--em)"><?= number_format($delivery_fee, 2) ?> DH</span>
                <?php else: ?>
                    <span style="color:var(--em)">Gratuite</span>
                <?php endif; ?>
            </div>
            <div style="display:flex; justify-content:space-between; margin-top:4px">
                <span>Total</span>
                <span style="font-size:28px; color:var(--em)"><?= number_format($o['total'],2) ?> DH</span>
            </div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="fas fa-box-open"></i> Produits commandés</div>
    </div>
    <div class="tbl-wrap">
        <table style="width:100%">
            <thead>
                <tr><th>Produit</th><th>Qté</th><th>Prix unit.</th><th>Sous-total</th></tr>
            </thead>
            <tbody>
                <?php foreach($items as $it): $px=(float)($it['prix']??0); $qty=(int)($it['qty']??1); ?>
                <tr>
                    <td style="text-align:left; padding:8px;"><?= htmlspecialchars($it['nom']??'') ?></td>
                    <td style="text-align:center"><?= $qty ?></td>
                    <td style="text-align:right"><?= number_format($px,2) ?> DH</td>
                    <td style="text-align:right;color:var(--em)"><?= number_format($px*$qty,2) ?> DH</td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" style="text-align:right"><strong>Total produits</strong></td>
                    <td style="text-align:right;color:var(--em)"><strong><?= number_format($o['total'] - $delivery_fee, 2) ?> DH</strong></td>
                </tr>
                <?php if($delivery_fee > 0): ?>
                <tr>
                    <td colspan="3" style="text-align:right"><strong>Livraison</strong></td>
                    <td style="text-align:right;color:var(--em)"><strong><?= number_format($delivery_fee, 2) ?> DH</strong></td>
                </tr>
                <?php endif; ?>
                <tr style="border-top:2px solid var(--border);">
                    <td colspan="3" style="text-align:right"><strong>Total général</strong></td>
                    <td style="text-align:right;font-size:20px;color:var(--em)"><strong><?= number_format($o['total'],2) ?> DH</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php $logs = $pdo->prepare("SELECT * FROM commande_logs WHERE commande_id = ? ORDER BY date_modif DESC"); $logs->execute([$id]); if($logs->rowCount() > 0): ?>
<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="fas fa-history"></i> Historique des statuts</div>
    </div>
    <div class="tbl-wrap">
        <table style="width:100%">
            <thead>
                <tr><th>Date</th><th>Admin</th><th>Ancien statut</th><th>Nouveau statut</th></tr>
            </thead>
            <tbody>
                <?php while($log = $logs->fetch()): ?>
                <tr>
                    <td><?= $log['date_modif'] ?></td>
                    <td><?= htmlspecialchars($log['admin_nom']) ?></td>
                    <td><?= $log['ancien_statut'] ?></td>
                    <td><?= $log['nouveau_statut'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>
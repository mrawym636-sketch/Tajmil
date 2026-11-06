<?php
$active_page='analytics'; $page_title='Analyses Financières';
require_once __DIR__ . '/config.php';

$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to = $_GET['date_to'] ?? date('Y-m-t');

// ========== نظام تعدد العملات (Multi-Devise) ==========
// جلب إعدادات العملات من جدول settings
$stmt = $pdo->query("SELECT rate_EUR, rate_USD, current_currency FROM settings WHERE id=1");
$currency_settings = $stmt->fetch();
$rate_EUR = $currency_settings['rate_EUR'] ?? 0.091;
$rate_USD = $currency_settings['rate_USD'] ?? 0.10;

// العملة المختارة (من GET أو الافتراضية)
$currency = $_GET['currency'] ?? ($currency_settings['current_currency'] ?? 'DH');

// تحديد سعر الصرف حسب العملة
if ($currency == 'EUR') $rate = $rate_EUR;
elseif ($currency == 'USD') $rate = $rate_USD;
else $rate = 1;

// دالة تحويل العملة
function convertCurrency($amount, $currency, $rate) {
    $converted = $amount * $rate;
    if ($currency == 'DH') return number_format($converted, 0) . ' DH';
    elseif ($currency == 'EUR') return '€' . number_format($converted, 2);
    else return '$' . number_format($converted, 2);
}
// ====================================================

// إحصائيات الفترة
$panier = $pdo->prepare("SELECT AVG(total) FROM commandes WHERE date_commande BETWEEN ? AND ?"); 
$panier->execute([$date_from, $date_to]); 
$panier = $panier->fetchColumn()??0;

$total_cmd = $pdo->prepare("SELECT COUNT(*) FROM commandes WHERE date_commande BETWEEN ? AND ?"); 
$total_cmd->execute([$date_from, $date_to]); 
$total_cmd = $total_cmd->fetchColumn()?:1;

$livree = $pdo->prepare("SELECT COUNT(*) FROM commandes WHERE statut='Livrée' AND date_commande BETWEEN ? AND ?"); 
$livree->execute([$date_from, $date_to]); 
$livree = $livree->fetchColumn();
$taux = round(($livree/$total_cmd)*100,1);

$en_cours = $pdo->prepare("SELECT COUNT(*) FROM commandes WHERE statut IN('En attente','En traitement') AND date_commande BETWEEN ? AND ?"); 
$en_cours->execute([$date_from, $date_to]); 
$en_cours = $en_cours->fetchColumn();

$statuts = $pdo->prepare("SELECT statut, COUNT(*) as nbr, SUM(total) as rev FROM commandes WHERE date_commande BETWEEN ? AND ? GROUP BY statut"); 
$statuts->execute([$date_from, $date_to]); 
$statuts = $statuts->fetchAll();

$total_rev = $pdo->prepare("SELECT SUM(total) FROM commandes WHERE date_commande BETWEEN ? AND ?"); 
$total_rev->execute([$date_from, $date_to]); 
$total_rev = $total_rev->fetchColumn()?:1;

// Top stocks
$top = $pdo->query("SELECT nom, prix, stock FROM produits ORDER BY stock DESC LIMIT 5")->fetchAll();
$low = $pdo->query("SELECT nom, stock FROM produits ORDER BY stock ASC LIMIT 5")->fetchAll();

// حساب الأرباح الصافية
$stmt = $pdo->query("SELECT SUM(prix_achat * stock) FROM produits");
$total_achat = $stmt->fetchColumn() ?? 0;
$benefice_brut = $total_rev - $total_achat;
$profit_rate = $total_rev > 0 ? round(($benefice_brut / $total_rev) * 100, 1) : 0;

// المنتجات الأكثر ربحية
$profit_products = $pdo->query("
    SELECT nom, prix, prix_achat, (prix - prix_achat) as profit 
    FROM produits 
    WHERE prix_achat > 0 
    ORDER BY profit DESC 
    LIMIT 5
")->fetchAll();

// إحصائيات إضافية
$total_clients = $pdo->query("SELECT COUNT(DISTINCT telephone_client) FROM commandes")->fetchColumn();
$avg_order = $total_cmd > 0 ? round($total_rev / $total_cmd, 0) : 0;

require_once 'header.php';
?>

<style>
    .profit-card {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 20px;
        padding: 25px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .profit-card::after {
        content: '';
        position: absolute;
        top: -30px;
        right: -30px;
        width: 150px;
        height: 150px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    .profit-card .profit-value {
        font-size: 42px;
        font-weight: 800;
        margin-bottom: 5px;
    }
    .profit-card .profit-label {
        font-size: 14px;
        opacity: 0.9;
    }
    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px;
        transition: transform 0.2s, border-color 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        border-color: var(--em);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: var(--em-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }
    .stat-icon i {
        font-size: 24px;
        color: var(--em);
    }
    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
    }
    .stat-label {
        font-size: 13px;
        color: var(--muted);
        margin-top: 5px;
    }
    .filter-bar {
        background: var(--surface);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
        border: 1px solid var(--border);
    }
    .select-currency {
        background: var(--surface2);
        border: 1px solid var(--border2);
        color: white;
        padding: 10px;
        border-radius: 8px;
        cursor: pointer;
    }
</style>

<!-- ========== فلتر التاريخ والعملة ========== -->
<div class="filter-bar">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
        <h3 style="font-size:16px; font-weight:600;"><i class="fas fa-calendar-alt"></i> Période & Devise</h3>
        <a href="analytics.php" class="btn btn-ghost btn-sm">Aujourd'hui</a>
    </div>
    <form method="GET" style="display:flex; gap:15px; flex-wrap:wrap; align-items:flex-end;">
        <div>
            <label style="font-size:12px; color:var(--muted);">Date début</label>
            <input type="date" name="date_from" value="<?= $date_from ?>" style="background:var(--surface2); border:1px solid var(--border2); border-radius:8px; padding:10px; color:white;">
        </div>
        <div>
            <label style="font-size:12px; color:var(--muted);">Date fin</label>
            <input type="date" name="date_to" value="<?= $date_to ?>" style="background:var(--surface2); border:1px solid var(--border2); border-radius:8px; padding:10px; color:white;">
        </div>
        <div>
            <label style="font-size:12px; color:var(--muted);">Devise</label>
            <select name="currency" class="select-currency">
                <option value="DH" <?= $currency == 'DH' ? 'selected' : '' ?>>MAD (DH)</option>
                <option value="EUR" <?= $currency == 'EUR' ? 'selected' : '' ?>>EUR (€)</option>
                <option value="USD" <?= $currency == 'USD' ? 'selected' : '' ?>>USD ($)</option>
            </select>
        </div>
        <button type="submit" class="btn btn-em">Appliquer</button>
    </form>
</div>

<!-- ========== Cartes principales ========== -->
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:20px; margin-bottom:25px;">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
        <div class="stat-value"><?= number_format($total_cmd, 0) ?></div>
        <div class="stat-label">Commandes totales</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-value"><?= number_format($total_clients, 0) ?></div>
        <div class="stat-label">Clients uniques</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
        <div class="stat-value"><?= convertCurrency($avg_order, $currency, $rate) ?></div>
        <div class="stat-label">Panier moyen</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-percent"></i></div>
        <div class="stat-value"><?= $taux ?>%</div>
        <div class="stat-label">Taux livraison</div>
    </div>
</div>

<!-- ========== Carte de profit (mise en avant) ========== -->
<div class="profit-card" style="margin-bottom:25px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
        <div>
            <div class="profit-label">Bénéfice net (période)</div>
            <div class="profit-value"><?= convertCurrency($benefice_brut, $currency, $rate) ?></div>
            <div style="font-size:13px; margin-top:8px;">
                📊 Chiffre d'affaires: <?= convertCurrency($total_rev, $currency, $rate) ?> &nbsp;|&nbsp;
                💰 Coût d'achat: <?= convertCurrency($total_achat, $currency, $rate) ?>
            </div>
        </div>
        <div style="text-align:center;">
            <div style="font-size:36px; font-weight:800;"><?= $profit_rate ?>%</div>
            <div style="font-size:12px; opacity:0.8;">Marge bénéficiaire</div>
        </div>
    </div>
</div>

<!-- ========== Deux colonnes ========== -->
<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:25px;">
    <!-- CA par statut -->
    <div class="panel">
        <div class="panel-head">
            <div class="panel-title"><i class="fas fa-chart-pie"></i> Répartition CA par statut</div>
        </div>
        <div class="panel-body">
            <?php foreach($statuts as $st): $pct=($total_rev>0)?($st['rev']/$total_rev)*100:0; 
                $col=['En attente'=>'#f59e0b','En traitement'=>'#3b82f6','Expédiée'=>'#a78bfa','Livrée'=>'#10b981','Annulée'=>'#ef4444']; 
                $c=$col[$st['statut']]??'#6b7280'; ?>
            <div style="margin-bottom:15px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                    <span><?= htmlspecialchars($st['statut']) ?></span>
                    <span><?= convertCurrency($st['rev'], $currency, $rate) ?> (<?= round($pct,1) ?>%)</span>
                </div>
                <div style="height:6px; background:var(--surface3); border-radius:99px; overflow:hidden;">
                    <div style="width:<?= round($pct,1) ?>%; height:100%; background:<?= $c ?>; border-radius:99px;"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Top produits par marge -->
    <div class="panel">
        <div class="panel-head">
            <div class="panel-title"><i class="fas fa-crown"></i> Top produits par marge</div>
        </div>
        <div class="panel-body" style="padding:0;">
            <?php if(count($profit_products) > 0): ?>
                <?php foreach($profit_products as $i => $p): ?>
                <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 18px; border-bottom:1px solid var(--border);">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:28px; height:28px; background:var(--em-bg); border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; color:var(--em);">#<?= $i+1 ?></div>
                        <div>
                            <div style="font-weight:500;"><?= htmlspecialchars($p['nom']) ?></div>
                            <div style="font-size:11px; color:var(--muted);">Achat: <?= convertCurrency($p['prix_achat'], $currency, $rate) ?> | Vente: <?= convertCurrency($p['prix'], $currency, $rate) ?></div>
                        </div>
                    </div>
                    <span style="color:var(--em); font-weight:bold;">+<?= convertCurrency($p['profit'], $currency, $rate) ?></span>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align:center; padding:40px;">
                    <i class="fas fa-box-open" style="font-size:40px; color:var(--muted); margin-bottom:15px; display:block;"></i>
                    <p>Aucune donnée disponible</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ========== Gestion des stocks ========== -->
<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
    <div class="panel">
        <div class="panel-head">
            <div class="panel-title"><i class="fas fa-arrow-trend-up"></i> Top stocks</div>
        </div>
        <div class="panel-body" style="padding:0;">
            <?php foreach($top as $p): ?>
            <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 18px; border-bottom:1px solid var(--border);">
                <div><?= htmlspecialchars($p['nom']) ?></div>
                <span class="badge badge-em"><?= $p['stock'] ?> u.</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="panel">
        <div class="panel-head">
            <div class="panel-title"><i class="fas fa-triangle-exclamation"></i> Stocks critiques</div>
        </div>
        <div class="panel-body" style="padding:0;">
            <?php foreach($low as $p): $s=$p['stock']??0; ?>
            <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 18px; border-bottom:1px solid var(--border);">
                <div><?= htmlspecialchars($p['nom']) ?></div>
                <span class="badge <?= $s==0?'badge-red':($s<=5?'badge-warn':'badge-em') ?>"><?= $s ?> u.</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
<?php require_once __DIR__ . '/config.php';
$page_title = $page_title ?? 'Dashboard';
$active_page = $active_page ?? 'dashboard';
$initials = strtoupper(substr($_SESSION['user_nom'] ?? 'A', 0, 2));

// Récupération des paramètres du thème
$settings = $pdo->query("SELECT * FROM settings WHERE id=1")->fetch();
$low_stock_count = $pdo->query("SELECT COUNT(*) FROM produits WHERE stock <= 5 AND stock > 0")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($page_title) ?> — Tajmil Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
:root{
  --bg:#0a0a0c;
  --surface:#111114;
  --surface2:#18181c;
  --surface3:#1e1e23;
  --border:rgba(255,255,255,0.06);
  --border2:rgba(255,255,255,0.09);
  --em:<?= $settings['primary_color'] ?? '#10b981' ?>;
  --em2:#34d399;
  --em-bg:rgba(16,185,129,0.08);
  --em-border:rgba(16,185,129,0.2);
  --text:#f1f1f1;
  --text2:#c4c4cc;
  --muted:#6b7280;
  --muted2:#9ca3af;
  --danger:#ef4444;
  --warning:#f59e0b;
  --blue:#3b82f6;
  --sidebar:240px;
  --topbar:58px;
}
*{margin:0;padding:0;box-sizing:border-box}
html,body{height:100%}
body{font-family:'Outfit',sans-serif;background:var(--bg);color:var(--text);display:flex;overflow:hidden}

/* SIDEBAR */
.sidebar{width:var(--sidebar);flex-shrink:0;height:100vh;background:var(--surface);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;z-index:200;transition:transform .25s}
.sidebar-brand{padding:20px 20px 0;border-bottom:1px solid var(--border);padding-bottom:18px}
.brand-inner{display:flex;align-items:center;gap:11px}
.brand-ic{width:34px;height:34px;background:var(--em-bg);border:1px solid var(--em-border);border-radius:9px;display:flex;align-items:center;justify-content:center;color:var(--em);font-size:15px}
.brand-name{font-family:'Syne',sans-serif;font-size:17px;font-weight:700;letter-spacing:-.2px}
.brand-name em{color:var(--em);font-style:normal}
.brand-tag{font-size:9px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-top:2px}
.sidebar-scroll{flex:1;overflow-y:auto;padding:14px 0}
.nav-label{font-size:9.5px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);padding:12px 20px 5px}
.nav-link{display:flex;align-items:center;gap:10px;padding:9px 14px 9px 20px;color:var(--muted2);text-decoration:none;font-size:13.5px;border-left:2px solid transparent;margin:1px 0;border-radius:0 8px 8px 0;margin-right:8px}
.nav-link i{width:16px;text-align:center;font-size:13px}
.nav-link:hover{background:rgba(255,255,255,0.04);color:var(--text2)}
.nav-link.active{background:var(--em-bg);color:var(--em);border-left-color:var(--em)}
.sidebar-foot{padding:14px 16px;border-top:1px solid var(--border)}
.sf-link{display:flex;align-items:center;gap:9px;padding:9px 14px;color:var(--muted);text-decoration:none;font-size:13px;border-radius:8px}
.sf-link:hover{background:rgba(255,255,255,0.04);color:var(--text2)}
.sf-link.logout:hover{background:rgba(239,68,68,0.08);color:#fca5a5}
.layout{margin-left:var(--sidebar);display:flex;flex-direction:column;min-height:100vh;flex:1;overflow:hidden}
.topbar{height:var(--topbar);background:var(--surface);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 28px;flex-shrink:0;position:sticky;top:0;z-index:100}
.topbar-left{display:flex;align-items:center;gap:14px}
.menu-btn{display:none;background:none;border:none;color:var(--muted2);cursor:pointer;font-size:18px}
.page-title-wrap .ptitle{font-family:'Syne',sans-serif;font-size:18px;font-weight:700}
.page-title-wrap .pbread{font-size:11px;color:var(--muted);margin-top:1px}
.topbar-right{display:flex;align-items:center;gap:10px}
.tb-icon{width:34px;height:34px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--muted2);cursor:pointer;position:relative}
.tb-icon:hover{background:var(--surface3);color:var(--text)}
.user-btn{display:flex;align-items:center;gap:9px;padding:5px 10px 5px 5px;background:var(--surface2);border:1px solid var(--border2);border-radius:24px;cursor:pointer;position:relative}
.uavatar{width:28px;height:28px;background:var(--em-bg);border:1px solid var(--em-border);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--em)}
.uname{font-size:13px;color:var(--text2)}
.user-dd{position:absolute;top:calc(100%+6px);right:0;background:var(--surface2);border:1px solid var(--border3);border-radius:12px;min-width:180px;padding:6px;display:none;z-index:999;box-shadow:0 16px 40px rgba(0,0,0,.5)}
.user-btn:hover .user-dd{display:block}
.dd-item{display:flex;align-items:center;gap:9px;padding:9px 12px;color:var(--muted2);text-decoration:none;font-size:13px;border-radius:8px}
.dd-item:hover{background:rgba(255,255,255,.05);color:var(--text)}
.dd-item.red:hover{background:rgba(239,68,68,.08);color:#fca5a5}
.dd-sep{height:1px;background:var(--border);margin:4px 0}
.main{flex:1;padding:28px 32px;overflow-y:auto}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:26px}
.sc{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:22px;position:relative;transition:all .2s}
.sc:hover{transform:translateY(-2px);border-color:var(--border2)}
.sc .sc-icon{position:absolute;top:20px;right:20px;font-size:18px;opacity:.18}
.sc.em .sc-icon{color:var(--em)}
.sc.blue .sc-icon{color:var(--blue)}
.sc.warn .sc-icon{color:var(--warning)}
.sc.red .sc-icon{color:var(--danger)}
.sc-label{font-size:11px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--muted);margin-bottom:12px}
.sc-val{font-family:'Syne',sans-serif;font-size:32px;font-weight:800;line-height:1;margin-bottom:6px}
.sc.em .sc-val{color:var(--em)}
.sc.blue .sc-val{color:var(--blue)}
.sc.warn .sc-val{color:var(--warning)}
.sc.red .sc-val{color:var(--danger)}
.sc-sub{font-size:12px;color:var(--muted)}
.panel{background:var(--surface);border:1px solid var(--border);border-radius:14px;margin-bottom:22px;overflow:hidden}
.panel-head{padding:16px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
.panel-title{font-family:'Syne',sans-serif;font-size:15px;font-weight:600;display:flex;align-items:center;gap:8px}
.panel-title i{color:var(--em);font-size:14px}
.panel-body{padding:22px}
.tbl-wrap{overflow-x:auto}
table.dtable{width:100%!important;border-collapse:collapse;font-size:13px}
table.dtable thead th{background:var(--surface2);padding:11px 16px;text-align:left;font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--muted)}
table.dtable tbody tr{border-bottom:1px solid var(--border)}
table.dtable tbody tr:hover{background:rgba(255,255,255,.02)}
table.dtable tbody td{padding:13px 16px;color:var(--text2);vertical-align:middle}
.dataTables_wrapper{color:var(--text2);font-size:13px}
.dataTables_filter input{background:var(--surface2)!important;border:1px solid var(--border2)!important;color:var(--text)!important;padding:7px 12px!important;border-radius:8px!important}
.dataTables_filter input:focus{border-color:var(--em-border)!important;box-shadow:0 0 0 3px var(--em-bg)!important}
.dataTables_length select{background:var(--surface2)!important;border:1px solid var(--border2)!important;color:var(--text)!important;padding:5px 8px!important;border-radius:8px!important}
.dataTables_paginate .paginate_button{padding:5px 10px!important;border-radius:7px!important;color:var(--muted)!important;background:transparent!important}
.dataTables_paginate .paginate_button.current,.dataTables_paginate .paginate_button:hover{background:var(--em-bg)!important;color:var(--em)!important}
.dt-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;padding:0 22px}
.dt-bottom{display:flex;justify-content:space-between;align-items:center;margin-top:14px;flex-wrap:wrap;gap:10px;padding:0 22px 16px}
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.badge-em{background:rgba(16,185,129,.1);color:#6ee7b7;border:1px solid rgba(16,185,129,.2)}
.badge-blue{background:rgba(59,130,246,.1);color:#93c5fd;border:1px solid rgba(59,130,246,.2)}
.badge-warn{background:rgba(245,158,11,.1);color:#fcd34d;border:1px solid rgba(245,158,11,.2)}
.badge-red{background:rgba(239,68,68,.1);color:#fca5a5;border:1px solid rgba(239,68,68,.2)}
.btn{display:inline-flex;align-items:center;gap:7px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:500;border:none;cursor:pointer;text-decoration:none;transition:all .2s}
.btn-em{background:var(--em);color:#0a0a0c}
.btn-em:hover{background:var(--em2);transform:translateY(-1px);box-shadow:0 4px 12px rgba(16,185,129,.3)}
.btn-ghost{background:transparent;border:1px solid var(--border2);color:var(--muted2)}
.btn-ghost:hover{border-color:var(--em-border);color:var(--em);background:rgba(16,185,129,.05)}
.btn-danger{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#fca5a5}
.btn-xs{padding:4px 9px;font-size:11px;border-radius:6px}
.prod-img{width:40px;height:40px;border-radius:8px;object-fit:cover;border:1px solid var(--border2)}
.prod-img-ph{width:40px;height:40px;border-radius:8px;background:var(--surface2);border:1px solid var(--border);display:inline-flex;align-items:center;justify-content:center;color:var(--muted);font-size:13px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.fg{margin-bottom:18px}
.fg label{display:block;font-size:11px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--muted);margin-bottom:7px}
.fg input,.fg select,.fg textarea{width:100%;padding:11px 14px;background:var(--surface2);border:1px solid var(--border2);border-radius:9px;color:var(--text);font-family:'Outfit',sans-serif;font-size:14px;outline:none;transition:all .2s}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:var(--em-border);box-shadow:0 0 0 3px var(--em-bg)}
.fg textarea{resize:vertical;min-height:90px}
.form-actions{display:flex;gap:10px;margin-top:20px}
.toast{position:fixed;bottom:24px;right:24px;background:var(--surface);border:1px solid var(--em-border);padding:13px 20px;border-radius:12px;display:flex;align-items:center;gap:10px;z-index:9999;opacity:0;transform:translateY(16px);transition:all .35s;pointer-events:none;color:var(--text)}
.toast.show{opacity:1;transform:translateY(0)}
@media(max-width:1100px){.stats-grid{grid-template-columns:1fr 1fr}}
@media(max-width:860px){
  .sidebar{transform:translateX(-100%)}
  .sidebar.open{transform:translateX(0)}
  .layout{margin-left:0}
  .menu-btn{display:block!important}
  .stats-grid{grid-template-columns:1fr}
}
</style>
</head>
<body>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand"><div class="brand-inner"><div class="brand-ic"><i class="fas fa-spa"></i></div><div><div class="brand-name">Tajmil<em>.</em></div><span class="brand-tag">Admin Panel</span></div></div></div>
  <div class="sidebar-scroll">
    <div class="nav-label">Principal</div>
    <a href="index.php" class="nav-link <?= $active_page==='dashboard'?'active':'' ?>"><i class="fas fa-chart-line"></i> Dashboard</a>
    <div class="nav-label">E-commerce</div>
    <a href="products.php" class="nav-link <?= $active_page==='products'?'active':'' ?>"><i class="fas fa-box-open"></i> Produits</a>
    <a href="categories.php" class="nav-link <?= $active_page==='products'?'active':'' ?>"><i class="fas fa-tags"></i> Catégories</a>
    <a href="brands.php" class="nav-link <?= $active_page==='products'?'active':'' ?>"><i class="fas fa-trademark"></i> Marques</a>
    <a href="inventory.php" class="nav-link <?= $active_page==='inventory'?'active':'' ?>"><i class="fas fa-warehouse"></i> Stock</a>
    <a href="orders.php" class="nav-link <?= $active_page==='orders'?'active':'' ?>"><i class="fas fa-shopping-bag"></i> Commandes</a>
    <a href="coupons.php" class="nav-link <?= $active_page==='coupons'?'active':'' ?>"><i class="fas fa-ticket-alt"></i> Codes promo</a>
    <div class="nav-label">Clients</div>
    <a href="users.php" class="nav-link <?= $active_page==='users'?'active':'' ?>"><i class="fas fa-users"></i> Utilisateurs</a>
    <a href="reviews.php" class="nav-link <?= $active_page==='reviews'?'active':'' ?>"><i class="fas fa-star"></i> Avis clients</a>
    <a href="crud_users.php" class="nav-link <?= $active_page === 'crud_users' ? 'active' : '' ?>">
        <i class="fas fa-user-plus"></i> Gestion utilisateurs
    </a>
    <div class="nav-label">Marketing</div>
    <a href="newsletter.php" class="nav-link <?= $active_page==='users'?'active':'' ?>"><i class="fas fa-envelope"></i> Newsletter</a>
    <div class="nav-label">Contenu</div>
    <a href="pages.php" class="nav-link <?= $active_page==='settings'?'active':'' ?>"><i class="fas fa-file-alt"></i> Pages</a>
    <div class="nav-label">Reporting</div>
    <a href="analytics.php" class="nav-link <?= $active_page==='analytics'?'active':'' ?>"><i class="fas fa-chart-pie"></i> Analyses</a>
    <a href="logs.php" class="nav-link <?= $active_page==='settings'?'active':'' ?>"><i class="fas fa-history"></i> Logs</a>
    <div class="nav-label">Config</div>
    <a href="settings.php" class="nav-link <?= $active_page==='settings'?'active':'' ?>"><i class="fas fa-sliders-h"></i> Paramètres</a>
    <a href="backup.php" class="nav-link <?= $active_page==='settings'?'active':'' ?>"><i class="fas fa-database"></i> Sauvegarde</a>
  </div>
  <div class="sidebar-foot">
    <a href="../pages/home.php" target="_blank" class="sf-link"><i class="fas fa-external-link-alt"></i> Voir le site</a>
    <a href="logout.php" class="sf-link logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
  </div>
</aside>
<div class="layout">
  <div class="topbar">
    <div class="topbar-left"><button class="menu-btn" id="menuBtn" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fas fa-bars"></i></button><div class="page-title-wrap"><div class="ptitle"><?= htmlspecialchars($page_title) ?></div><div class="pbread">Admin / <?= htmlspecialchars($page_title) ?></div></div></div>
    <div class="topbar-right">
      <a href="inventory.php" class="tb-icon" style="position:relative;">
        <i class="fas fa-boxes"></i>
        <?php if($low_stock_count > 0): ?>
          <span class="badge-notif" style="position:absolute; top:-5px; right:-5px; background:#ef4444; color:white; font-size:9px; padding:2px 5px; border-radius:50%;"><?= $low_stock_count ?></span>
        <?php endif; ?>
      </a>
      <a href="../pages/home.php" target="_blank" class="tb-icon"><i class="fas fa-globe"></i></a>
      <div class="user-btn">
        <div class="uavatar"><?= $initials ?></div>
        <span class="uname"><?= htmlspecialchars($_SESSION['user_nom'] ?? 'Admin') ?></span>
        <i class="fas fa-chevron-down uchev"></i>
        <div class="user-dd">
          <a href="../pages/home.php" target="_blank" class="dd-item"><i class="fas fa-globe"></i> Voir le site</a>
          <div class="dd-sep"></div>
          <a href="logout.php" class="dd-item red"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
      </div>
    </div>
  </div>
  <div class="main">
    <div id="appToast" class="toast"><i class="fas fa-check-circle"></i> <span id="toastMsg"></span></div>
    <script>
    function showToast(msg, type='success') {
      const toast = document.getElementById('appToast');
      toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> <span>${msg}</span>`;
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 3500);
    }
    document.addEventListener('click', function(e) {
      const sb = document.getElementById('sidebar');
      const btn = document.getElementById('menuBtn');
      if(sb && sb.classList.contains('open') && !sb.contains(e.target) && btn && !btn.contains(e.target)) sb.classList.remove('open');
    });
    </script>
<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
require_once '../php_files/config.php';

$user_info = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_info = $stmt->fetch();
    if ($user_info) {
        $_SESSION['user_photo'] = $user_info['photo'] ?? 'default.png';
        $_SESSION['user_nom'] = $user_info['nom'];
    }
}

try { $pdo->query("SELECT 1 FROM commandes LIMIT 1"); $table_exists = true; } catch(PDOException $e) { $table_exists = false; }
$commandes = [];
if ($table_exists) {
    try { $stmt = $pdo->prepare("SELECT * FROM commandes WHERE user_id = ? ORDER BY date_commande DESC"); $stmt->execute([$_SESSION['user_id']]); $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC); } catch(PDOException $e) { $commandes = []; }
}
$cart_count = getCartCount();
$wishlist_count = getWishlistCount();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes commandes - Tajmil Lwajh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#f8f9fa;padding-top:80px}
        header{position:fixed;top:0;left:0;right:0;background:rgba(255,255,255,0.98);backdrop-filter:blur(10px);box-shadow:0 2px 15px rgba(0,0,0,0.05);z-index:1000;padding:15px 5%}
        .header-container{max-width:1400px;margin:0 auto;display:flex;align-items:center;justify-content:space-between}
        .header-left{display:flex;align-items:center;gap:15px}
        .logo{font-size:28px;font-weight:800;text-decoration:none;color:#1a1a2e;letter-spacing:-0.5px}
        .logo span{color:#e84393}
        .navbar{display:flex;gap:35px}
        .navbar a{text-decoration:none;color:#4a4a6a;font-weight:500;font-size:15px;transition:0.3s}
        .navbar a:hover,.navbar a.active{color:#e84393}
        .header-right{display:flex;align-items:center;gap:25px}
        .icon-btn{position:relative;font-size:22px;color:#1a1a2e;text-decoration:none;transition:0.3s}
        .icon-btn:hover{color:#e84393}
        .badge{position:absolute;top:-8px;right:-12px;background:#e84393;color:white;font-size:10px;font-weight:600;padding:2px 6px;border-radius:50%;min-width:18px;text-align:center}
        .user-menu{position:relative;cursor:pointer}
        .user-avatar{display:flex;align-items:center;gap:10px;padding:8px 15px;background:#f0f0f5;border-radius:40px;transition:0.3s}
        .user-avatar:hover{background:#e8e8f0}
        .dropdown-menu{position:absolute;top:55px;right:0;background:white;border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,0.1);min-width:220px;padding:10px 0;opacity:0;visibility:hidden;transition:0.3s;z-index:1001}
        .dropdown-menu.show{opacity:1;visibility:visible}
        .dropdown-menu a{display:flex;align-items:center;gap:12px;padding:12px 20px;text-decoration:none;color:#4a4a6a;font-size:14px;transition:0.3s}
        .dropdown-menu a:hover{background:#f8f9fa;color:#e84393}
        .login-btn-header{background:#e84393;color:white;padding:10px 25px;border-radius:40px;text-decoration:none;font-weight:600;font-size:14px;transition:0.3s;display:inline-flex;align-items:center;gap:8px}
        .menu-icon{display:none;cursor:pointer;font-size:24px;color:#1a1a2e}
        .avatar-img{width:35px;height:35px;border-radius:50%;object-fit:cover;border:2px solid #e84393}
        .avatar-placeholder{width:35px;height:35px;border-radius:50%;background:#e84393;display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:14px}
        .welcome-banner{background:linear-gradient(135deg,#e84393,#c2185b);color:white;padding:12px 5%;text-align:center;position:relative;z-index:999}
        .welcome-banner i{margin-right:10px}
        .orders-hero{background:linear-gradient(135deg,#1a1a2e,#2d1b36);padding:60px 5%;text-align:center}
        .orders-hero h1{font-size:48px;font-weight:800;color:white;margin-bottom:15px}
        .orders-hero p{font-size:18px;color:rgba(255,255,255,0.8)}
        .orders-container{max-width:1000px;margin:40px auto;padding:20px;min-height:400px}
        .success-message{background:#e0ffe0;color:#2ed573;padding:15px;border-radius:15px;margin-bottom:20px;text-align:center}
        .order-card{background:white;border-radius:20px;box-shadow:0 5px 20px rgba(0,0,0,0.05);margin-bottom:25px;overflow:hidden;transition:0.3s}
        .order-card:hover{transform:translateY(-3px)}
        .order-header{background:#f8f9fa;padding:18px 25px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;border-bottom:1px solid #e8e8f0}
        .order-num{font-weight:700;color:#1a1a2e;font-size:16px}
        .order-date{color:#6c757d;font-size:14px}
        .order-status{font-weight:600;font-size:13px;padding:5px 12px;border-radius:30px;background:#e6f9ed;color:#2ed573;display:inline-flex;align-items:center;gap:6px}
        .order-status.pending{background:#fff3cd;color:#ffc107}
        .order-body{padding:20px 25px}
        .order-items{border-bottom:1px solid #e8e8f0;margin-bottom:15px;padding-bottom:15px}
        .order-item{display:flex;justify-content:space-between;padding:8px 0;color:#4a4a6a;font-size:14px}
        .order-total{text-align:right;font-size:18px;font-weight:800;color:#e84393}
        .empty-orders{text-align:center;padding:80px;background:white;border-radius:30px}
        .empty-orders i{font-size:80px;color:#e8e8f0;margin-bottom:20px}
        .btn-shop{display:inline-block;background:#e84393;color:white;padding:12px 35px;border-radius:50px;text-decoration:none;font-weight:600}
        .global-footer{background:#1a1a2e;color:#fff;padding:60px 20px 20px;margin-top:60px;border-top:3px solid #e84393}
        .footer-container{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:40px}
        .footer-col h3{font-size:26px;font-weight:700;margin-bottom:20px;color:#fff}
        .footer-col h3 span{color:#e84393}
        .footer-col h4{font-size:18px;font-weight:600;margin-bottom:20px;position:relative;padding-bottom:8px}
        .footer-col h4::after{content:'';position:absolute;left:0;bottom:0;width:35px;height:2px;background:#e84393}
        .footer-col p{color:#bdc3c7;font-size:14px;line-height:1.6}
        .footer-col ul{list-style:none}
        .footer-col ul li{margin-bottom:12px}
        .footer-col ul li a{color:#bdc3c7;text-decoration:none;font-size:14px;display:inline-flex;align-items:center;gap:8px;transition:0.2s}
        .footer-col ul li a:hover{color:#e84393}
        .social-icons{display:flex;gap:15px;margin-top:20px}
        .social-icons a{width:38px;height:38px;background:#333539;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:white;font-size:18px;transition:0.3s;text-decoration:none}
        .social-icons a:hover{background:#e84393;transform:translateY(-4px)}
        .footer-bottom{text-align:center;margin-top:50px;padding-top:20px;border-top:1px solid #333539}
        .toast-notification{position:fixed;bottom:30px;right:30px;background:#1a1a2e;color:white;padding:15px 25px;border-radius:50px;display:flex;align-items:center;gap:12px;transform:translateX(450px);transition:0.4s;z-index:9999}
        .toast-notification.show{transform:translateX(0)}
        @media(max-width:992px){
            body{padding-top:70px}
            header{padding:15px 20px}
            .navbar{position:fixed;top:70px;left:-280px;width:280px;height:calc(100vh-70px);background:white;flex-direction:column;padding:30px;transition:0.3s;z-index:999;gap:25px}
            .navbar.show{left:0}
            .menu-icon{display:block!important}
            .footer-container{grid-template-columns:repeat(2,1fr)}
        }
        @media(max-width:768px){
            .order-header{flex-direction:column;align-items:flex-start}
            .empty-orders{padding:50px 20px}
            .footer-container{grid-template-columns:1fr}
            .toast-notification{left:20px;right:20px;bottom:20px;transform:translateY(150px)}
            .toast-notification.show{transform:translateY(0)}
        }
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <div class="header-left"><i class="fas fa-bars menu-icon" onclick="toggleMobileMenu(event)"></i><a href="home.php" class="logo">Tajmil<span>.</span></a></div>
        <nav class="navbar" id="mainNavbar">
            <a href="home.php">Accueil</a>
            <a href="products.php">Produits</a>
            <a href="about.php">À propos</a>
            <a href="review.php">Avis</a>
            <a href="contact.php">Contact</a>
        </nav>
        <div class="header-right">
            <a href="wishlist.php" class="icon-btn"><i class="far fa-heart"></i><span class="badge wishlist-badge"><?= $wishlist_count ?></span></a>
            <a href="cart.php" class="icon-btn"><i class="fas fa-shopping-cart"></i><span class="badge cart-badge"><?= $cart_count ?></span></a>
            <div class="user-menu">
                <?php if(isset($_SESSION['user_id']) && $user_info): 
                    $photo = $_SESSION['user_photo'] ?? 'default.png';
                    $has_photo = ($photo != 'default.png' && file_exists(__DIR__ . '/../assets/uploads/' . $photo));
                ?>
                    <div class="user-avatar" onclick="toggleUserMenu(event)">
                        <?php if($has_photo): ?>
                            <img src="../assets/uploads/<?= htmlspecialchars($photo) ?>" class="avatar-img">
                        <?php else: ?>
                            <div class="avatar-placeholder"><?= strtoupper(substr($user_info['nom'], 0, 2)) ?></div>
                        <?php endif; ?>
                        <span><?= htmlspecialchars($user_info['nom']) ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="dropdown-menu" id="userDropdown">
                        <a href="profile.php"><i class="fas fa-user"></i> Mon compte</a>
                        <a href="wishlist.php"><i class="fas fa-heart"></i> Mes favoris</a>
                        <a href="orders.php" class="active"><i class="fas fa-box"></i> Mes commandes</a>
                        <a href="../php_files/logout.php" style="color:#e84393;"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="login-btn-header"><i class="fas fa-sign-in-alt"></i> Se connecter</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<?php if(isset($_SESSION['user_id']) && $user_info): ?>
<div class="welcome-banner">
    <i class="fas fa-smile-wink"></i>
    <strong>Bonjour, <?= htmlspecialchars($user_info['nom']) ?> !</strong> 
    Suivez vos commandes ici. 📦
</div>
<?php endif; ?>

<section class="orders-hero"><h1>📦 Mes commandes</h1><p>Retrouvez l'historique de toutes vos commandes beauté</p></section>

<div class="orders-container">
    <?php if(isset($_GET['success'])): ?><div class="success-message"><i class="fas fa-check-circle"></i> Votre commande a été passée avec succès !</div><?php endif; ?>
    <?php if (!$table_exists): ?>
        <div class="empty-orders"><i class="fas fa-database"></i><h2>Table non trouvée</h2><a href="install.php" class="btn-shop">Créer les tables</a></div>
    <?php elseif (empty($commandes)): ?>
        <div class="empty-orders"><i class="fas fa-shopping-bag"></i><h2>Aucune commande pour le moment</h2><p>Votre historique de commandes est vide.</p><a href="products.php" class="btn-shop">Découvrir nos produits</a></div>
    <?php else: ?>
        <?php foreach ($commandes as $commande): $statut = $commande['statut'] ?? 'En traitement'; $is_pending = (strpos(strtolower($statut),'traitement')!==false||strpos(strtolower($statut),'cours')!==false||strpos(strtolower($statut),'attente')!==false); $items = json_decode($commande['produits'], true); ?>
            <div class="order-card"><div class="order-header"><div class="order-num">Commande #<?= htmlspecialchars($commande['numero']) ?></div><div class="order-date"><?= date('d/m/Y H:i',strtotime($commande['date_commande'])) ?></div><div class="order-status <?= $is_pending?'pending':'' ?>"><i class="fas <?= $is_pending?'fa-clock':'fa-check-circle' ?>"></i> <?= htmlspecialchars($statut) ?></div></div><div class="order-body"><div class="order-items"><?php if(is_array($items)): foreach($items as $item): $nom_produit = $item['nom']??($item['name']??'Produit'); $quantite = $item['qty']??($item['quantity']??1); $sous_total = $item['subtotal']??(($item['price']??0)*$quantite); ?><div class="order-item"><span><?= htmlspecialchars($nom_produit) ?> x <?= (int)$quantite ?></span><span><?= number_format((float)$sous_total,2) ?> DH</span></div><?php endforeach; else: ?><div class="order-item"><span>Détails indisponibles</span><span>-</span></div><?php endif; ?></div><div class="order-total">Total : <?= number_format((float)$commande['total'],2) ?> DH</div></div></div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<footer class="global-footer">
    <div class="footer-container">
        <div class="footer-col about-col"><h3>Tajmil<span>.</span></h3><p>Des produits et services de beauté premium pour sublimer votre visage.</p><div class="social-icons"><a href="#"><i class="fab fa-facebook"></i></a><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fab fa-tiktok"></i></a><a href="#"><i class="fab fa-whatsapp"></i></a></div></div>
        <div class="footer-col"><h4>Navigation</h4><ul><li><a href="home.php">Accueil</a></li><li><a href="products.php">Produits</a></li><li><a href="about.php">À propos</a></li><li><a href="review.php">Avis</a></li><li><a href="contact.php">Contact</a></li></ul></div>
        <div class="footer-col"><h4>Support</h4><ul><li><a href="#">FAQ</a></li><li><a href="#">Livraison</a></li><li><a href="#">Retours</a></li><li><a href="#">CGV</a></li></ul></div>
        <div class="footer-col contact-col"><h4>Contact</h4><p><i class="fas fa-map-marker-alt"></i> Casablanca, Maroc</p><p><i class="fas fa-phone-alt"></i> +212 723-699031</p><p><i class="fas fa-envelope"></i> contact@tajmil.ma</p></div>
    </div>
    <div class="footer-bottom"><p>&copy; 2026 Tajmil Lwajh — Tous droits réservés.</p></div>
</footer>

<div id="toastNotification" class="toast-notification"></div>

<script>
function toggleMobileMenu(e){if(e)e.stopPropagation();document.getElementById('mainNavbar').classList.toggle('show');}
function toggleUserMenu(e){e.stopPropagation();let d=document.getElementById('userDropdown');if(d)d.classList.toggle('show');}
document.addEventListener('click',function(e){let d=document.getElementById('userDropdown');let n=document.getElementById('mainNavbar');if(d&&!d.contains(e.target))d.classList.remove('show');if(n&&!n.contains(e.target))n.classList.remove('show');});
function updateCartBadge(){let cart=<?= isset($_SESSION['cart'])?json_encode(array_values($_SESSION['cart'])):'[]' ?>;let c=0;for(let i=0;i<cart.length;i++)c+=cart[i].qty;let b=document.querySelector('.cart-badge');if(b)b.textContent=c;}
function updateWishlistBadge(){let wishlist=<?= isset($_SESSION['wishlist'])?json_encode(array_keys($_SESSION['wishlist'])):'[]' ?>;let b=document.querySelector('.wishlist-badge');if(b)b.textContent=wishlist.length;}
document.addEventListener('DOMContentLoaded',function(){updateCartBadge();updateWishlistBadge();});
</script>

<?php require_once '../php_files/chatbot_widget.php'; ?>
</body>
</html>
<?php
session_start();
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

$stmt = $pdo->prepare("SELECT * FROM produits ORDER BY id DESC LIMIT 8");
$stmt->execute();
$recent_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];
$cart_count = getCartCount();
$wishlist_count = getWishlistCount();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tajmil Lwajh - Beauté & Soins</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#fff;padding-top:80px;overflow-x:hidden}
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
        .hero{min-height:85vh;background:linear-gradient(135deg,#1a1a2e,#2d1b36,#1a1a2e);display:flex;align-items:center;justify-content:space-between;padding:60px 8%;position:relative;overflow:hidden}
        .hero-content{max-width:600px;z-index:2}
        .hero-content .tag-badge{display:inline-block;background:rgba(232,67,147,0.2);backdrop-filter:blur(10px);color:#e84393;padding:8px 22px;border-radius:50px;font-size:13px;font-weight:700;margin-bottom:25px;border:1px solid rgba(232,67,147,0.3)}
        .hero-content h1{font-size:56px;font-weight:800;color:white;line-height:1.15;margin-bottom:22px}
        .hero-content h1 span{color:#e84393}
        .hero-content p{font-size:18px;color:rgba(255,255,255,0.7);margin-bottom:35px}
        .hero-buttons{display:flex;gap:18px;flex-wrap:wrap}
        .btn-primary{background:linear-gradient(135deg,#e84393,#c2185b);color:white;padding:15px 35px;border-radius:50px;text-decoration:none;font-weight:700;transition:0.3s;display:inline-flex;align-items:center;gap:10px;box-shadow:0 5px 20px rgba(232,67,147,0.4)}
        .btn-primary:hover{transform:translateY(-3px)}
        .btn-secondary{background:transparent;border:2px solid rgba(255,255,255,0.3);color:white;padding:14px 35px;border-radius:50px;text-decoration:none;font-weight:700;transition:0.3s}
        .btn-secondary:hover{border-color:#e84393;color:#e84393}
        .hero-image img{max-width:450px;width:100%;animation:float 5s ease-in-out infinite;border-radius:50%}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-20px)}}
        .features{padding:100px 8%;background:linear-gradient(180deg,#fff,#faf5f8);text-align:center}
        .section-title{font-size:42px;font-weight:800;color:#1a1a2e;margin-bottom:15px;position:relative;display:inline-block}
        .section-title::after{content:'';position:absolute;bottom:-10px;left:50%;transform:translateX(-50%);width:80px;height:3px;background:linear-gradient(90deg,#e84393,#ff6b9d);border-radius:3px}
        .section-subtitle{font-size:17px;color:#6c757d;margin-bottom:60px}
        .features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:35px;max-width:1300px;margin:0 auto}
        .feature-card{background:white;padding:45px 30px;border-radius:30px;transition:0.4s;text-align:center;border:1px solid #f0e6ea}
        .feature-card:hover{transform:translateY(-10px);border-color:#e84393}
        .feature-card i{font-size:50px;background:linear-gradient(135deg,#e84393,#ff6b9d);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:25px}
        .feature-card h3{font-size:22px;font-weight:700;margin-bottom:12px;color:#1a1a2e}
        .featured-products{padding:80px 8%;background:white}
        .products-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:35px;max-width:1400px;margin:0 auto}
        .product-card{background:white;border-radius:25px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.05);transition:0.4s;border:1px solid #f0e6ea;position:relative}
        .product-card:hover{transform:translateY(-12px);border-color:#e84393}
        .product-image{height:300px;overflow:hidden;background:linear-gradient(135deg,#f8f9fa,#fff)}
        .product-image img{width:100%;height:100%;object-fit:cover;transition:transform 0.6s}
        .product-card:hover .product-image img{transform:scale(1.08)}
        .wishlist-icon{position:absolute;top:15px;right:15px;background:rgba(255,255,255,0.95);width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:0.3s;z-index:3}
        .wishlist-icon:hover{background:#e84393;transform:scale(1.1)}
        .wishlist-icon:hover i{color:white}
        .product-info{padding:22px;text-align:center}
        .product-brand{font-size:11px;color:#e84393;text-transform:uppercase;font-weight:800;letter-spacing:1.5px;margin-bottom:8px}
        .product-title{font-size:18px;font-weight:700;color:#1a1a2e;margin-bottom:12px}
        .product-price{font-size:22px;font-weight:800;color:#e84393}
        .global-footer{background:#1a1a2e;color:#fff;padding:60px 20px 20px;margin-top:60px;border-top:3px solid #e84393}
        .footer-container{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:40px}
        .footer-col h3{font-size:26px;font-weight:700;margin-bottom:20px;color:#fff}
        .footer-col h3 span{color:#e84393}
        .footer-col h4{font-size:18px;font-weight:600;margin-bottom:20px;position:relative;padding-bottom:8px}
        .footer-col h4::after{content:'';position:absolute;left:0;bottom:0;width:35px;height:2px;background:#e84393}
        .footer-col p{color:#bdc3c7;font-size:14px;line-height:1.6;margin-bottom:15px}
        .footer-col ul{list-style:none}
        .footer-col ul li{margin-bottom:12px}
        .footer-col ul li a{color:#bdc3c7;text-decoration:none;font-size:14px;display:inline-flex;align-items:center;gap:8px;transition:0.2s}
        .footer-col ul li a:hover{color:#e84393;transform:translateX(5px)}
        .social-icons{display:flex;gap:15px;margin-top:20px}
        .social-icons a{width:38px;height:38px;background:#333539;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:white;font-size:18px;transition:0.3s;text-decoration:none}
        .social-icons a:hover{background:#e84393;transform:translateY(-4px)}
        .footer-bottom{text-align:center;margin-top:50px;padding-top:20px;border-top:1px solid #333539}
        .toast-notification{position:fixed;bottom:30px;right:30px;background:#1a1a2e;color:white;padding:15px 25px;border-radius:50px;display:flex;align-items:center;gap:12px;transform:translateX(450px);transition:0.4s;z-index:9999}
        .toast-notification.show{transform:translateX(0)}
        @media(max-width:992px){
            body{padding-top:70px}
            header{padding:15px 20px}
            .navbar{position:fixed;top:70px;left:-280px;width:280px;height:calc(100vh - 70px);background:white;flex-direction:column;padding:30px;transition:0.3s;z-index:999;gap:25px}
            .navbar.show{left:0}
            .menu-icon{display:block!important}
            .hero{flex-direction:column;text-align:center;padding:60px 5% 80px;gap:50px}
            .hero-content h1{font-size:40px}
            .hero-image img{max-width:320px}
            .section-title{font-size:32px}
            .footer-container{grid-template-columns:repeat(2,1fr)}
        }
        @media(max-width:768px){
            .footer-container{grid-template-columns:1fr}
            .toast-notification{left:20px;right:20px;bottom:20px;transform:translateY(150px)}
            .toast-notification.show{transform:translateY(0)}
        }
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <div class="header-left">
            <i class="fas fa-bars menu-icon" onclick="toggleMobileMenu(event)"></i>
            <a href="home.php" class="logo">Tajmil<span>.</span></a>
        </div>
        <nav class="navbar" id="mainNavbar">
            <a href="home.php" class="active">Accueil</a>
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
                        <a href="orders.php"><i class="fas fa-box"></i> Mes commandes</a>
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
    Bienvenue sur Tajmil Lwajh. 🌸 Prenez soin de vous !
</div>
<?php endif; ?>

<section class="hero">
    <div class="hero-content">
        <div class="tag-badge">✨ Nouvelle Collection Beauté 2026</div>
        <h1>Révélez votre <span>beauté</span><br>au naturel</h1>
        <p>Découvrez nos produits et services de beauté premium, conçus pour sublimer votre visage.</p>
        <div class="hero-buttons">
            <a href="products.php" class="btn-primary"><i class="fas fa-spa"></i> Découvrir nos soins</a>
            <a href="about.php" class="btn-secondary">En savoir plus</a>
        </div>
    </div>
    <!-- ✅ NOUVELLE IMAGE (Beauté & Soins) -->
    <div class="hero-image"><img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=600" alt="Soin du visage beauté"></div>
</section>

<section class="features">
    <h2 class="section-title">Pourquoi nous choisir ?</h2>
    <p class="section-subtitle">Une expérience beauté unique</p>
    <div class="features-grid">
        <div class="feature-card"><i class="fas fa-brush"></i><h3>Maquillage pro</h3><p>Pour toutes vos occasions spéciales</p></div>
        <div class="feature-card"><i class="fas fa-leaf"></i><h3>Soins naturels</h3><p>Des ingrédients sains pour votre peau</p></div>
        <div class="feature-card"><i class="fas fa-truck-fast"></i><h3>Livraison express</h3><p>Gratuite à partir de 500 DH</p></div>
        <div class="feature-card"><i class="fas fa-gift"></i><h3>Coffret cadeau</h3><p>Emballage luxueux offert</p></div>
    </div>
</section>

<section class="featured-products">
    <h2 class="section-title">Nos meilleures ventes</h2>
    <p class="section-subtitle">Les produits préférés de nos clientes</p>
    <div class="products-grid">
        <?php foreach($recent_products as $product): $in_wishlist = isset($_SESSION['wishlist'][$product['id']]); ?>
        <div class="product-card" data-id="<?= $product['id'] ?>">
            <div class="wishlist-icon wishlist-btn" data-id="<?= $product['id'] ?>"><i class="<?= $in_wishlist ? 'fas' : 'far' ?> fa-heart"></i></div>
            <a href="product-details.php?id=<?= $product['id'] ?>">
                <div class="product-image"><img src="../assets/images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['nom']) ?>"></div>
            </a>
            <div class="product-info">
                <div class="product-brand"><?= htmlspecialchars($product['marque']) ?></div>
                <div class="product-title"><?= htmlspecialchars($product['nom']) ?></div>
                <div class="product-price"><?= number_format($product['prix'], 2) ?> DH</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:50px">
        <a href="products.php" class="btn-primary" style="background:linear-gradient(135deg,#1a1a2e,#2d1b36)">Voir tous nos produits <i class="fas fa-arrow-right"></i></a>
    </div>
</section>

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
function showToast(msg){let t=document.getElementById('toastNotification');t.innerHTML='<i class="fas fa-check-circle"></i> '+msg;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),3000);}
function updateCartBadge(){fetch('../php_files/get_cart.php').then(r=>r.json()).then(data=>{let c=data.items?data.items.reduce((s,i)=>s+i.qty,0):0;let b=document.querySelector('.cart-badge');if(b)b.textContent=c;}).catch(e=>console.error(e));}
function updateWishlistBadge(){let wishlist=<?= json_encode(array_keys($_SESSION['wishlist'])) ?>;let b=document.querySelector('.wishlist-badge');if(b)b.textContent=wishlist.length;}
document.querySelectorAll('.wishlist-btn').forEach(btn=>{btn.addEventListener('click',function(e){e.preventDefault();e.stopPropagation();let id=this.dataset.id;let icon=this.querySelector('i');fetch('product-details.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'wishlist_toggle=1&product_id='+id}).then(r=>r.json()).then(d=>{if(d.success){if(d.in_wishlist){icon.className='fas fa-heart';showToast('❤️ Ajouté aux favoris');}else{icon.className='far fa-heart';showToast('💔 Retiré des favoris');}updateWishlistBadge();}}).catch(e=>console.error(e));});});
document.addEventListener('DOMContentLoaded',function(){updateCartBadge();updateWishlistBadge();});
</script>

<?php require_once '../php_files/chatbot_widget.php'; ?>
</body>
</html>
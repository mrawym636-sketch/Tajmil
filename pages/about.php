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

$cart_count = getCartCount();
$wishlist_count = getWishlistCount();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - Tajmil Lwajh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#fff;padding-top:80px}
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
        .hero-about{background:linear-gradient(135deg,#1a1a2e,#2d1b36);padding:60px 20px;text-align:center;color:white}
        .hero-about h1{font-size:44px;font-weight:800;margin-bottom:15px}
        .about-content{padding:80px 8%;display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;max-width:1400px;margin:0 auto}
        .about-text h2{font-size:36px;font-weight:700;color:#1a1a2e;margin-bottom:20px}
        .about-text h2 span{color:#e84393}
        .about-text p{font-size:16px;color:#4a4a6a;line-height:1.8;margin-bottom:20px}
        .about-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:30px;margin-top:40px}
        .stat{text-align:center;background:#f8f9fa;padding:20px;border-radius:15px}
        .stat .number{font-size:32px;font-weight:800;color:#e84393}
        .about-image img{width:100%;border-radius:30px}
        .values{background:#f8f9fa;padding:80px 8%;text-align:center}
        .section-title{font-size:36px;font-weight:700;color:#1a1a2e;margin-bottom:15px}
        .values-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:40px;max-width:1200px;margin:0 auto}
        .value-card{background:white;padding:40px 30px;border-radius:20px;transition:0.3s;text-align:center}
        .value-card:hover{transform:translateY(-10px);border-color:#e84393;box-shadow:0 10px 30px rgba(232,67,147,0.1)}
        .value-card i{font-size:46px;color:#e84393;margin-bottom:20px}
        .value-card h3{font-size:22px;font-weight:700;margin-bottom:15px}
        .team{padding:80px 8%;text-align:center}
        .team-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:40px;max-width:1100px;margin:0 auto}
        .team-member{background:white;padding:30px 20px;border-radius:24px;transition:0.3s}
        .team-member:hover{transform:translateY(-5px)}
        .team-member img{width:130px;height:130px;border-radius:50%;object-fit:cover;margin-bottom:20px;border:3px solid #e84393}
        .team-member h4{font-size:19px;font-weight:700;margin-bottom:6px}
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
            .about-content{grid-template-columns:1fr;text-align:center}
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
        <div class="header-left"><i class="fas fa-bars menu-icon" onclick="toggleMobileMenu(event)"></i><a href="home.php" class="logo">Tajmil<span>.</span></a></div>
        <nav class="navbar" id="mainNavbar">
            <a href="home.php">Accueil</a>
            <a href="products.php">Produits</a>
            <a href="about.php" class="active">À propos</a>
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

<!-- ✅ BANDEAU "BONJOUR + NOM" AJOUTÉ CI-DESSOUS -->
<?php if(isset($_SESSION['user_id']) && $user_info): ?>
<div class="welcome-banner">
    <i class="fas fa-smile-wink"></i>
    <strong>✨ Bonjour <?= htmlspecialchars($user_info['nom']) ?> !</strong> ✨
    <span style="margin-left: 8px; font-size: 14px;">Découvrez notre univers beauté et nos soins naturels.</span>
</div>
<?php else: ?>
<div class="welcome-banner" style="background: linear-gradient(135deg, #1a1a2e, #2d1b36);">
    <i class="fas fa-spa"></i>
    <strong>Bienvenue chez Tajmil Lwajh !</strong> ✨
    <span style="margin-left: 8px; font-size: 14px;">Connectez-vous pour profiter de nos offres exclusives.</span>
</div>
<?php endif; ?>

<section class="hero-about"><h1>✨ Notre histoire</h1><p>Depuis 2024, nous sublimons les visages et révélons la beauté naturelle</p></section>

<section class="about-content">
    <div class="about-text">
        <h2>Une passion <span>pour la beauté</span> depuis 2024</h2>
        <p>Tajmil Lwajh est née d'une passion pour la beauté et le bien-être. Nous proposons des produits cosmétiques de qualité et des soins professionnels pour révéler votre éclat naturel.</p>
        <p>Chaque produit est sélectionné avec soin, alliant des ingrédients naturels et des formules innovantes.</p>
        <div class="about-stats">
            <div class="stat"><div class="number">1000+</div><div class="label">Clientes satisfaites</div></div>
            <div class="stat"><div class="number">50+</div><div class="label">Produits cosmétiques</div></div>
            <div class="stat"><div class="number">15+</div><div class="label">Services proposés</div></div>
        </div>
    </div>
    <div class="about-image"><img src="https://images.unsplash.com/photo-1527799820374-dcf8d9d4a388?w=600" alt="Notre institut de beauté"></div>
</section>

<section class="values">
    <h2 class="section-title">Nos valeurs</h2>
    <div class="values-grid">
        <div class="value-card"><i class="fas fa-leaf"></i><h3>Naturel</h3><p>Des ingrédients naturels pour des soins sains</p></div>
        <div class="value-card"><i class="fas fa-heart"></i><h3>Bien-être</h3><p>Prendre soin de soi pour rayonner</p></div>
        <div class="value-card"><i class="fas fa-globe"></i><h3>Éthique</h3><p>Produits non testés sur les animaux</p></div>
    </div>
</section>

<section class="team">
    <h2 class="section-title">Notre équipe</h2>
    <div class="team-grid">
        <div class="team-member"><img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Fondateur"><h4>Mohamad Amraoui</h4><p>Fondateur & Expert beauté</p></div>
        <div class="team-member"><img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Directrice"><h4>Sarah Benali</h4><p>Directrice Artistique</p></div>
        <div class="team-member"><img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Expert"><h4>Karim El Fassi</h4><p>Expert en cosmétique</p></div>
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
function updateCartBadge(){fetch('../php_files/get_cart.php').then(r=>r.json()).then(data=>{let c=data.items?data.items.reduce((s,i)=>s+i.qty,0):0;let b=document.querySelector('.cart-badge');if(b)b.textContent=c;}).catch(e=>console.error(e));}
function updateWishlistBadge(){let wishlist=<?= json_encode(array_keys($_SESSION['wishlist'] ?? [])) ?>;let b=document.querySelector('.wishlist-badge');if(b)b.textContent=wishlist.length;}
document.addEventListener('DOMContentLoaded',function(){updateCartBadge();updateWishlistBadge();});
</script>

<?php require_once '../php_files/chatbot_widget.php'; ?>
</body>
</html>
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

if (!isset($_SESSION['wishlist'])) { $_SESSION['wishlist'] = []; }

$cart_count = getCartCount();
$wishlist_count = getWishlistCount();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes favoris - Tajmil Lwajh</title>
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
        .wishlist-hero{background:linear-gradient(135deg,#1a1a2e,#2d1b36);padding:60px 5%;text-align:center}
        .wishlist-hero h1{font-size:48px;font-weight:800;color:white;margin-bottom:15px}
        .wishlist-hero p{font-size:18px;color:rgba(255,255,255,0.8)}
        .wishlist-container{max-width:1200px;margin:40px auto;padding:0 20px;min-height:400px}
        .wishlist-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:30px}
        .wishlist-item{background:white;border-radius:20px;padding:25px;text-align:center;position:relative;transition:0.3s;border:1px solid #e8e8f0}
        .wishlist-item:hover{transform:translateY(-5px);border-color:#e84393}
        .wishlist-item img{width:100%;height:240px;object-fit:cover;border-radius:15px;margin-bottom:10px}
        .wishlist-item h3{margin:15px 0 8px;font-size:18px;font-weight:700;color:#1a1a2e}
        .wishlist-item .price{color:#e84393;font-size:22px;font-weight:800;margin-bottom:20px}
        .wishlist-actions{display:flex;gap:10px}
        .btn-remove{position:absolute;top:15px;right:15px;background:rgba(255,255,255,0.9);border:none;width:36px;height:36px;border-radius:50%;cursor:pointer;color:#dc3545;transition:0.3s}
        .btn-remove:hover{background:#dc3545;color:white}
        .btn-view{flex:1;background:#1a1a2e;color:white;padding:12px;border-radius:40px;text-decoration:none;font-weight:700;font-size:13px}
        .btn-view:hover{background:#e84393}
        .btn-cart-wishlist{width:44px;height:44px;background:white;border:2px solid #e8e8f0;border-radius:50%;cursor:pointer}
        .btn-cart-wishlist:hover{border-color:#e84393;color:#e84393}
        .empty-wishlist{text-align:center;padding:80px 40px;background:white;border-radius:24px;border:1px solid #e8e8f0}
        .empty-wishlist i{font-size:70px;color:#e8e8f0;margin-bottom:25px}
        .empty-wishlist h2{font-size:24px;font-weight:800;margin-bottom:12px}
        .empty-wishlist .btn-shop{background:#e84393;color:white;padding:14px 35px;border-radius:50px;text-decoration:none;display:inline-block;margin-top:20px}
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
            .wishlist-hero h1{font-size:28px}
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
            <a href="wishlist.php" class="icon-btn active"><i class="far fa-heart"></i><span class="badge wishlist-badge"><?= $wishlist_count ?></span></a>
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
    Vos produits préférés sont ici. ❤️
</div>
<?php endif; ?>

<section class="wishlist-hero"><h1>❤️ Mes favoris</h1><p>Retrouvez ici tous vos produits préférés</p></section>

<div class="wishlist-container">
    <?php if(empty($_SESSION['wishlist'])): ?>
        <div class="empty-wishlist"><i class="far fa-heart"></i><h2>Votre liste de favoris est vide</h2><p>Ajoutez des produits à vos favoris en cliquant sur le cœur ❤️</p><a href="products.php" class="btn-shop">Découvrir nos produits</a></div>
    <?php else: ?>
        <div class="wishlist-grid" id="wishlistGrid">
            <?php foreach($_SESSION['wishlist'] as $item): ?>
                <div class="wishlist-item" data-id="<?= $item['id'] ?>">
                    <button class="btn-remove" onclick="removeFromWishlist(<?= $item['id'] ?>)" title="Retirer"><i class="fas fa-times"></i></button>
                    <img src="../assets/images/<?= htmlspecialchars($item['img']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                    <div class="price"><?= number_format($item['price'], 2) ?> DH</div>
                    <div class="wishlist-actions">
                        <a href="product-details.php?id=<?= $item['id'] ?>" class="btn-view"><i class="fas fa-eye"></i> Détails</a>
                        <button class="btn-cart-wishlist" onclick="addToCart(<?= $item['id'] ?>)" title="Ajouter au panier"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
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

function showToast(msg,iconClass='fas fa-check-circle'){let t=document.getElementById('toastNotification');t.innerHTML=`<i class="${iconClass}"></i> <span>${escapeHtml(msg)}</span>`;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),3500);}
function escapeHtml(str){return str.replace(/[&<>]/g,function(m){if(m==='&')return '&amp;';if(m==='<')return '&lt;';if(m==='>')return '&gt;';return m;});}
function updateCartBadge(){let cart=<?= isset($_SESSION['cart'])?json_encode(array_values($_SESSION['cart'])):'[]' ?>;let c=0;for(let i=0;i<cart.length;i++)c+=cart[i].qty;let b=document.querySelector('.cart-badge');if(b)b.textContent=c;}
function updateWishlistBadge(){let wishlist=<?= json_encode(array_keys($_SESSION['wishlist'])) ?>;let b=document.querySelector('.wishlist-badge');if(b)b.textContent=wishlist.length;}

function addToCart(id){fetch(window.location.href,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'add_to_cart=1&product_id='+id}).then(r=>r.json()).then(d=>{if(d.success){showToast('Produit ajouté au panier !');updateCartBadge();}else{showToast('Erreur',true);}}).catch(()=>showToast('Erreur réseau',true));}

function removeFromWishlist(id){fetch(window.location.href,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'remove_wishlist=1&product_id='+id}).then(r=>r.json()).then(d=>{if(d.success){showToast('Produit retiré des favoris');let card=document.querySelector('.wishlist-item[data-id="'+id+'"]');if(card){card.style.opacity='0';setTimeout(()=>{card.remove();updateWishlistBadge();if(document.querySelectorAll('.wishlist-item').length===0){location.reload();}},300);}}else{showToast('Erreur',true);}}).catch(()=>showToast('Erreur réseau',true));}

document.addEventListener('DOMContentLoaded',function(){updateCartBadge();updateWishlistBadge();});
</script>

<?php require_once '../php_files/chatbot_widget.php'; ?>
</body>
</html>
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

$stmt = $pdo->query("SELECT * FROM produits ORDER BY id DESC");
$all_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$brand_filter = isset($_GET['brand']) ? trim($_GET['brand']) : '';

$products = $all_products;
if (!empty($search_term)) {
    $filtered = [];
    foreach ($all_products as $p) {
        if (stripos($p['nom'], $search_term) !== false || stripos($p['marque'], $search_term) !== false) {
            $filtered[] = $p;
        }
    }
    $products = $filtered;
}
if (!empty($brand_filter)) {
    $filtered = [];
    foreach ($products as $p) {
        if (stripos($p['marque'], $brand_filter) !== false) {
            $filtered[] = $p;
        }
    }
    $products = $filtered;
}

$brands = array_unique(array_column($all_products, 'marque'));
sort($brands);

$cart_count = getCartCount();
$wishlist_count = getWishlistCount();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos produits - Tajmil Lwajh</title>
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
        .welcome-banner{background:linear-gradient(135deg,#e84393,#c2185b);color:white;padding:12px 5%;text-align:center}
        .welcome-banner i{margin-right:10px}
        
        .hero-products{background:linear-gradient(135deg,#1a1a2e,#2d1b36);padding:60px 5%;text-align:center}
        .hero-products h1{font-size:48px;font-weight:800;color:white;margin-bottom:15px}
        .hero-products p{font-size:18px;color:rgba(255,255,255,0.8)}
        
        .filters-section{max-width:1400px;margin:40px auto;padding:0 20px}
        .search-wrapper{display:flex;gap:15px;flex-wrap:wrap;justify-content:center;align-items:center}
        .search-box{flex:1;max-width:500px;position:relative}
        .search-box input{width:100%;padding:16px 50px 16px 25px;border:2px solid #e8e8f0;border-radius:50px;font-size:16px;outline:none;background:white}
        .search-box input:focus{border-color:#e84393}
        .search-box button{position:absolute;right:8px;top:50%;transform:translateY(-50%);background:#e84393;border:none;color:white;width:45px;height:45px;border-radius:50%;cursor:pointer}
        .brand-filter select{padding:16px 25px;border:2px solid #e8e8f0;border-radius:50px;font-size:16px;background:white;cursor:pointer;min-width:200px}
        .reset-btn{background:#f0f0f5;border:none;padding:0 25px;border-radius:50px;font-size:14px;font-weight:500;color:#4a4a6a;cursor:pointer;display:flex;align-items:center;gap:8px;text-decoration:none;height:56px}
        
        .products-section{max-width:1400px;margin:0 auto;padding:0 20px 80px}
        .products-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:35px}
        .product-card{background:white;border-radius:25px;overflow:hidden;box-shadow:0 5px 20px rgba(0,0,0,0.05);transition:0.4s;position:relative}
        .product-card:hover{transform:translateY(-12px)}
        .product-badge{position:absolute;top:15px;left:15px;background:#e84393;color:white;padding:6px 14px;border-radius:30px;font-size:12px;font-weight:700;z-index:2}
        .wishlist-icon{position:absolute;top:15px;right:15px;background:rgba(255,255,255,0.95);width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:0.3s;z-index:3}
        .wishlist-icon:hover{background:#e84393;transform:scale(1.1)}
        .wishlist-icon:hover i{color:white}
        .product-image{position:relative;overflow:hidden;height:320px;background:#f5f5f5}
        .product-image img{width:100%;height:100%;object-fit:cover;transition:transform 0.6s}
        .product-card:hover .product-image img{transform:scale(1.08)}
        .product-overlay{position:absolute;bottom:-70px;left:0;right:0;display:flex;justify-content:center;gap:15px;padding:18px;background:linear-gradient(to top,rgba(0,0,0,0.9),rgba(0,0,0,0.5));backdrop-filter:blur(8px);transition:0.3s}
        .product-card:hover .product-overlay{bottom:0}
        .overlay-btn{background:white;border:none;padding:12px 22px;border-radius:40px;font-size:13px;font-weight:600;cursor:pointer;transition:0.3s;display:flex;align-items:center;gap:8px}
        .overlay-btn:hover{background:#e84393;color:white}
        .product-info{padding:22px}
        .product-brand{font-size:11px;color:#e84393;text-transform:uppercase;font-weight:700;letter-spacing:1.5px;margin-bottom:8px}
        .product-title{font-size:20px;font-weight:700;color:#1a1a2e;margin-bottom:10px}
        .product-price{font-size:24px;font-weight:800;color:#e84393;margin-bottom:12px}
        
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
        .footer-bottom p{color:#9aa0a6;font-size:13px}
        
        .toast-notification{position:fixed;bottom:30px;right:30px;background:#1a1a2e;color:white;padding:15px 25px;border-radius:50px;display:flex;align-items:center;gap:12px;transform:translateX(450px);transition:0.4s;z-index:9999}
        .toast-notification.show{transform:translateX(0)}
        
        @media(max-width:992px){
            body{padding-top:70px}
            header{padding:15px 20px}
            .navbar{position:fixed;top:70px;left:-280px;width:280px;height:calc(100vh-70px);background:white;flex-direction:column;padding:30px;transition:0.3s;z-index:999;gap:25px}
            .navbar.show{left:0}
            .menu-icon{display:block!important}
            .hero-products h1{font-size:36px}
            .footer-container{grid-template-columns:repeat(2,1fr)}
        }
        @media(max-width:768px){
            .products-grid{grid-template-columns:1fr}
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
            <a href="products.php" class="active">Produits</a>
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
    Découvrez notre collection beauté. 💄
</div>
<?php endif; ?>

<section class="hero-products"><h1>💄 Nos produits beauté</h1><p>Découvrez notre collection de cosmétiques et soins du visage</p></section>

<section class="filters-section">
    <div class="search-wrapper">
        <div class="search-box">
            <form method="GET">
                <input type="text" name="search" placeholder="Rechercher un produit..." value="<?= htmlspecialchars($search_term) ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
                <?php if(!empty($brand_filter)): ?><input type="hidden" name="brand" value="<?= htmlspecialchars($brand_filter) ?>"><?php endif; ?>
            </form>
        </div>
        <div class="brand-filter">
            <form method="GET">
                <select name="brand" onchange="this.form.submit()">
                    <option value="">Toutes les marques</option>
                    <?php foreach($brands as $b): ?>
                        <option value="<?= htmlspecialchars($b) ?>" <?= $brand_filter == $b ? 'selected' : '' ?>><?= htmlspecialchars($b) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if(!empty($search_term)): ?><input type="hidden" name="search" value="<?= htmlspecialchars($search_term) ?>"><?php endif; ?>
            </form>
        </div>
        <?php if(!empty($search_term) || !empty($brand_filter)): ?>
            <a href="products.php" class="reset-btn"><i class="fas fa-times"></i> Réinitialiser</a>
        <?php endif; ?>
    </div>
</section>

<div class="products-section">
    <div class="products-grid">
        <?php if(count($products) > 0): foreach($products as $p): 
            $discount = (!empty($p['ancien_prix']) && $p['ancien_prix'] > 0) ? round((1 - $p['prix'] / $p['ancien_prix']) * 100) : 0; 
            $in_wishlist = isset($_SESSION['wishlist'][$p['id']]); 
            // ========== AFFICHAGE DE L'IMAGE CORRIGÉ ==========
            $imageFile = '../assets/images/' . $p['image'];
            $hasImage = !empty($p['image']) && file_exists($imageFile);
        ?>
        <div class="product-card" data-id="<?= $p['id'] ?>">
            <?php if($discount > 0): ?>
                <div class="product-badge">-<?= $discount ?>%</div>
            <?php endif; ?>
            <div class="wishlist-icon wishlist-btn" data-id="<?= $p['id'] ?>"><i class="<?= $in_wishlist ? 'fas' : 'far' ?> fa-heart"></i></div>
            <div class="product-image">
                <a href="product-details.php?id=<?= $p['id'] ?>">
                    <?php if($hasImage): ?>
                        <img src="../assets/images/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['nom']) ?>">
                    <?php else: ?>
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#f8f9fa;color:#999;font-size:14px;">
                            <i class="fas fa-image" style="font-size:40px;margin-bottom:10px;display:block;"></i>
                            <span>No Image</span>
                        </div>
                    <?php endif; ?>
                </a>
                <div class="product-overlay">
                    <button class="overlay-btn add-to-cart-btn"><i class="fas fa-shopping-cart"></i> Ajouter</button>
                    <button class="overlay-btn" onclick="location.href='product-details.php?id=<?= $p['id'] ?>'"><i class="fas fa-eye"></i> Voir</button>
                </div>
            </div>
            <div class="product-info">
                <div class="product-brand"><?= htmlspecialchars($p['marque']) ?></div>
                <div class="product-title"><?= htmlspecialchars($p['nom']) ?></div>
                <div class="product-price"><?= number_format($p['prix'], 2) ?> DH</div>
            </div>
        </div>
        <?php endforeach; else: ?>
            <div class="empty-state"><i class="fas fa-search"></i><h3>Aucun produit trouvé</h3><a href="products.php" class="btn-primary" style="background:#e84393;">Voir tous les produits</a></div>
        <?php endif; ?>
    </div>
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
function showToast(msg){let t=document.getElementById('toastNotification');t.innerHTML='<i class="fas fa-check-circle"></i> '+msg;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),3000);}
function updateCartBadge(){fetch('../php_files/get_cart.php').then(r=>r.json()).then(data=>{let c=data.items?data.items.reduce((s,i)=>s+i.qty,0):0;let b=document.querySelector('.cart-badge');if(b)b.textContent=c;}).catch(e=>console.error(e));}
function updateWishlistBadge(){let wishlist=<?= json_encode(array_keys($_SESSION['wishlist'])) ?>;let b=document.querySelector('.wishlist-badge');if(b)b.textContent=wishlist.length;}
document.querySelectorAll('.add-to-cart-btn').forEach(btn=>{btn.addEventListener('click',function(e){e.preventDefault();e.stopPropagation();let id=this.closest('.product-card').dataset.id;fetch('../php_files/add_to_cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id+'&qty=1'}).then(r=>r.json()).then(d=>{if(d.success){showToast('✓ Produit ajouté au panier');updateCartBadge();}}).catch(e=>console.error(e));});});
document.querySelectorAll('.wishlist-btn').forEach(btn=>{btn.addEventListener('click',function(e){e.preventDefault();e.stopPropagation();let id=this.dataset.id;let icon=this.querySelector('i');fetch('product-details.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'wishlist_toggle=1&product_id='+id}).then(r=>r.json()).then(d=>{if(d.success){if(d.in_wishlist){icon.className='fas fa-heart';showToast('❤️ Ajouté aux favoris');}else{icon.className='far fa-heart';showToast('💔 Retiré des favoris');}updateWishlistBadge();}}).catch(e=>console.error(e));});});
document.addEventListener('DOMContentLoaded',function(){updateCartBadge();updateWishlistBadge();});
</script>

<?php require_once '../php_files/chatbot_widget.php'; ?>
</body>
</html>
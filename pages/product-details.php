<?php
session_start();
require_once __DIR__ . '/../php_files/config.php';

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

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: products.php'); exit(); }

$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) { header('Location: products.php'); exit(); }

if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wishlist_toggle'])) {
    $product_id = (int)$_POST['product_id'];
    if (isset($_SESSION['wishlist'][$product_id])) {
        unset($_SESSION['wishlist'][$product_id]);
        $in_wishlist = false;
    } else {
        $_SESSION['wishlist'][$product_id] = [
            'id' => $product['id'], 'name' => $product['nom'], 'price' => (float)$product['prix'],
            'old_price' => (float)$product['ancien_prix'], 'img' => $product['image'],
            'desc' => $product['description'], 'ml' => $product['ml'], 'brand' => $product['marque']
        ];
        $in_wishlist = true;
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'in_wishlist' => $in_wishlist, 'count' => count($_SESSION['wishlist'])]);
    exit();
}

if (!isset($_SESSION['ratings'])) $_SESSION['ratings'] = [];
if (!isset($_SESSION['ratings'][$id])) { $_SESSION['ratings'][$id] = ['avg' => 4.8, 'count' => 245]; }
$rating_data = $_SESSION['ratings'][$id];
$avg_rating = $rating_data['avg'];
$total_reviews = $rating_data['count'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating']) && isset($_SESSION['user_id'])) {
    $user_rating = (int)$_POST['rating'];
    if ($user_rating >= 1 && $user_rating <= 5) {
        $new_avg = round(($avg_rating * $total_reviews + $user_rating) / ($total_reviews + 1), 1);
        $_SESSION['ratings'][$id] = ['avg' => $new_avg, 'count' => $total_reviews + 1];
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'new_avg' => number_format($new_avg, 1), 'new_count' => $total_reviews + 1]);
        exit();
    }
}

$in_wishlist = isset($_SESSION['wishlist'][$id]);
$prices_ml = ['50ml' => round($product['prix'] * 0.75, 2), '100ml' => round($product['prix'], 2), '150ml' => round($product['prix'] * 1.35, 2)];
$cart_count = getCartCount();
$wishlist_count = getWishlistCount();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['nom']) ?> - Tajmil Lwajh</title>
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
        .detail-page{max-width:1200px;margin:40px auto;padding:0 20px;display:grid;grid-template-columns:1fr 1fr;gap:60px}
        .detail-left img{width:100%;max-width:450px;border-radius:30px;margin:0 auto;display:block}
        .detail-brand{font-size:14px;color:#e84393;text-transform:uppercase;font-weight:700;margin-bottom:10px}
        .detail-title{font-size:36px;font-weight:800;color:#1a1a2e;margin-bottom:15px}
        .detail-rating{display:flex;align-items:center;gap:15px;margin-bottom:25px;flex-wrap:wrap}
        .stars-container i{font-size:20px;cursor:pointer;color:#ddd}
        .stars-container i.active,.stars-container i:hover{color:#ffc107}
        .detail-price{display:flex;align-items:center;gap:15px;margin-bottom:20px}
        .price-display{font-size:32px;font-weight:800;color:#e84393}
        .old-price{font-size:20px;color:#adb5bd;text-decoration:line-through}
        .detail-desc{font-size:16px;color:#4a4a6a;line-height:1.7;margin-bottom:30px}
        .ml-choice{margin:25px 0}
        .ml-buttons{display:flex;gap:12px;flex-wrap:wrap}
        .ml-btn{padding:10px 22px;border:2px solid #e8e8f0;border-radius:40px;cursor:pointer;background:white}
        .ml-btn:hover,.ml-btn.active{background:#e84393;color:white;border-color:#e84393}
        .detail-qty{display:flex;align-items:center;gap:15px;margin:30px 0}
        .qty-input{width:80px;padding:12px;border:2px solid #e8e8f0;border-radius:12px;text-align:center}
        .action-buttons{display:flex;gap:15px;margin-top:20px;flex-wrap:wrap}
        .btn-add{background:#1a1a2e;color:white;padding:15px 30px;border-radius:50px;cursor:pointer;flex:2;border:none;font-weight:600;display:flex;align-items:center;justify-content:center;gap:10px}
        .btn-add:hover{background:#e84393}
        .btn-wishlist{background:white;border:2px solid #e8e8f0;border-radius:50px;cursor:pointer;flex:1;font-size:24px;display:flex;align-items:center;justify-content:center}
        .btn-wishlist:hover{border-color:#e84393;color:#e84393}
        .related-section{max-width:1200px;margin:60px auto;padding:0 20px}
        .related-title{font-size:28px;font-weight:800;color:#1a1a2e;margin-bottom:30px;text-align:center}
        .related-title span{color:#e84393}
        .related-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:25px}
        .related-product{background:white;border-radius:20px;padding:20px;text-align:center;border:1px solid #e8e8f0;transition:0.3s}
        .related-product:hover{transform:translateY(-5px);border-color:#e84393}
        .related-product img{width:100%;height:180px;object-fit:cover;border-radius:15px;margin-bottom:15px}
        .related-product h4{font-size:14px;font-weight:700;margin-bottom:8px}
        .related-product .price{color:#e84393;font-weight:800}
        .toast-msg{position:fixed;bottom:30px;right:30px;background:#1a1a2e;color:white;padding:15px 25px;border-radius:50px;transform:translateX(450px);transition:0.4s;z-index:9999}
        .toast-msg.show{transform:translateX(0)}
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
        @keyframes heartBeat{0%,100%{transform:scale(1)}50%{transform:scale(1.3)}}
        .heart-animation{animation:heartBeat 0.3s ease}
        @media(max-width:992px){
            body{padding-top:70px}
            header{padding:15px 20px}
            .navbar{position:fixed;top:70px;left:-280px;width:280px;height:calc(100vh-70px);background:white;flex-direction:column;padding:30px;transition:0.3s;z-index:999;gap:25px}
            .navbar.show{left:0}
            .menu-icon{display:block!important}
            .detail-page{grid-template-columns:1fr;text-align:center}
            .footer-container{grid-template-columns:repeat(2,1fr)}
        }
        @media(max-width:768px){
            .detail-title{font-size:28px}
            .footer-container{grid-template-columns:1fr}
            .toast-msg{left:20px;right:20px;bottom:20px;transform:translateY(150px)}
            .toast-msg.show{transform:translateY(0)}
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
    Découvrez ce produit en détail. ✨
</div>
<?php endif; ?>

<div class="detail-page">
    <div class="detail-left"><img src="../assets/images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['nom']) ?>"></div>
    <div class="detail-right">
        <div class="detail-brand"><?= htmlspecialchars($product['marque']) ?></div>
        <h1 class="detail-title"><?= htmlspecialchars($product['nom']) ?></h1>
        <div class="detail-rating">
            <div class="stars-container"><?php for($i=1;$i<=5;$i++): ?><i class="fas fa-star star <?= $i<=round($avg_rating)?'active':'' ?>" data-value="<?= $i ?>"></i><?php endfor; ?></div>
            <span class="rating-avg" id="ratingAvgDisplay"><?= number_format($avg_rating,1) ?></span>
            <span class="rating-count" id="ratingCountDisplay">(<?= $total_reviews ?> avis)</span>
        </div>
        <div class="ml-choice"><h3>Contenance :</h3><div class="ml-buttons"><button class="ml-btn" data-ml="50ml" data-price="<?= $prices_ml['50ml'] ?>">50 ml - <?= number_format($prices_ml['50ml'],2) ?> DH</button><button class="ml-btn active" data-ml="100ml" data-price="<?= $prices_ml['100ml'] ?>">100 ml - <?= number_format($prices_ml['100ml'],2) ?> DH</button><button class="ml-btn" data-ml="150ml" data-price="<?= $prices_ml['150ml'] ?>">150 ml - <?= number_format($prices_ml['150ml'],2) ?> DH</button></div></div>
        <div class="detail-price"><span class="price-display" id="priceDisplay"><?= number_format($prices_ml['100ml'],2) ?> DH</span><?php if(!empty($product['ancien_prix']) && $product['ancien_prix'] > 0): ?><span class="old-price"><?= number_format($product['ancien_prix'],2) ?> DH</span><?php endif; ?></div>
        <div class="detail-desc"><?= nl2br(htmlspecialchars($product['description'])) ?></div>
        <div class="detail-qty"><label><strong>Quantité :</strong></label><input type="number" id="productQty" value="1" min="1" max="10" class="qty-input"></div>
        <div class="action-buttons"><button class="btn-add" id="addToCartBtn"><i class="fas fa-shopping-cart"></i> Ajouter au panier</button><button class="btn-wishlist" id="wishlistBtn" data-id="<?= $product['id'] ?>"><i class="<?= $in_wishlist?'fas':'far' ?> fa-heart" style="color:<?= $in_wishlist?'#e84393':'#1a1a2e' ?>;"></i></button></div>
    </div>
</div>

<section class="related-section" id="relatedSection" style="display:none">
    <h2 class="related-title">✨ <span>Produits similaires</span> ✨</h2>
    <div class="related-grid" id="relatedGrid"></div>
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

<div id="toastMsg" class="toast-msg"></div>

<script>
function toggleMobileMenu(e){if(e)e.stopPropagation();document.getElementById('mainNavbar').classList.toggle('show');}
function toggleUserMenu(e){e.stopPropagation();let d=document.getElementById('userDropdown');if(d)d.classList.toggle('show');}
document.addEventListener('click',function(e){let d=document.getElementById('userDropdown');let n=document.getElementById('mainNavbar');if(d&&!d.contains(e.target))d.classList.remove('show');if(n&&!n.contains(e.target))n.classList.remove('show');});
function showToast(msg,isSuccess=true){let t=document.getElementById('toastMsg');t.innerHTML=(isSuccess?'<i class="fas fa-check-circle"></i> ':'<i class="fas fa-times-circle"></i> ')+msg;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),3000);}
function updateCartBadge(){fetch('../php_files/get_cart.php').then(r=>r.json()).then(d=>{let c=0;if(d&&d.items)c=d.items.reduce((s,i)=>s+i.qty,0);let b=document.querySelector('.cart-badge');if(b)b.textContent=c;}).catch(e=>console.error(e));}
function updateWishlistBadge(count=null){let b=document.querySelector('.wishlist-badge');if(!b)return;if(count!==null)b.textContent=count;}
function loadRelated(){fetch('../php_files/get_products.php').then(r=>r.json()).then(products=>{let currentId=<?= $id ?>;let filtered=products.filter(p=>p.id!=currentId).sort(()=>0.5-Math.random()).slice(0,4);if(filtered.length>0){document.getElementById('relatedSection').style.display='block';let html='';filtered.forEach(p=>{html+=`<div class="related-product"><img src="../assets/images/${escapeHtml(p.image)}"><h4>${escapeHtml(p.nom)}</h4><div class="price">${parseFloat(p.prix).toFixed(2)} DH</div><a href="product-details.php?id=${p.id}" style="display:inline-block;margin-top:10px;color:#e84393;text-decoration:none;"><i class="fas fa-eye"></i> Voir</a></div>`;});document.getElementById('relatedGrid').innerHTML=html;}}).catch(e=>console.error(e));}
function escapeHtml(str){return str.replace(/[&<>]/g,function(m){if(m==='&')return '&amp;';if(m==='<')return '&lt;';if(m==='>')return '&gt;';return m;});}
document.querySelectorAll('.ml-btn').forEach(btn=>{btn.addEventListener('click',function(){document.querySelectorAll('.ml-btn').forEach(b=>b.classList.remove('active'));this.classList.add('active');let price=parseFloat(this.dataset.price);document.getElementById('priceDisplay').textContent=price.toFixed(2)+' DH';});});
document.getElementById('addToCartBtn').addEventListener('click',function(){let qty=document.getElementById('productQty').value;let id=<?= $product['id'] ?>;let ml=document.querySelector('.ml-btn.active').dataset.ml;fetch('../php_files/add_to_cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id+'&qty='+qty+'&ml='+ml}).then(r=>r.json()).then(d=>{if(d.success){showToast('✓ Produit ajouté au panier');updateCartBadge();}else showToast('Erreur',false);}).catch(e=>console.error(e));});
document.getElementById('wishlistBtn').addEventListener('click',function(){let id=this.dataset.id;let icon=this.querySelector('i');icon.classList.add('heart-animation');setTimeout(()=>icon.classList.remove('heart-animation'),300);fetch(window.location.href,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'wishlist_toggle=1&product_id='+id}).then(r=>r.json()).then(d=>{if(d.success){if(d.in_wishlist){icon.className='fas fa-heart';icon.style.color='#e84393';showToast('❤️ Ajouté aux favoris');}else{icon.className='far fa-heart';icon.style.color='#1a1a2e';showToast('💔 Retiré des favoris');}updateWishlistBadge(d.count);}}).catch(e=>console.error(e));});
<?php if(isset($_SESSION['user_id'])): ?>
document.querySelectorAll('.star').forEach(star=>{star.addEventListener('click',function(){let rating=this.dataset.value;let productId=<?= $product['id'] ?>;fetch(window.location.href,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'rating='+rating+'&product_id='+productId}).then(r=>r.json()).then(d=>{if(d.success){document.querySelectorAll('.star').forEach((s,idx)=>{if(idx<rating)s.classList.add('active');else s.classList.remove('active');});document.getElementById('ratingAvgDisplay').textContent=d.new_avg;document.getElementById('ratingCountDisplay').textContent=`(${d.new_count} avis)`;showToast('⭐ Merci pour votre note de '+rating+' étoiles !');}}).catch(e=>console.error(e));});});
<?php endif; ?>
document.addEventListener('DOMContentLoaded',function(){updateCartBadge();loadRelated();});
</script>

<?php require_once '../php_files/chatbot_widget.php'; ?>
</body>
</html>
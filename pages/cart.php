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
    <title>Mon panier - Tajmil Lwajh</title>
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
        .cart-hero{background:linear-gradient(135deg,#1a1a2e,#2d1b36);padding:60px 5%;text-align:center}
        .cart-hero h1{font-size:48px;font-weight:800;color:white;margin-bottom:15px}
        .cart-hero p{font-size:18px;color:rgba(255,255,255,0.8)}
        .cart-container{max-width:1200px;margin:40px auto;padding:0 20px;min-height:400px}
        .cart-table{width:100%;border-collapse:collapse;background:white;border-radius:20px;overflow:hidden;border:1px solid #e8e8f0}
        .cart-table th,.cart-table td{padding:18px 15px;border-bottom:1px solid #e8e8f0;text-align:center;vertical-align:middle}
        .cart-table th{background:#f8f9fa;color:#4a4a6a;font-weight:600;font-size:14px;text-transform:uppercase}
        .qty-input{width:70px;padding:10px;text-align:center;border:2px solid #e8e8f0;border-radius:12px;outline:none}
        .qty-input:focus{border-color:#e84393}
        .btn-remove{background:none;border:none;color:#dc3545;cursor:pointer;padding:8px 12px;border-radius:40px}
        .btn-remove:hover{background:#fee;color:#c2185b}
        .cart-summary-box{background:white;padding:30px;border-radius:20px;margin-top:30px;border:1px solid #e8e8f0}
        .cart-total{text-align:right;font-size:28px;font-weight:800;color:#e84393;margin-bottom:25px}
        .cart-actions{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px}
        .btn-continue,.btn-checkout{display:inline-flex;align-items:center;gap:10px;padding:14px 30px;border-radius:50px;text-decoration:none;font-weight:600;transition:0.3s}
        .btn-continue{background:#f0f0f5;color:#1a1a2e}
        .btn-continue:hover{background:#e8e8f0;transform:translateY(-2px)}
        .btn-checkout{background:#e84393;color:white;box-shadow:0 4px 12px rgba(232,67,147,0.2)}
        .btn-checkout:hover{background:#c2185b;transform:translateY(-2px)}
        .empty-cart{text-align:center;padding:80px 40px;background:white;border-radius:24px;border:1px solid #e8e8f0}
        .empty-cart i{font-size:70px;color:#e8e8f0;margin-bottom:20px}
        .empty-cart .btn-shop{background:#e84393;color:white;padding:14px 35px;border-radius:50px;text-decoration:none;display:inline-block;margin-top:20px}
        .recommended-section{max-width:1200px;margin:60px auto;padding:0 20px}
        .recommended-title{font-size:28px;font-weight:800;color:#1a1a2e;margin-bottom:30px;text-align:center}
        .recommended-title span{color:#e84393}
        .recommended-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:25px}
        .rec-product{background:white;border-radius:20px;padding:20px;text-align:center;border:1px solid #e8e8f0;transition:0.3s}
        .rec-product:hover{transform:translateY(-5px);border-color:#e84393}
        .rec-product img{width:100%;height:200px;object-fit:cover;border-radius:15px;margin-bottom:15px}
        .rec-product h4{font-size:16px;font-weight:700;margin-bottom:8px}
        .rec-product .price{color:#e84393;font-weight:800;margin-bottom:15px}
        .rec-product .btn-add{background:#1a1a2e;color:white;padding:8px 20px;border-radius:40px;text-decoration:none;font-size:13px}
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
            .cart-table th,.cart-table td{padding:12px 8px}
            .qty-input{width:50px}
            .btn-remove span{display:none}
            .cart-actions{flex-direction:column}
            .btn-continue,.btn-checkout{width:100%;justify-content:center}
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
    Votre panier vous attend. 🛒
</div>
<?php endif; ?>

<section class="cart-hero"><h1>🛒 Mon panier</h1><p>Vérifiez vos produits avant de valider votre commande</p></section>

<div class="cart-container"><div id="cartContent">Chargement...</div></div>

<!-- Section produits recommandés -->
<section class="recommended-section" id="recommendedSection" style="display:none">
    <h2 class="recommended-title">✨ <span>Vous aimerez aussi</span> ✨</h2>
    <div class="recommended-grid" id="recommendedGrid"></div>
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

function showToast(msg,isError=false){let t=document.getElementById('toastNotification');let icon=isError?'fa-exclamation-circle':'fa-check-circle';t.innerHTML=`<i class="fas ${icon}"></i> <span>${msg}</span>`;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),3500);}
function escapeHtml(str){return str.replace(/[&<>]/g,function(m){if(m==='&')return '&amp;';if(m==='<')return '&lt;';if(m==='>')return '&gt;';return m;});}
function updateCartBadge(){let cart=<?= isset($_SESSION['cart'])?json_encode(array_values($_SESSION['cart'])):'[]' ?>;let c=0;for(let i=0;i<cart.length;i++)c+=cart[i].qty;let b=document.querySelector('.cart-badge');if(b)b.textContent=c;}
function updateWishlistBadge(){let wishlist=<?= isset($_SESSION['wishlist'])?json_encode(array_keys($_SESSION['wishlist'])):'[]' ?>;let b=document.querySelector('.wishlist-badge');if(b)b.textContent=wishlist.length;}

function loadCart(){
    fetch('../php_files/get_cart.php').then(r=>r.json()).then(data=>{
        let container=document.getElementById('cartContent');
        if(!data.items||data.items.length===0){
            container.innerHTML=`<div class="empty-cart"><i class="far fa-shopping-cart"></i><p>Votre panier est vide</p><a href="products.php" class="btn-shop">Découvrir nos produits</a></div>`;
            document.getElementById('recommendedSection').style.display='block';
            loadRecommended();
            return;
        }
        let html=`<table class="cart-table"><thead><tr><th>Produit</th><th>Prix</th><th>Quantité</th><th>Total</th><th>Action</th></tr></thead><tbody>`;
        let total=0;
        data.items.forEach(item=>{let prix=parseFloat(item.prix);let qty=parseInt(item.qty);let itemTotal=prix*qty;total+=itemTotal;html+=`<tr data-id="${item.id}"><td>${escapeHtml(item.nom)}</td><td>${prix.toFixed(2)} DH</td><td><input type="number" value="${qty}" min="1" onchange="updateQty(${item.id}, this.value)" class="qty-input"></td><td>${itemTotal.toFixed(2)} DH</td><td><button onclick="removeItem(${item.id})" class="btn-remove"><i class="fas fa-trash-alt"></i> <span>Supprimer</span></button></td></tr>`;});
        html+=`</tbody></table><div class="cart-summary-box"><div class="cart-total">Total : ${total.toFixed(2)} DH</div><div class="cart-actions"><a href="products.php" class="btn-continue"><i class="fas fa-arrow-left"></i> Continuer mes achats</a><a href="../php_files/checkout.php" class="btn-checkout">Passer commande <i class="fas fa-arrow-right"></i></a></div></div>`;
        container.innerHTML=html;
        document.getElementById('recommendedSection').style.display='block';
        loadRecommended();
    }).catch(err=>{showToast("Erreur lors du chargement du panier",true);});
}

function loadRecommended(){
    fetch('../php_files/get_products.php').then(r=>r.json()).then(products=>{
        let randomProducts = products.sort(()=>0.5-Math.random()).slice(0,4);
        let grid=document.getElementById('recommendedGrid');
        let html='';
        randomProducts.forEach(p=>{
            html+=`<div class="rec-product"><img src="../assets/images/${escapeHtml(p.image)}" alt="${escapeHtml(p.nom)}"><h4>${escapeHtml(p.nom)}</h4><div class="price">${parseFloat(p.prix).toFixed(2)} DH</div><a href="product-details.php?id=${p.id}" class="btn-add"><i class="fas fa-eye"></i> Voir</a></div>`;
        });
        grid.innerHTML=html;
    }).catch(e=>console.error(e));
}

window.updateQty=function(id,qty){if(qty<1)qty=1;fetch('../php_files/update_cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id+'&qty='+qty}).then(()=>{loadCart();updateCartBadge();showToast("Quantité mise à jour");}).catch(()=>showToast("Erreur",true));};

window.removeItem=function(id){if(confirm("Voulez-vous retirer ce produit du panier ?")){fetch('../php_files/remove_cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id}).then(()=>{loadCart();updateCartBadge();showToast("Produit retiré du panier");}).catch(()=>showToast("Erreur",true));}};

document.addEventListener('DOMContentLoaded',function(){loadCart();updateCartBadge();updateWishlistBadge();});
</script>

<?php require_once '../php_files/chatbot_widget.php'; ?>
</body>
</html>
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
    <title>Contact - Tajmil Lwajh</title>
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
        .hero-contact{background:linear-gradient(135deg,#1a1a2e,#2d1b36);padding:80px 20px;text-align:center;color:white}
        .hero-contact h1{font-size:44px;font-weight:800}
        .contact-section{max-width:1200px;margin:60px auto;padding:0 20px;display:grid;grid-template-columns:1fr 1.3fr;gap:50px}
        .contact-info h2{font-size:28px;font-weight:800;color:#1a1a2e;margin-bottom:15px}
        .info-card{background:white;border-radius:20px;padding:25px;display:flex;align-items:center;gap:20px;margin-bottom:15px;border:1px solid #e8e8f0}
        .info-card i{width:50px;height:50px;background:#fff5f8;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;color:#e84393}
        .social-links{display:flex;gap:14px;margin-top:30px}
        .social-links a{width:42px;height:42px;background:#f0f0f5;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#1a1a2e;font-size:18px;transition:0.3s;text-decoration:none}
        .social-links a:hover{background:#e84393;color:white}
        .contact-form{background:white;padding:40px;border-radius:24px;border:1px solid #e8e8f0}
        .contact-form h2{font-size:24px;font-weight:800;color:#1a1a2e;margin-bottom:25px}
        .form-group{margin-bottom:20px}
        .form-group input,.form-group textarea{width:100%;padding:14px 20px;background:#f8f9fa;border:2px solid #f8f9fa;border-radius:12px;font-size:15px;outline:none;font-family:inherit}
        .form-group input:focus,.form-group textarea:focus{border-color:#e84393;background:white}
        .form-group textarea{height:140px;resize:vertical}
        .submit-btn{background:#e84393;color:white;border:none;padding:15px 35px;border-radius:50px;font-weight:700;cursor:pointer;width:100%;display:flex;align-items:center;justify-content:center;gap:10px}
        .submit-btn:hover{background:#c2185b}
        .map-section{max-width:1200px;margin:0 auto 80px;padding:0 20px}
        .map-container{border-radius:24px;overflow:hidden;height:400px}
        .map-container iframe{width:100%;height:100%;border:none}
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
            .contact-section{grid-template-columns:1fr}
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
            <a href="about.php">À propos</a>
            <a href="review.php">Avis</a>
            <a href="contact.php" class="active">Contact</a>
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
    Une question ? Nous sommes là pour vous aider. 📞
</div>
<?php endif; ?>

<section class="hero-contact"><h1>📞 Contactez-nous</h1><p>Une question sur nos produits ou services ? Notre équipe est à votre écoute</p></section>

<section class="contact-section">
    <div class="contact-info">
        <h2>Entrons en contact</h2>
        <div class="info-card"><i class="fas fa-map-marker-alt"></i><div><h3>Adresse</h3><p>Casablanca, Maroc</p></div></div>
        <div class="info-card"><i class="fas fa-phone"></i><div><h3>Téléphone</h3><p>+212 723 699 031</p></div></div>
        <div class="info-card"><i class="fas fa-envelope"></i><div><h3>Email</h3><p>contact@tajmil.ma</p></div></div>
        <div class="info-card"><i class="fab fa-whatsapp"></i><div><h3>WhatsApp</h3><p>+212 723 699 031</p></div></div>
        <div class="social-links">
            <a href="#"><i class="fab fa-facebook-messenger"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-whatsapp"></i></a>
            <a href="#"><i class="fab fa-tiktok"></i></a>
        </div>
    </div>
    <div class="contact-form">
        <h2>Envoyez-nous un message</h2>
        <form id="contactForm">
            <div class="form-group"><input type="text" id="nom" placeholder="Votre nom" required></div>
            <div class="form-group"><input type="email" id="email" placeholder="Votre email" required></div>
            <div class="form-group"><textarea id="message" placeholder="Votre message..." required></textarea></div>
            <button type="submit" class="submit-btn"><i class="fas fa-paper-plane"></i> Envoyer</button>
        </form>
    </div>
</section>

<section class="map-section">
    <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3309.0255446040097!2d-7.631219984786141!3d33.573099680740515!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xda7cd4778aa33f7%3A0x7b7c1b6c0d8c5f4!2sCasablanca%2C%20Maroc!5e0!3m2!1sfr!2s!4v1700000000000!5m2!1sfr!2s" allowfullscreen></iframe>
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
function showToast(msg,isError=false){let t=document.getElementById('toastNotification');t.innerHTML=(isError?'<i class="fas fa-exclamation-circle"></i> ':'<i class="fas fa-check-circle"></i> ')+msg;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),3000);}
function updateCartBadge(){let cart=<?= isset($_SESSION['cart'])?json_encode(array_values($_SESSION['cart'])):'[]' ?>;let c=0;for(let i=0;i<cart.length;i++)c+=cart[i].qty;let b=document.querySelector('.cart-badge');if(b)b.textContent=c;}
function updateWishlistBadge(){let wishlist=<?= isset($_SESSION['wishlist'])?json_encode(array_keys($_SESSION['wishlist'])):'[]' ?>;let b=document.querySelector('.wishlist-badge');if(b)b.textContent=wishlist.length;}
document.getElementById('contactForm').addEventListener('submit',function(e){e.preventDefault();fetch('../php_files/contact.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({nom:document.getElementById('nom').value,email:document.getElementById('email').value,message:document.getElementById('message').value})}).then(r=>r.json()).then(d=>{if(d.success){showToast('Message envoyé avec succès !');this.reset();}else{showToast('Erreur',true);}}).catch(e=>showToast('Erreur réseau',true));});
document.addEventListener('DOMContentLoaded',function(){updateCartBadge();updateWishlistBadge();});
</script>

<?php require_once '../php_files/chatbot_widget.php'; ?>
</body>
</html>
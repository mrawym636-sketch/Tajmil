<?php
session_start();
if (isset($_SESSION['user_id'])) header('Location: home.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Tajmil Lwajh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Même style que les autres pages avec logo Tajmil */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%); min-height: 100vh; padding-top: 80px; }
        header { position: fixed; top: 0; left: 0; right: 0; background: rgba(255,255,255,0.98); backdrop-filter: blur(10px); box-shadow: 0 2px 15px rgba(0,0,0,0.05); z-index: 1000; padding: 15px 5%; }
        .header-container { max-width: 1400px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .logo { font-size: 28px; font-weight: 800; text-decoration: none; color: #1a1a2e; letter-spacing: -0.5px; line-height: 1; }
        .logo span { color: #e84393; }
        .navbar { display: flex; gap: 35px; }
        .navbar a { text-decoration: none; color: #4a4a6a; font-weight: 500; font-size: 15px; transition: 0.3s; }
        .navbar a:hover { color: #e84393; }
        .menu-icon { display: none; cursor: pointer; font-size: 24px; color: #1a1a2e; }
        .login-container { min-height: calc(100vh - 80px); display: flex; align-items: center; justify-content: center; padding: 40px 20px 60px; }
        .login-box { background: white; border-radius: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.08); max-width: 480px; width: 100%; padding: 50px; transition: 0.3s; }
        .login-box:hover { transform: translateY(-5px); }
        .login-header { text-align: center; margin-bottom: 40px; }
        .login-header i { font-size: 60px; color: #e84393; margin-bottom: 15px; }
        .login-header h1 { font-size: 32px; font-weight: 800; color: #1a1a2e; }
        .login-header p { color: #6c757d; margin-top: 10px; }
        .form-group { margin-bottom: 25px; }
        .form-group input { width: 100%; padding: 15px 20px; border: 2px solid #e8e8f0; border-radius: 15px; font-size: 15px; outline: none; transition: 0.3s; font-family: inherit; }
        .form-group input:focus { border-color: #e84393; }
        .login-btn { background: #e84393; color: white; border: none; padding: 15px; border-radius: 50px; font-size: 16px; font-weight: 700; cursor: pointer; transition: 0.3s; width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .login-btn:hover { background: #c2185b; transform: translateY(-2px); }
        .register-link { text-align: center; margin-top: 25px; color: #6c757d; }
        .register-link a { color: #e84393; text-decoration: none; font-weight: 600; }
        .error-message { background: #fee; color: #e84393; padding: 12px; border-radius: 12px; margin-bottom: 20px; text-align: center; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 500; }
        .global-footer { background: #1a1a2e; color: #ffffff; padding: 60px 20px 20px; margin-top: 60px; border-top: 3px solid #e84393; }
        .footer-container { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 40px; }
        .footer-col h3 { font-size: 26px; font-weight: 700; margin-bottom: 20px; color: #fff; letter-spacing: 0.5px; }
        .footer-col h3 span { color: #e84393; }
        .footer-col h4 { font-size: 18px; font-weight: 600; margin-bottom: 20px; position: relative; padding-bottom: 8px; }
        .footer-col h4::after { content: ''; position: absolute; left: 0; bottom: 0; width: 35px; height: 2px; background: #e84393; }
        .footer-col p { color: #bdc3c7; font-size: 14px; line-height: 1.6; margin-bottom: 15px; }
        .footer-col ul { list-style: none; }
        .footer-col ul li { margin-bottom: 12px; }
        .footer-col ul li a { color: #bdc3c7; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
        .footer-col ul li a i { font-size: 10px; color: #5f6368; transition: transform 0.2s; }
        .footer-col ul li a:hover { color: #e84393; }
        .footer-col ul li a:hover i { transform: translateX(4px); color: #e84393; }
        .social-icons { display: flex; gap: 15px; margin-top: 20px; }
        .social-icons a { width: 38px; height: 38px; background: #333539; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 18px; transition: 0.3s; text-decoration: none; }
        .social-icons a:hover { background: #e84393; transform: translateY(-4px); }
        .footer-bottom { text-align: center; margin-top: 50px; padding-top: 20px; border-top: 1px solid #333539; }
        .footer-bottom p { color: #9aa0a6; font-size: 13px; }
        @media (max-width: 992px) {
            body { padding-top: 70px; }
            header { padding: 15px 20px; }
            .navbar { position: fixed; top: 70px; left: -280px; width: 280px; height: calc(100vh - 70px); background: white; flex-direction: column; padding: 30px; transition: 0.3s; z-index: 999; box-shadow: 2px 0 20px rgba(0,0,0,0.1); gap: 20px; }
            .navbar.show { left: 0; }
            .menu-icon { display: block !important; }
            .login-box { margin: 20px; padding: 35px 25px; }
            .footer-container { grid-template-columns: repeat(2,1fr); gap:30px; }
        }
        @media (max-width: 768px) { .footer-container { grid-template-columns: 1fr; } }
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
        <div class="header-right"><div style="width:40px;"></div></div>
    </div>
</header>
<div class="login-container">
    <div class="login-box">
        <div class="login-header"><i class="fas fa-user-circle"></i><h1>Connexion</h1><p>Bienvenue chez Tajmil Lwajh</p></div>
        <?php if(isset($_GET['error'])): ?>
            <div class="error-message"><i class="fas fa-exclamation-circle"></i> Email ou mot de passe incorrect</div>
        <?php endif; ?>
        <?php if(isset($_GET['registered'])): ?>
            <div class="error-message" style="background:#e0ffe0;color:#2ed573;"><i class="fas fa-check-circle"></i> Inscription réussie ! Connectez-vous</div>
        <?php endif; ?>
        <form method="POST" action="../php_files/login.php">
            <div class="form-group"><input type="email" name="email" placeholder="Adresse email" required></div>
            <div class="form-group"><input type="password" name="password" placeholder="Mot de passe" required></div>
            <button type="submit" class="login-btn"><i class="fas fa-arrow-right"></i> Se connecter</button>
            <div class="register-link">Pas encore de compte ? <a href="register.php">Créer un compte</a></div>
        </form>
    </div>
</div>
<footer class="global-footer"><div class="footer-container"><div class="footer-col about-col"><h3>Tajmil<span>.</span></h3><p>Découvrez nos produits et services de beauté premium, conçus avec élégance et passion pour sublimer votre visage.</p><div class="social-icons"><a href="https://m.me/mohamd_amroui" target="_blank"><i class="fab fa-facebook-messenger"></i></a><a href="https://ig.me/m/mohamd_amroui/" target="_blank"><i class="fab fa-instagram"></i></a><a href="https://www.tiktok.com/@mohamd_amroui" target="_blank"><i class="fab fa-tiktok"></i></a><a href="https://wa.me/212723699031" target="_blank"><i class="fab fa-whatsapp"></i></a></div></div><div class="footer-col"><h4>Navigation</h4><ul><li><a href="home.php"><i class="fas fa-chevron-right"></i> Accueil</a></li><li><a href="products.php"><i class="fas fa-chevron-right"></i> Produits</a></li><li><a href="about.php"><i class="fas fa-chevron-right"></i> À propos</a></li><li><a href="review.php"><i class="fas fa-chevron-right"></i> Avis</a></li><li><a href="contact.php"><i class="fas fa-chevron-right"></i> Contact</a></li></ul></div><div class="footer-col"><h4>Support Client</h4><ul><li><a href="#"><i class="fas fa-chevron-right"></i> FAQ</a></li><li><a href="#"><i class="fas fa-chevron-right"></i> Livraison & Retours</a></li><li><a href="#"><i class="fas fa-chevron-right"></i> Conditions Générales</a></li><li><a href="#"><i class="fas fa-chevron-right"></i> Confidentialité</a></li></ul></div><div class="footer-col contact-col"><h4>Contact & Horaires</h4><p><i class="fas fa-map-marker-alt"></i> Casablanca, Maroc</p><p><i class="fas fa-phone-alt"></i> +212 723-699031</p><p><i class="fas fa-envelope"></i> contact@tajmil.ma</p><p><i class="fas fa-clock"></i> Lun - Sam : 09:00 - 20:00</p></div></div><div class="footer-bottom"><p>&copy; 2026 Tajmil Lwajh — Tous droits réservés.</p></div></footer>
<script>
function toggleMobileMenu(e){if(e)e.stopPropagation();document.getElementById('mainNavbar').classList.toggle('show');}
document.addEventListener('click',function(e){let n=document.getElementById('mainNavbar');if(n&&n.classList.contains('show')&&!n.contains(e.target)){n.classList.remove('show');}});
</script>

<?php require_once '../php_files/chatbot_widget.php'; ?>
</body>
</html>
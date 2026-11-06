<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
require_once '../php_files/config.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) { session_destroy(); header('Location: login.php'); exit(); }

$_SESSION['user_photo'] = $user['photo'] ?? 'default.png';
$_SESSION['user_nom'] = $user['nom'];

$cart_count = getCartCount();
$wishlist_count = getWishlistCount();

$success = '';
$error = '';
$active_tab = $_GET['tab'] ?? 'avatar';

// Changer photo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_avatar'])) {
    $photo = $user['photo'] ?? 'default.png';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../assets/uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            if ($user['photo'] != 'default.png' && file_exists($upload_dir . $user['photo'])) unlink($upload_dir . $user['photo']);
            $photo = 'user_' . $user_id . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo);
            $stmt = $pdo->prepare("UPDATE utilisateurs SET photo = ? WHERE id = ?");
            $stmt->execute([$photo, $user_id]);
            $_SESSION['user_photo'] = $photo;
            $user['photo'] = $photo;
            $success = "Photo mise à jour !";
        } else { $error = "Format non autorisé"; }
    } else { $error = "Veuillez sélectionner une image"; }
}

// Modifier infos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    if (empty($nom) || empty($email)) { $error = "Nom et email obligatoires"; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $error = "Email invalide"; }
    elseif ($email !== $user['email']) {
        $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
        $check->execute([$email, $user_id]);
        if ($check->fetch()) { $error = "Cet email est déjà utilisé"; }
    }
    if (empty($error)) {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, email = ?, telephone = ? WHERE id = ?");
        $stmt->execute([$nom, $email, $telephone, $user_id]);
        $_SESSION['user_nom'] = $nom;
        $user['nom'] = $nom; $user['email'] = $email; $user['telephone'] = $telephone;
        $success = "Informations mises à jour !";
    }
}

// Changer mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    $stmt = $pdo->prepare("SELECT password FROM utilisateurs WHERE id = ?");
    $stmt->execute([$user_id]);
    $current_hash = $stmt->fetchColumn();
    if (!password_verify($current, $current_hash)) { $error = "Mot de passe actuel incorrect"; }
    elseif (strlen($new) < 6) { $error = "Minimum 6 caractères"; }
    elseif ($new !== $confirm) { $error = "Les mots de passe ne correspondent pas"; }
    else {
        $new_hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE utilisateurs SET password = ? WHERE id = ?");
        $stmt->execute([$new_hash, $user_id]);
        $success = "Mot de passe modifié !";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil - Tajmil Lwajh</title>
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
        .profile-hero{background:linear-gradient(135deg,#1a1a2e,#2d1b36);padding:60px 5%;text-align:center}
        .profile-hero h1{font-size:48px;font-weight:800;color:white;margin-bottom:15px}
        .profile-container{max-width:1000px;margin:40px auto;padding:0 20px}
        .profile-header{background:white;border-radius:20px;padding:30px;text-align:center;margin-bottom:30px}
        .profile-avatar{width:120px;height:120px;border-radius:50%;object-fit:cover;border:4px solid #e84393;margin:0 auto 15px}
        .profile-name{font-size:24px;font-weight:700;color:#1a1a2e}
        .profile-email{color:#6c757d;margin-top:5px}
        .profile-tabs{display:flex;gap:10px;margin-bottom:25px;flex-wrap:wrap;background:white;padding:10px;border-radius:12px}
        .tab-btn{flex:1;padding:12px;background:transparent;border:none;border-radius:10px;font-weight:600;color:#6c757d;cursor:pointer}
        .tab-btn.active{background:#e84393;color:white}
        .panel{background:white;border-radius:20px;padding:30px;box-shadow:0 5px 20px rgba(0,0,0,0.05)}
        .panel-title{font-size:20px;font-weight:700;color:#1a1a2e;margin-bottom:25px;padding-bottom:15px;border-bottom:2px solid #e84393}
        .form-group{margin-bottom:20px}
        .form-group label{display:block;margin-bottom:8px;font-weight:600}
        .form-group input{width:100%;padding:12px 15px;border:2px solid #e8e8f0;border-radius:12px;outline:none}
        .form-group input:focus{border-color:#e84393}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px}
        .avatar-upload{text-align:center;margin-bottom:20px}
        .upload-btn{background:#f0f0f5;padding:12px 25px;border-radius:40px;cursor:pointer;display:inline-flex;align-items:center;gap:8px}
        .upload-btn:hover{background:#e84393;color:white}
        .btn-save{background:#e84393;color:white;border:none;padding:12px 30px;border-radius:40px;font-weight:600;cursor:pointer}
        .btn-save:hover{background:#c2185b}
        .alert-success{background:#d4edda;color:#155724;padding:12px;border-radius:12px;margin-bottom:20px}
        .alert-error{background:#f8d7da;color:#721c24;padding:12px;border-radius:12px;margin-bottom:20px}
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
            .form-row{grid-template-columns:1fr}
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
                <?php if(isset($_SESSION['user_id']) && $user): 
                    $photo = $_SESSION['user_photo'] ?? 'default.png';
                    $has_photo = ($photo != 'default.png' && file_exists(__DIR__ . '/../assets/uploads/' . $photo));
                ?>
                    <div class="user-avatar" onclick="toggleUserMenu(event)">
                        <?php if($has_photo): ?>
                            <img src="../assets/uploads/<?= htmlspecialchars($photo) ?>" class="avatar-img">
                        <?php else: ?>
                            <div class="avatar-placeholder"><?= strtoupper(substr($user['nom'], 0, 2)) ?></div>
                        <?php endif; ?>
                        <span><?= htmlspecialchars($user['nom']) ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="dropdown-menu" id="userDropdown">
                        <a href="profile.php"><i class="fas fa-user"></i> Mon profil</a>
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

<?php if(isset($_SESSION['user_id']) && $user): ?>
<div class="welcome-banner">
    <i class="fas fa-smile-wink"></i>
    <strong>Bonjour, <?= htmlspecialchars($user['nom']) ?> !</strong> 
    Gérez votre profil ici. 👤
</div>
<?php endif; ?>

<section class="profile-hero"><h1>👤 Mon profil</h1><p>Gérez vos informations personnelles</p></section>

<div class="profile-container">
    <?php if($success): ?><div class="alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if($error): ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    
    <div class="profile-header">
        <?php $has_profile_photo = ($user['photo'] != 'default.png' && file_exists(__DIR__ . '/../assets/uploads/' . $user['photo'])); ?>
        <?php if($has_profile_photo): ?>
            <img src="../assets/uploads/<?= htmlspecialchars($user['photo']) ?>" class="profile-avatar" id="profileAvatar">
        <?php else: ?>
            <div style="width:120px;height:120px;border-radius:50%;background:#e84393;display:flex;align-items:center;justify-content:center;color:white;font-size:48px;font-weight:bold;margin:0 auto 15px"><?= strtoupper(substr($user['nom'], 0, 2)) ?></div>
        <?php endif; ?>
        <div class="profile-name"><?= htmlspecialchars($user['nom']) ?></div>
        <div class="profile-email"><?= htmlspecialchars($user['email']) ?></div>
    </div>
    
    <div class="profile-tabs">
        <button class="tab-btn <?= $active_tab == 'avatar' ? 'active' : '' ?>" data-tab="avatar"><i class="fas fa-camera"></i> Photo</button>
        <button class="tab-btn <?= $active_tab == 'info' ? 'active' : '' ?>" data-tab="info"><i class="fas fa-user-edit"></i> Infos</button>
        <button class="tab-btn <?= $active_tab == 'password' ? 'active' : '' ?>" data-tab="password"><i class="fas fa-lock"></i> Sécurité</button>
    </div>
    
    <div id="tab-avatar" class="panel" style="display: <?= $active_tab == 'avatar' ? 'block' : 'none' ?>;">
        <h3 class="panel-title">Changer ma photo</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="avatar-upload">
                <div class="avatar-preview"></div>
                <label class="upload-btn"><i class="fas fa-upload"></i> Choisir une photo
                    <input type="file" name="photo" accept="image/*" style="display:none;" onchange="previewAvatar(this)">
                </label>
            </div>
            <button type="submit" name="update_avatar" class="btn-save">Enregistrer</button>
        </form>
    </div>
    
    <div id="tab-info" class="panel" style="display: <?= $active_tab == 'info' ? 'block' : 'none' ?>;">
        <h3 class="panel-title">Mes informations</h3>
        <form method="POST">
            <div class="form-row">
                <div class="form-group"><label>Nom</label><input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></div>
            </div>
            <div class="form-group"><label>Téléphone</label><input type="tel" name="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>"></div>
            <button type="submit" name="update_info" class="btn-save">Enregistrer</button>
        </form>
    </div>
    
    <div id="tab-password" class="panel" style="display: <?= $active_tab == 'password' ? 'block' : 'none' ?>;">
        <h3 class="panel-title">Changer mot de passe</h3>
        <form method="POST">
            <div class="form-group"><label>Mot de passe actuel</label><input type="password" name="current_password" required></div>
            <div class="form-row">
                <div class="form-group"><label>Nouveau mot de passe</label><input type="password" name="new_password" minlength="6" required></div>
                <div class="form-group"><label>Confirmer</label><input type="password" name="confirm_password" required></div>
            </div>
            <button type="submit" name="update_password" class="btn-save">Changer</button>
        </form>
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

document.querySelectorAll('.tab-btn').forEach(btn=>{btn.addEventListener('click',()=>{let tab=btn.dataset.tab;document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));btn.classList.add('active');document.getElementById('tab-avatar').style.display=tab==='avatar'?'block':'none';document.getElementById('tab-info').style.display=tab==='info'?'block':'none';document.getElementById('tab-password').style.display=tab==='password'?'block':'none';window.history.replaceState({},'','?tab='+tab);});});

function previewAvatar(input){if(input.files&&input.files[0]){const file=input.files[0];if(file.size>2*1024*1024){alert("L'image ne doit pas dépasser 2MB");input.value='';return;}const reader=new FileReader();reader.onload=function(e){document.getElementById('profileAvatar').src=e.target.result;const navAvatar=document.querySelector('.user-avatar img');if(navAvatar)navAvatar.src=e.target.result;};reader.readAsDataURL(file);}}

function updateCartBadge(){let cart=<?= isset($_SESSION['cart'])?json_encode(array_values($_SESSION['cart'])):'[]' ?>;let c=0;for(let i=0;i<cart.length;i++)c+=cart[i].qty;let b=document.querySelector('.cart-badge');if(b)b.textContent=c;}
function updateWishlistBadge(){let wishlist=<?= json_encode(array_keys($_SESSION['wishlist'])) ?>;let b=document.querySelector('.wishlist-badge');if(b)b.textContent=wishlist.length;}
document.addEventListener('DOMContentLoaded',function(){updateCartBadge();updateWishlistBadge();});
</script>

<?php require_once '../php_files/chatbot_widget.php'; ?>
</body>
</html>
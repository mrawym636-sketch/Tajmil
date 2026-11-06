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
    <title>Avis clientes - Tajmil Lwajh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            padding-top: 80px;
        }

        /* ========== HEADER ========== */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            z-index: 1000;
            padding: 15px 5%;
        }
        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logo {
            font-size: 28px;
            font-weight: 800;
            text-decoration: none;
            color: #1a1a2e;
            letter-spacing: -0.5px;
        }
        .logo span {
            color: #e84393;
        }
        .navbar {
            display: flex;
            gap: 35px;
        }
        .navbar a {
            text-decoration: none;
            color: #4a4a6a;
            font-weight: 500;
            font-size: 15px;
            transition: 0.3s;
        }
        .navbar a:hover,
        .navbar a.active {
            color: #e84393;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 25px;
        }
        .icon-btn {
            position: relative;
            font-size: 22px;
            color: #1a1a2e;
            text-decoration: none;
            transition: 0.3s;
        }
        .icon-btn:hover {
            color: #e84393;
        }
        .badge {
            position: absolute;
            top: -8px;
            right: -12px;
            background: #e84393;
            color: white;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 50%;
            min-width: 18px;
            text-align: center;
        }
        .user-menu {
            position: relative;
            cursor: pointer;
        }
        .user-avatar {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 15px;
            background: #f0f0f5;
            border-radius: 40px;
            transition: 0.3s;
        }
        .user-avatar:hover {
            background: #e8e8f0;
        }
        .dropdown-menu {
            position: absolute;
            top: 55px;
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            min-width: 220px;
            padding: 10px 0;
            opacity: 0;
            visibility: hidden;
            transition: 0.3s;
            z-index: 1001;
        }
        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
        }
        .dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            text-decoration: none;
            color: #4a4a6a;
            font-size: 14px;
            transition: 0.3s;
        }
        .dropdown-menu a:hover {
            background: #f8f9fa;
            color: #e84393;
        }
        .login-btn-header {
            background: #e84393;
            color: white;
            padding: 10px 25px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .menu-icon {
            display: none;
            cursor: pointer;
            font-size: 24px;
            color: #1a1a2e;
        }
        .avatar-img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e84393;
        }
        .avatar-placeholder {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #e84393;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        /* ========== BANDE BIENVENUE ========== */
        .welcome-banner {
            background: linear-gradient(135deg, #e84393, #c2185b);
            color: white;
            padding: 12px 5%;
            text-align: center;
            position: relative;
            z-index: 999;
        }
        .welcome-banner i {
            margin-right: 10px;
        }

        /* ========== HERO ========== */
        .reviews-hero {
            background: linear-gradient(135deg, #1a1a2e, #2d1b36);
            padding: 60px 5%;
            text-align: center;
        }
        .reviews-hero h1 {
            font-size: 44px;
            font-weight: 800;
            color: white;
        }

        /* ========== STATS ========== */
        .rating-summary {
            background: white;
            border-radius: 30px;
            padding: 40px;
            margin: 40px auto;
            max-width: 1200px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            width: 90%;
        }
        .overall-rating .score {
            font-size: 64px;
            font-weight: 800;
            color: #e84393;
        }
        .overall-rating .stars {
            font-size: 28px;
            color: #ffc107;
            margin: 10px 0;
        }
        .overall-rating .count {
            color: #6c757d;
            font-size: 15px;
        }

        /* ========== GRILLE DES AVIS ========== */
        .reviews-grid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 5%;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 30px;
        }
        .review-card {
            background: white;
            border-radius: 24px;
            padding: 25px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border: 1px solid #f0e6ea;
        }
        .review-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(232,67,147,0.1);
            border-color: #e84393;
        }
        .review-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        .review-avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            overflow: hidden;
            background: #e84393;
            flex-shrink: 0;
        }
        .review-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .review-avatar .avatar-initials {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e84393;
            color: white;
            font-weight: bold;
            font-size: 20px;
        }
        .review-info h4 {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 5px;
        }
        .review-stars {
            color: #ffc107;
            font-size: 13px;
            margin: 3px 0;
        }
        .review-date {
            font-size: 11px;
            color: #9aa0a6;
        }
        .review-content p {
            color: #4a4a6a;
            line-height: 1.6;
            margin-top: 12px;
            font-size: 14px;
        }

        /* ========== PAGINATION ========== */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 50px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }
        .pagination a, .pagination span {
            padding: 10px 18px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            background: white;
            color: #1a1a2e;
            border: 1px solid #e8e8f0;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .pagination a:hover {
            background: #e84393;
            color: white;
            border-color: #e84393;
        }
        .pagination .active {
            background: #e84393;
            color: white;
            border-color: #e84393;
        }
        .pagination .disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        /* ========== FORMULAIRE D'AJOUT ========== */
        .add-review {
            background: white;
            border-radius: 30px;
            padding: 40px;
            margin: 40px auto 60px;
            max-width: 800px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            text-align: center;
            width: 90%;
            border: 1px solid #f0e6ea;
        }
        .add-review h3 {
            font-size: 24px;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 10px;
        }
        .add-review p {
            color: #6c757d;
            font-size: 14px;
        }
        .star-rating {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 25px 0;
            font-size: 36px;
            color: #ddd;
            cursor: pointer;
        }
        .star-rating i:hover,
        .star-rating i.active {
            color: #ffc107;
            transform: scale(1.1);
            transition: 0.2s;
        }
        .add-review textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e8e8f0;
            border-radius: 16px;
            font-size: 15px;
            font-family: inherit;
            resize: vertical;
            margin: 20px 0;
            transition: 0.3s;
        }
        .add-review textarea:focus {
            outline: none;
            border-color: #e84393;
        }
        .submit-review {
            background: #e84393;
            color: white;
            border: none;
            padding: 14px 40px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .submit-review:hover {
            background: #c2185b;
            transform: translateY(-2px);
            gap: 15px;
        }

        /* ========== FOOTER ========== */
        .global-footer {
            background: #1a1a2e;
            color: #fff;
            padding: 60px 20px 20px;
            margin-top: 60px;
            border-top: 3px solid #e84393;
        }
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 40px;
        }
        .footer-col h3 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #fff;
        }
        .footer-col h3 span {
            color: #e84393;
        }
        .footer-col h4 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 8px;
        }
        .footer-col h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 35px;
            height: 2px;
            background: #e84393;
        }
        .footer-col p {
            color: #bdc3c7;
            font-size: 14px;
            line-height: 1.6;
        }
        .footer-col ul {
            list-style: none;
        }
        .footer-col ul li {
            margin-bottom: 12px;
        }
        .footer-col ul li a {
            color: #bdc3c7;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .footer-col ul li a:hover {
            color: #e84393;
        }
        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        .social-icons a {
            width: 38px;
            height: 38px;
            background: #333539;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            transition: 0.3s;
            text-decoration: none;
        }
        .social-icons a:hover {
            background: #e84393;
            transform: translateY(-4px);
        }
        .footer-bottom {
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #333539;
        }
        .footer-bottom p {
            color: #9aa0a6;
            font-size: 13px;
        }

        /* ========== TOAST ========== */
        .toast-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #1a1a2e;
            color: white;
            padding: 15px 25px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(450px);
            transition: 0.4s;
            z-index: 9999;
        }
        .toast-notification.show {
            transform: translateX(0);
        }

        /* ========== LOADING ========== */
        .loading-spinner {
            text-align: center;
            padding: 60px;
            grid-column: 1/-1;
        }
        @keyframes spin {
            0% { transform: rotate(0); }
            100% { transform: rotate(360deg); }
        }
        .loading-spinner i {
            font-size: 40px;
            color: #e84393;
            animation: spin 1s linear infinite;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 992px) {
            body { padding-top: 70px; }
            header { padding: 15px 20px; }
            .navbar {
                position: fixed;
                top: 70px;
                left: -280px;
                width: 280px;
                height: calc(100vh - 70px);
                background: white;
                flex-direction: column;
                padding: 30px;
                transition: 0.3s;
                z-index: 999;
                gap: 25px;
            }
            .navbar.show { left: 0; }
            .menu-icon { display: block !important; }
            .footer-container { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .reviews-grid { grid-template-columns: 1fr; }
            .footer-container { grid-template-columns: 1fr; }
            .toast-notification {
                left: 20px;
                right: 20px;
                bottom: 20px;
                transform: translateY(150px);
                justify-content: center;
            }
            .toast-notification.show { transform: translateY(0); }
        }
    </style>
</head>
<body>

<!-- ========== HEADER ========== -->
<header>
    <div class="header-container">
        <div class="header-left">
            <i class="fas fa-bars menu-icon" onclick="toggleMobileMenu(event)"></i>
            <a href="home.php" class="logo">Tajmil<span>.</span></a>
        </div>
        <nav class="navbar" id="mainNavbar">
            <a href="home.php">Accueil</a>
            <a href="products.php">Produits</a>
            <a href="about.php">À propos</a>
            <a href="review.php" class="active">Avis</a>
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

<!-- ========== BANDE BIENVENUE ========== -->
<?php if(isset($_SESSION['user_id']) && $user_info): ?>
<div class="welcome-banner">
    <i class="fas fa-smile-wink"></i>
    <strong>Bonjour, <?= htmlspecialchars($user_info['nom']) ?> !</strong> 
    Partagez votre expérience avec nous. ⭐
</div>
<?php endif; ?>

<!-- ========== HERO ========== -->
<section class="reviews-hero">
    <h1>⭐ Ce que nos clientes pensent</h1>
</section>

<!-- ========== STATS ========== -->
<div class="rating-summary">
    <div class="overall-rating">
        <div class="score" id="avgScore">4.8</div>
        <div class="stars" id="avgStars"></div>
        <div class="count" id="totalCount">Chargement...</div>
    </div>
</div>

<!-- ========== GRILLE DES AVIS ========== -->
<section class="reviews-section">
    <div class="reviews-grid" id="reviewsGrid">
        <div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i><p style="margin-top:15px;">Chargement des avis...</p></div>
    </div>
</section>

<!-- ========== PAGINATION ========== -->
<div class="pagination" id="pagination"></div>

<!-- ========== FORMULAIRE D'AJOUT ========== -->
<?php if(isset($_SESSION['user_id'])): ?>
<div class="add-review">
    <h3>✍️ Donnez votre avis</h3>
    <p>Partagez votre expérience avec nos produits beauté</p>
    <div class="star-rating" id="starRating">
        <i class="far fa-star" data-value="1"></i>
        <i class="far fa-star" data-value="2"></i>
        <i class="far fa-star" data-value="3"></i>
        <i class="far fa-star" data-value="4"></i>
        <i class="far fa-star" data-value="5"></i>
    </div>
    <textarea id="reviewText" placeholder="Votre avis sur le produit..." rows="4"></textarea>
    <button class="submit-review" id="submitReviewBtn"><i class="fas fa-paper-plane"></i> Publier mon avis</button>
</div>
<?php endif; ?>

<!-- ========== FOOTER ========== -->
<footer class="global-footer">
    <div class="footer-container">
        <div class="footer-col about-col">
            <h3>Tajmil<span>.</span></h3>
            <p>Des produits et services de beauté premium pour sublimer votre visage.</p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
                <a href="#"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
        <div class="footer-col">
            <h4>Navigation</h4>
            <ul>
                <li><a href="home.php">Accueil</a></li>
                <li><a href="products.php">Produits</a></li>
                <li><a href="about.php">À propos</a></li>
                <li><a href="review.php">Avis</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Support</h4>
            <ul>
                <li><a href="#">FAQ</a></li>
                <li><a href="#">Livraison</a></li>
                <li><a href="#">Retours</a></li>
                <li><a href="#">CGV</a></li>
            </ul>
        </div>
        <div class="footer-col contact-col">
            <h4>Contact</h4>
            <p><i class="fas fa-map-marker-alt"></i> Casablanca, Maroc</p>
            <p><i class="fas fa-phone-alt"></i> +212 723-699031</p>
            <p><i class="fas fa-envelope"></i> contact@tajmil.ma</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 Tajmil Lwajh — Tous droits réservés.</p>
    </div>
</footer>

<div id="toastNotification" class="toast-notification"></div>

<script>
// ========== FONCTIONS UTILES ==========
function toggleMobileMenu(e) {
    if (e) e.stopPropagation();
    document.getElementById('mainNavbar').classList.toggle('show');
}

function toggleUserMenu(e) {
    e.stopPropagation();
    let d = document.getElementById('userDropdown');
    if (d) d.classList.toggle('show');
}

document.addEventListener('click', function(e) {
    let d = document.getElementById('userDropdown');
    let n = document.getElementById('mainNavbar');
    if (d && !d.contains(e.target)) d.classList.remove('show');
    if (n && !n.contains(e.target)) n.classList.remove('show');
});

function showToast(msg, isError = false) {
    let t = document.getElementById('toastNotification');
    t.innerHTML = (isError ? '<i class="fas fa-exclamation-circle"></i> ' : '<i class="fas fa-check-circle"></i> ') + msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// ========== UPDATE BADGES ==========
function updateCartBadge() {
    let cart = <?= isset($_SESSION['cart']) ? json_encode(array_values($_SESSION['cart'])) : '[]' ?>;
    let c = 0;
    for (let i = 0; i < cart.length; i++) c += cart[i].qty;
    let b = document.querySelector('.cart-badge');
    if (b) b.textContent = c;
}

function updateWishlistBadge() {
    let wishlist = <?= isset($_SESSION['wishlist']) ? json_encode(array_keys($_SESSION['wishlist'])) : '[]' ?>;
    let b = document.querySelector('.wishlist-badge');
    if (b) b.textContent = wishlist.length;
}

// ========== PAGINATION ==========
let currentPage = 1;
let allReviews = [];
let itemsPerPage = 6;

function displayReviews(page) {
    const start = (page - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageReviews = allReviews.slice(start, end);
    
    let reviewsHtml = '';
    if (pageReviews.length === 0 && allReviews.length === 0) {
        reviewsHtml = '<div style="text-align:center;padding:60px;grid-column:1/-1;"><i class="far fa-smile" style="font-size:50px;color:#e84393;"></i><h3 style="margin-top:15px;">Soyez la première à donner votre avis !</h3></div>';
    } else {
        pageReviews.forEach(review => {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += i <= review.note ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
            }
            let userPhoto = review.photo || 'default.png';
            let initials = (review.nom || 'U').substring(0, 2).toUpperCase();
            
            reviewsHtml += `
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-avatar">
                            ${userPhoto !== 'default.png' ? 
                                `<img src="../assets/uploads/${escapeHtml(userPhoto)}" alt="Photo">` : 
                                `<div class="avatar-initials">${escapeHtml(initials)}</div>`
                            }
                        </div>
                        <div class="review-info">
                            <h4>${escapeHtml(review.nom)}</h4>
                            <div class="review-stars">${stars}</div>
                            <div class="review-date">${review.date_creation || ''}</div>
                        </div>
                    </div>
                    <div class="review-content">
                        <p>${escapeHtml(review.commentaire)}</p>
                    </div>
                </div>
            `;
        });
    }
    document.getElementById('reviewsGrid').innerHTML = reviewsHtml;
}

function setupPagination() {
    const totalPages = Math.ceil(allReviews.length / itemsPerPage);
    const paginationContainer = document.getElementById('pagination');
    
    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    let paginationHtml = '';
    
    // Previous button
    paginationHtml += `<a class="${currentPage === 1 ? 'disabled' : ''}" onclick="changePage(${currentPage - 1})"><i class="fas fa-chevron-left"></i></a>`;
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            paginationHtml += `<a class="${currentPage === i ? 'active' : ''}" onclick="changePage(${i})">${i}</a>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            paginationHtml += `<span>...</span>`;
        }
    }
    
    // Next button
    paginationHtml += `<a class="${currentPage === totalPages ? 'disabled' : ''}" onclick="changePage(${currentPage + 1})"><i class="fas fa-chevron-right"></i></a>`;
    
    paginationContainer.innerHTML = paginationHtml;
}

function changePage(page) {
    const totalPages = Math.ceil(allReviews.length / itemsPerPage);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    displayReviews(currentPage);
    setupPagination();
    window.scrollTo({ top: document.querySelector('.reviews-section').offsetTop - 100, behavior: 'smooth' });
}

// ========== CHARGEMENT DES AVIS ==========
function loadReviews() {
    fetch('../php_files/get_reviews.php')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Mise à jour des stats
                document.getElementById('avgScore').textContent = data.moyenne;
                document.getElementById('totalCount').textContent = 'Basé sur ' + data.total + ' avis';
                
                let avgStars = '';
                let fullStars = Math.floor(data.moyenne);
                let hasHalf = (data.moyenne - fullStars) >= 0.5;
                for (let i = 1; i <= 5; i++) {
                    if (i <= fullStars) avgStars += '<i class="fas fa-star"></i>';
                    else if (i === fullStars + 1 && hasHalf) avgStars += '<i class="fas fa-star-half-alt"></i>';
                    else avgStars += '<i class="far fa-star"></i>';
                }
                document.getElementById('avgStars').innerHTML = avgStars;
                
                // Sauvegarde des avis
                allReviews = data.reviews;
                
                // Affichage des avis avec pagination
                currentPage = 1;
                displayReviews(currentPage);
                setupPagination();
            }
        })
        .catch(e => console.error(e));
}

// ========== GESTION DU FORMULAIRE D'AVIS ==========
let selectedRating = 0;
const starsElements = document.querySelectorAll('#starRating i');

if (starsElements.length) {
    starsElements.forEach(star => {
        star.addEventListener('click', function() {
            selectedRating = parseInt(this.dataset.value);
            starsElements.forEach((s, i) => {
                if (i < selectedRating) {
                    s.className = 'fas fa-star active';
                } else {
                    s.className = 'far fa-star';
                }
            });
        });
    });
}

const submitBtn = document.getElementById('submitReviewBtn');
if (submitBtn) {
    submitBtn.addEventListener('click', function() {
        if (selectedRating === 0) {
            showToast('Veuillez sélectionner une note', true);
            return;
        }
        let text = document.getElementById('reviewText').value;
        if (!text.trim()) {
            showToast('Veuillez écrire votre avis', true);
            return;
        }
        fetch('../php_files/add_review.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ note: selectedRating, commentaire: text })
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                showToast('✓ Merci pour votre avis !');
                document.getElementById('reviewText').value = '';
                selectedRating = 0;
                starsElements.forEach(s => { s.className = 'far fa-star'; });
                loadReviews();
            } else {
                showToast(d.message || 'Erreur', true);
            }
        })
        .catch(e => showToast('Erreur lors de l\'envoi', true));
    });
}

// ========== INITIALISATION ==========
document.addEventListener('DOMContentLoaded', function() {
    updateCartBadge();
    updateWishlistBadge();
    loadReviews();
});
</script>

<?php require_once '../php_files/chatbot_widget.php'; ?>
</body>
</html>
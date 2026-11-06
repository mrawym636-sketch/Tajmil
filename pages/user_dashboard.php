<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../php_files/config.php';

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$cart_count = getCartCount();
$wishlist_count = getWishlistCount();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace - Tajmil Lwajh</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#f8f9fa}
        
        .navbar{
            background:linear-gradient(135deg, #1a1a2e 0%, #2d1b36 100%);
            color:white;
            padding:15px 30px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            box-shadow:0 2px 15px rgba(0,0,0,0.1);
        }
        .logo{
            font-size:28px;
            font-weight:800;
            color:white;
            text-decoration:none;
            letter-spacing:-0.5px;
        }
        .logo span{color:#e84393;}
        .user-info{
            display:flex;
            align-items:center;
            gap:20px;
        }
        .user-info img{
            width:45px;
            height:45px;
            border-radius:50%;
            object-fit:cover;
            border:2px solid #e84393;
        }
        .user-info span{
            font-weight:500;
            font-size:15px;
        }
        .logout-btn{
            background:#e84393;
            color:white;
            padding:8px 20px;
            border-radius:40px;
            text-decoration:none;
            transition:0.3s;
            display:flex;
            align-items:center;
            gap:8px;
            font-size:14px;
            font-weight:600;
        }
        .logout-btn:hover{
            background:#c2185b;
            transform:translateY(-2px);
        }
        .btn-back{
            color:white;
            text-decoration:none;
            margin-right:15px;
            background:rgba(255,255,255,0.15);
            padding:8px 18px;
            border-radius:40px;
            transition:0.3s;
            display:flex;
            align-items:center;
            gap:8px;
            font-size:14px;
        }
        .btn-back:hover{
            background:rgba(255,255,255,0.3);
        }
        .container{
            max-width:1200px;
            margin:40px auto;
            padding:0 20px;
        }
        .welcome-card{
            background:linear-gradient(135deg, #e84393, #c2185b);
            color:white;
            padding:35px;
            border-radius:25px;
            margin-bottom:40px;
            box-shadow:0 10px 30px rgba(232,67,147,0.2);
        }
        .welcome-card h1{
            font-size:32px;
            font-weight:800;
            margin-bottom:10px;
        }
        .welcome-card p{
            font-size:16px;
            opacity:0.9;
        }
        .stats-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));
            gap:25px;
            margin-bottom:40px;
        }
        .stat-card{
            background:white;
            padding:30px;
            border-radius:20px;
            text-align:center;
            box-shadow:0 5px 20px rgba(0,0,0,0.05);
            text-decoration:none;
            color:#1a1a2e;
            display:block;
            transition:all 0.3s ease;
            border:1px solid #f0e6ea;
        }
        .stat-card:hover{
            transform:translateY(-8px);
            border-color:#e84393;
            box-shadow:0 20px 40px rgba(232,67,147,0.15);
        }
        .stat-card i{
            font-size:50px;
            color:#e84393;
            margin-bottom:20px;
        }
        .stat-card h3{
            font-size:20px;
            font-weight:700;
            margin-bottom:10px;
        }
        .stat-card p{
            color:#6c757d;
            font-size:14px;
        }
        .quick-actions{
            background:white;
            border-radius:20px;
            padding:25px;
            border:1px solid #f0e6ea;
        }
        .quick-actions h3{
            font-size:18px;
            font-weight:700;
            margin-bottom:20px;
            color:#1a1a2e;
            display:flex;
            align-items:center;
            gap:10px;
        }
        .actions-buttons{
            display:flex;
            gap:15px;
            flex-wrap:wrap;
        }
        .action-btn{
            background:#f8f9fa;
            padding:12px 25px;
            border-radius:50px;
            text-decoration:none;
            color:#4a4a6a;
            font-weight:500;
            font-size:14px;
            transition:0.3s;
            display:inline-flex;
            align-items:center;
            gap:8px;
            border:1px solid #e8e8f0;
        }
        .action-btn:hover{
            background:#e84393;
            color:white;
            border-color:#e84393;
            transform:translateY(-2px);
        }
        
        @media (max-width:768px){
            .navbar{
                flex-direction:column;
                gap:15px;
                padding:15px;
            }
            .user-info{
                flex-wrap:wrap;
                justify-content:center;
            }
            .welcome-card h1{
                font-size:24px;
            }
            .stats-grid{
                grid-template-columns:1fr;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="home.php" class="logo">Tajmil<span>.</span></a>
        <div class="user-info">
            <a href="home.php" class="btn-back"><i class="fas fa-arrow-left"></i> Accueil</a>
            <img src="../assets/uploads/<?= htmlspecialchars($user['photo'] ?? 'default.png') ?>" alt="Photo">
            <span><?= htmlspecialchars($user['nom']) ?></span>
            <a href="../php_files/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome-card">
            <h1>Bienvenue, <?= htmlspecialchars($user['nom']) ?> !</h1>
            <p>Bienvenue dans votre espace personnel Tajmil Lwajh. Gérez vos commandes, favoris et informations.</p>
        </div>
        
        <div class="stats-grid">
            <a href="orders.php" class="stat-card">
                <i class="fas fa-shopping-bag"></i>
                <h3>Mes commandes</h3>
                <p>Consultez l'historique de vos achats</p>
            </a>
            <a href="wishlist.php" class="stat-card">
                <i class="fas fa-heart"></i>
                <h3>Mes favoris</h3>
                <p>Retrouvez vos produits préférés</p>
            </a>
            <a href="profile.php" class="stat-card">
                <i class="fas fa-user-edit"></i>
                <h3>Mon profil</h3>
                <p>Modifiez vos informations personnelles</p>
            </a>
        </div>
        
        <div class="quick-actions">
            <h3><i class="fas fa-bolt" style="color:#e84393;"></i> Actions rapides</h3>
            <div class="actions-buttons">
                <a href="products.php" class="action-btn"><i class="fas fa-spa"></i> Découvrir nos produits</a>
                <a href="contact.php" class="action-btn"><i class="fas fa-headset"></i> Contacter le support</a>
                <a href="review.php" class="action-btn"><i class="fas fa-star"></i> Donner mon avis</a>
            </div>
        </div>
    </div>

<?php require_once '../php_files/chatbot_widget.php'; ?>
</body>
</html>
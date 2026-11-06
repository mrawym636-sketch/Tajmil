<?php
$host = 'localhost';
$dbname = 'khalid';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion: " . $e->getMessage());
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];

function getCartCount() {
    $count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['qty'];
        }
    }
    return $count;
}

function getWishlistCount() {
    return isset($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;
}

function getProductById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllProducts($pdo) {
    $stmt = $pdo->query("SELECT * FROM produits");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ✅ Fonction pour afficher l'avatar (photo ou initiales)
function getUserAvatarHTML($user, $size = 35) {
    $photo = $user['photo'] ?? 'default.png';
    $nom = $user['nom'] ?? 'Utilisateur';
    $photo_path = __DIR__ . '/../assets/uploads/' . $photo;
    
    if ($photo != 'default.png' && file_exists($photo_path)) {
        return '<img src="../assets/uploads/'.htmlspecialchars($photo).'" class="avatar-img" style="width:'.$size.'px;height:'.$size.'px;border-radius:50%;object-fit:cover;border:2px solid #e84393;">';
    } else {
        $initials = strtoupper(substr($nom, 0, 2));
        return '<div class="avatar-placeholder" style="width:'.$size.'px;height:'.$size.'px;border-radius:50%;background:#e84393;display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:'.($size/2).'px;">'.$initials.'</div>';
    }
}
?>
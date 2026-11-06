<?php
require_once 'config.php';

// التأكد من أن السلة ليست فارغة
if (empty($_SESSION['cart'])) {
    header('Location: ../pages/cart.php');
    exit();
}

// حساب المجموع الكلي للمنتجات
$items = [];
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $item_total = $item['prix'] * $item['qty'];
    $subtotal += $item_total;
    $items[] = [
        'id' => $item['id'],
        'nom' => $item['nom'],
        'qty' => $item['qty'],
        'prix' => $item['prix'],
        'subtotal' => $item_total
    ];
}

// جلب رسوم التوصيل من قاعدة البيانات (من صفحة الإعدادات)
$stmt = $pdo->query("SELECT delivery_fee FROM settings WHERE id=1");
$delivery_fee = (float)($stmt->fetchColumn() ?? 0);
$total = $subtotal + $delivery_fee;

// معالجة النموذج عند الإرسال
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    
    if (!empty($nom) && !empty($email) && !empty($telephone) && !empty($items)) {
        $numero = 'CMD-' . date('Ymd') . '-' . rand(1000, 9999);
        $produits_json = json_encode($items, JSON_UNESCAPED_UNICODE);
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        try {
            $stmt = $pdo->prepare("INSERT INTO commandes (numero, user_id, nom_client, email_client, telephone_client, total, produits, statut) VALUES (?, ?, ?, ?, ?, ?, ?, 'En attente')");
            $stmt->execute([$numero, $user_id, $nom, $email, $telephone, $total, $produits_json]);
            
            // تفريغ السلة بعد نجاح الطلب
            $_SESSION['cart'] = [];
            
            echo "<script>
                alert('✅ Commande validée !\\nNuméro: $numero\\nTotal: " . number_format($total, 2) . " DH');
                window.location='../pages/orders.php?success=1';
            </script>";
            exit();
        } catch(PDOException $e) {
            echo "<script>
                alert('❌ Erreur: " . addslashes($e->getMessage()) . "');
                window.location='../pages/cart.php';
            </script>";
            exit();
        }
    } else {
        echo "<script>
            alert('❌ Veuillez remplir tous les champs');
            window.location='../pages/cart.php';
        </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Validation commande - Parfumerie Elegance</title>
    <link rel="stylesheet" href="../assets/css/site.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:Arial,sans-serif;background:#f5f5f5;padding:40px 20px}
        .container{max-width:600px;margin:0 auto}
        .card{background:white;border-radius:20px;box-shadow:0 10px 30px rgba(0,0,0,0.1);padding:30px}
        h2{color:#1a1a2e;margin-bottom:20px;text-align:center}
        .recap{background:#f8f8f8;padding:15px;border-radius:15px;margin-bottom:20px}
        .recap h3{margin-bottom:10px;color:#555;font-size:16px}
        .recap-item{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #eee}
        .recap-total{text-align:right;font-size:1.3rem;font-weight:bold;color:#e84393;margin-top:10px;padding-top:10px;border-top:2px solid #ddd}
        .form-group{margin-bottom:15px}
        .form-group input{width:100%;padding:12px;border:1px solid #ddd;border-radius:10px;font-size:1rem}
        .btn-submit{width:100%;padding:14px;background:#e84393;color:white;border:none;border-radius:10px;font-size:1.1rem;cursor:pointer;transition:0.3s}
        .btn-submit:hover{background:#c2185b}
        .btn-back{display:inline-block;margin-top:15px;text-align:center;width:100%;padding:12px;background:#333;color:white;text-decoration:none;border-radius:10px}
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h2>📋 Finaliser la commande</h2>
        
        <div class="recap">
            <h3>🛒 Récapitulatif de votre commande</h3>
            <?php foreach($items as $item): ?>
                <div class="recap-item">
                    <span><?= htmlspecialchars($item['nom']) ?> x <?= $item['qty'] ?></span>
                    <span><?= number_format($item['subtotal'], 2) ?> DH</span>
                </div>
            <?php endforeach; ?>
            <?php if($delivery_fee > 0): ?>
            <div class="recap-item">
                <span>Livraison</span>
                <span><?= number_format($delivery_fee, 2) ?> DH</span>
            </div>
            <?php endif; ?>
            <div class="recap-total">
                Total: <?= number_format($total, 2) ?> DH
            </div>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <input type="text" name="nom" placeholder="Nom complet" required value="<?= isset($_SESSION['user_nom']) ? htmlspecialchars($_SESSION['user_nom']) : '' ?>">
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required value="<?= isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : '' ?>">
            </div>
            <div class="form-group">
                <input type="tel" name="telephone" placeholder="Téléphone" required>
            </div>
            <button type="submit" class="btn-submit">✅ Confirmer la commande</button>
            <a href="../pages/cart.php" class="btn-back">← Retour au panier</a>
        </form>
    </div>
</div>
</body>
</html>
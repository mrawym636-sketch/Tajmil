<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once '../php_files/config.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$cart_count = getCartCount();
$wishlist_count = getWishlistCount();

$success = '';
$error = '';

// معالجة تحديث المعلومات + الصورة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    
    // معالجة الصورة
    $photo = $user['photo'] ?? 'default.png';
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../assets/uploads/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($ext, $allowed)) {
            $photo = 'user_' . $user_id . '_' . time() . '.' . $ext;
            $upload_path = $upload_dir . $photo;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                if ($user['photo'] != 'default.png' && file_exists($upload_dir . $user['photo'])) {
                    unlink($upload_dir . $user['photo']);
                }
            } else {
                $error = "Erreur lors de l'upload de l'image";
            }
        } else {
            $error = "Format non autorisé (JPG, PNG, GIF, WEBP)";
        }
    }
    
    if (empty($error)) {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET nom=?, email=?, telephone=?, photo=? WHERE id=?");
        if ($stmt->execute([$nom, $email, $telephone, $photo, $user_id])) {
            $_SESSION['user_nom'] = $nom;
            $_SESSION['user_photo'] = $photo;
            $success = "Profil mis à jour avec succès !";
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        } else {
            $error = "Erreur lors de la mise à jour";
        }
    }
}

// معالجة تغيير كلمة المرور
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    
    $stmt = $pdo->prepare("SELECT password FROM utilisateurs WHERE id = ?");
    $stmt->execute([$user_id]);
    $current_hash = $stmt->fetchColumn();
    
    if (!password_verify($current, $current_hash)) {
        $error = "Mot de passe actuel incorrect";
    } elseif (strlen($new) < 6) {
        $error = "Le nouveau mot de passe doit contenir au moins 6 caractères";
    } elseif ($new !== $confirm) {
        $error = "Les mots de passe ne correspondent pas";
    } else {
        $new_hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE utilisateurs SET password = ? WHERE id = ?");
        $stmt->execute([$new_hash, $user_id]);
        $success = "Mot de passe modifié avec succès !";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon profil - Tajmil Lwajh</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#f5f5f5}
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
        }
        .logo span{color:#e84393;}
        .container{
            max-width:800px;
            margin:40px auto;
            padding:0 20px;
        }
        .card{
            background:white;
            border-radius:25px;
            padding:35px;
            margin-bottom:30px;
            box-shadow:0 10px 30px rgba(0,0,0,0.05);
            border:1px solid #f0e6ea;
        }
        .card h2{
            color:#1a1a2e;
            margin-bottom:25px;
            padding-bottom:12px;
            border-bottom:3px solid #e84393;
            display:inline-block;
            font-size:22px;
        }
        .form-group{
            margin-bottom:20px;
        }
        .form-group label{
            display:block;
            margin-bottom:8px;
            font-weight:600;
            color:#333;
            font-size:14px;
        }
        .form-group input{
            width:100%;
            padding:14px;
            border:2px solid #e8e8f0;
            border-radius:12px;
            font-size:14px;
            transition:0.3s;
            font-family:inherit;
        }
        .form-group input:focus{
            outline:none;
            border-color:#e84393;
        }
        .photo-preview{
            display:flex;
            align-items:center;
            gap:25px;
            margin-bottom:25px;
            flex-wrap:wrap;
        }
        .photo-preview img{
            width:100px;
            height:100px;
            border-radius:50%;
            object-fit:cover;
            border:3px solid #e84393;
            box-shadow:0 5px 15px rgba(0,0,0,0.1);
        }
        .photo-upload{
            flex:1;
        }
        .photo-upload small{
            color:#6c757d;
            display:block;
            margin-top:8px;
            font-size:12px;
        }
        .btn-submit{
            background:#e84393;
            color:white;
            padding:14px 30px;
            border:none;
            border-radius:50px;
            cursor:pointer;
            font-weight:700;
            font-size:15px;
            transition:0.3s;
            display:inline-flex;
            align-items:center;
            gap:10px;
        }
        .btn-submit:hover{
            background:#c2185b;
            transform:translateY(-2px);
        }
        .btn-back{
            display:inline-block;
            margin-top:20px;
            color:#e84393;
            text-decoration:none;
            font-weight:600;
            display:flex;
            align-items:center;
            gap:8px;
        }
        .btn-back:hover{
            gap:12px;
        }
        .success{
            background:#d4edda;
            color:#155724;
            padding:15px;
            border-radius:12px;
            margin-bottom:25px;
            display:flex;
            align-items:center;
            gap:10px;
            font-weight:500;
        }
        .error{
            background:#f8d7da;
            color:#721c24;
            padding:15px;
            border-radius:12px;
            margin-bottom:25px;
            display:flex;
            align-items:center;
            gap:10px;
            font-weight:500;
        }
        @media (max-width:768px){
            .navbar{
                flex-direction:column;
                gap:15px;
            }
            .card{
                padding:25px;
            }
            .photo-preview{
                flex-direction:column;
                text-align:center;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="home.php" class="logo">Tajmil<span>.</span></a>
        <a href="user_dashboard.php" style="color:white;text-decoration:none;background:rgba(255,255,255,0.15);padding:8px 20px;border-radius:40px;"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>
    
    <div class="container">
        <?php if($success): ?>
            <div class="success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h2><i class="fas fa-user" style="color:#e84393; margin-right:10px;"></i> Mes informations</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="photo-preview">
                    <img src="../assets/uploads/<?= htmlspecialchars($user['photo'] ?? 'default.png') ?>" id="preview" alt="Photo">
                    <div class="photo-upload">
                        <input type="file" name="photo" accept="image/*" onchange="previewImage(this)" style="margin-bottom:8px;">
                        <small>Formats acceptés : JPG, PNG, GIF, WEBP (max 2MB)</small>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nom complet</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>" placeholder="+212 6XX XXX XXX">
                </div>
                <button type="submit" name="update_profile" class="btn-submit"><i class="fas fa-save"></i> Mettre à jour</button>
            </form>
        </div>
        
        <div class="card">
            <h2><i class="fas fa-lock" style="color:#e84393; margin-right:10px;"></i> Changer le mot de passe</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Mot de passe actuel</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label>Nouveau mot de passe</label>
                    <input type="password" name="new_password" minlength="6" required>
                    <small style="color:#6c757d;">Minimum 6 caractères</small>
                </div>
                <div class="form-group">
                    <label>Confirmer le nouveau mot de passe</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" name="update_password" class="btn-submit"><i class="fas fa-key"></i> Changer le mot de passe</button>
            </form>
        </div>
        
        <a href="user_dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour à mon espace</a>
    </div>
    
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 2 * 1024 * 1024) {
                    alert("L'image ne doit pas dépasser 2MB");
                    input.value = '';
                    return;
                }
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert("Format non autorisé. Utilisez JPG, PNG, GIF ou WEBP");
                    input.value = '';
                    return;
                }
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>

<?php require_once '../php_files/chatbot_widget.php'; ?>
</body>
</html>
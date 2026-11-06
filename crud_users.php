<?php
$active_page='crud_users'; $page_title='Gestion des utilisateurs';
require_once __DIR__ . '/config.php';

// Ajouter un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $telephone = trim($_POST['telephone']);
    $role = $_POST['role'];
    
    $photo = 'default.png';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../assets/uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $photo = 'user_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo);
        }
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, password, telephone, role, photo) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $email, $password, $telephone, $role, $photo]);
        logAction($pdo, 'Ajout utilisateur', "Nom: $nom");
        header('Location: crud_users.php?added=1'); exit();
    } catch(PDOException $e) {
        $error = "Email déjà utilisé";
    }
}

// Modifier un utilisateur
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);
    $edit_user = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $id = (int)$_POST['user_id'];
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $role = $_POST['role'];
    
    $photo = $_POST['existing_photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../assets/uploads/';
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            // Supprimer l'ancienne photo si ce n'est pas default.png
            if ($photo != 'default.png' && file_exists($upload_dir . $photo)) {
                unlink($upload_dir . $photo);
            }
            $photo = 'user_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo);
        }
    }
    
    $stmt = $pdo->prepare("UPDATE utilisateurs SET nom=?, email=?, telephone=?, role=?, photo=? WHERE id=?");
    $stmt->execute([$nom, $email, $telephone, $role, $photo, $id]);
    logAction($pdo, 'Modification utilisateur', "ID $id");
    header('Location: crud_users.php?updated=1'); exit();
}

// Supprimer un utilisateur
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT photo FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);
    $photo = $stmt->fetchColumn();
    if ($photo && $photo != 'default.png') {
        $upload_dir = __DIR__ . '/../assets/uploads/';
        if (file_exists($upload_dir . $photo)) unlink($upload_dir . $photo);
    }
    $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?")->execute([$id]);
    logAction($pdo, 'Suppression utilisateur', "ID $id");
    header('Location: crud_users.php?deleted=1'); exit();
}

// Recherche
$search = $_GET['search'] ?? '';
if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE nom LIKE ? OR email LIKE ? OR telephone LIKE ? ORDER BY id DESC");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
    $users = $stmt->fetchAll();
} else {
    $users = $pdo->query("SELECT * FROM utilisateurs ORDER BY id DESC")->fetchAll();
}

require_once 'header.php';
?>

<style>
    .avatar-img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid var(--em); background: var(--surface2); }
    .avatar-placeholder { width: 45px; height: 45px; border-radius: 50%; background: var(--em-bg); border: 2px solid var(--em-border); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; color: var(--em); }
    .user-info { display: flex; align-items: center; gap: 12px; }
    .user-name { font-weight: 600; color: var(--text); }
    .user-role-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .role-admin { background: rgba(16, 185, 129, 0.1); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.2); }
    .role-user { background: rgba(59, 130, 246, 0.1); color: #93c5fd; border: 1px solid rgba(59, 130, 246, 0.2); }
    .search-bar { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
    .search-bar input { flex: 1; padding: 10px 15px; background: var(--surface2); border: 1px solid var(--border2); border-radius: 10px; color: var(--text); }
    .search-bar button { padding: 10px 20px; }
</style>

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="fas fa-users"></i> Gestion des utilisateurs <span style="font-size:12px; color:var(--muted); margin-left:8px;">(<?= count($users) ?> total)</span></div>
        <button class="btn btn-em btn-sm" onclick="document.getElementById('addModal').style.display='flex'"><i class="fas fa-plus"></i> Nouvel utilisateur</button>
    </div>
    
    <div class="panel-body">
        <form method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Rechercher par nom, email ou téléphone..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-em"><i class="fas fa-search"></i> Rechercher</button>
            <?php if(!empty($search)): ?>
                <a href="crud_users.php" class="btn btn-ghost"><i class="fas fa-times"></i> Réinitialiser</a>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="tbl-wrap">
        <table class="dtable">
            <thead><tr><th>Photo</th><th>Nom</th><th>Email</th><th>Téléphone</th><th>Rôle</th><th>Inscription</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach($users as $u): 
                    $photo = $u['photo'] ?? 'default.png';
                    $has_photo = file_exists(__DIR__ . '/../assets/uploads/' . $photo) && $photo != 'default.png';
                    $initials = strtoupper(substr($u['nom'], 0, 2));
                ?>
                <tr>
                    <td><div class="user-info"><?php if($has_photo): ?><img src="../assets/uploads/<?= htmlspecialchars($photo) ?>" class="avatar-img"><?php else: ?><div class="avatar-placeholder"><?= $initials ?></div><?php endif; ?></div></td>
                    <td><div class="user-name"><?= htmlspecialchars($u['nom']) ?></div><div style="font-size:11px; color:var(--muted);">ID: #<?= $u['id'] ?></div></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['telephone'] ?? '—') ?></td>
                    <td><span class="user-role-badge <?= $u['role'] == 'admin' ? 'role-admin' : 'role-user' ?>"><i class="fas <?= $u['role'] == 'admin' ? 'fa-shield-alt' : 'fa-user' ?>"></i> <?= $u['role'] == 'admin' ? 'Admin' : 'Utilisateur' ?></span></td>
                    <td style="font-size:12px;"><?= date('d/m/Y', strtotime($u['date_inscription'] ?? 'now')) ?></td>
                    <td class="actions" style="display:flex; gap:6px; flex-wrap:wrap;">
                        <a href="?edit=<?= $u['id'] ?>" class="btn btn-ghost btn-xs"><i class="fas fa-pen"></i> Modifier</a>
                        <a href="?delete=<?= $u['id'] ?>" class="btn btn-danger btn-xs" onclick="return confirm('Supprimer cet utilisateur ?')"><i class="fas fa-trash"></i> Supprimer</a>
                        <?php if(!empty($u['telephone'])): ?>
                        <a href="blacklist.php?add=<?= urlencode($u['telephone']) ?>" class="btn btn-danger btn-xs" onclick="return confirm('Bloquer ce numéro ?')"><i class="fas fa-ban"></i> Bloquer</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($users)): ?>
                <tr><td colspan="7" style="text-align:center; padding:40px;"><i class="fas fa-user-slash" style="font-size:40px; color:var(--muted); margin-bottom:10px; display:block;"></i>Aucun utilisateur trouvé</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajout -->
<div id="addModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:var(--surface); padding:25px; border-radius:16px; width:500px; max-width:90%;">
        <h3 style="margin-bottom:20px;"><i class="fas fa-user-plus"></i> Ajouter un utilisateur</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="fg"><label>Nom complet *</label><input type="text" name="nom" required></div>
            <div class="fg"><label>Email *</label><input type="email" name="email" required></div>
            <div class="fg"><label>Mot de passe *</label><input type="password" name="password" minlength="6" required></div>
            <div class="fg"><label>Téléphone</label><input type="text" name="telephone" placeholder="+212XXXXXXXXX"></div>
            <div class="fg"><label>Rôle</label><select name="role"><option value="user">Utilisateur</option><option value="admin">Administrateur</option></select></div>
            <div class="fg"><label>Photo de profil</label><input type="file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp"><small style="color:var(--muted); display:block; margin-top:5px;">Formats: JPG, PNG, GIF, WEBP (max 2MB)</small></div>
            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="submit" name="add_user" class="btn btn-em">Ajouter</button>
                <button type="button" onclick="document.getElementById('addModal').style.display='none'" class="btn btn-ghost">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Modification -->
<?php if(isset($edit_user)): ?>
<div id="editModal" style="display:flex; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:var(--surface); padding:25px; border-radius:16px; width:500px; max-width:90%;">
        <h3 style="margin-bottom:20px;"><i class="fas fa-user-edit"></i> Modifier <?= htmlspecialchars($edit_user['nom']) ?></h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?= $edit_user['id'] ?>">
            <input type="hidden" name="existing_photo" value="<?= $edit_user['photo'] ?? 'default.png' ?>">
            <div style="text-align:center; margin-bottom:15px;">
                <?php 
                $current_photo = $edit_user['photo'] ?? 'default.png';
                $has_current = file_exists(__DIR__ . '/../assets/uploads/' . $current_photo) && $current_photo != 'default.png';
                ?>
                <?php if($has_current): ?>
                    <img src="../assets/uploads/<?= htmlspecialchars($current_photo) ?>" style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:2px solid var(--em);">
                <?php else: ?>
                    <div style="width:80px; height:80px; border-radius:50%; background:var(--em-bg); border:2px solid var(--em-border); display:flex; align-items:center; justify-content:center; margin:0 auto; font-size:28px; font-weight:700; color:var(--em);"><?= strtoupper(substr($edit_user['nom'], 0, 2)) ?></div>
                <?php endif; ?>
            </div>
            <div class="fg"><label>Nom complet</label><input type="text" name="nom" value="<?= htmlspecialchars($edit_user['nom']) ?>" required></div>
            <div class="fg"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($edit_user['email']) ?>" required></div>
            <div class="fg"><label>Téléphone</label><input type="text" name="telephone" value="<?= htmlspecialchars($edit_user['telephone'] ?? '') ?>"></div>
            <div class="fg"><label>Rôle</label><select name="role"><option value="user" <?= $edit_user['role'] == 'user' ? 'selected' : '' ?>>Utilisateur</option><option value="admin" <?= $edit_user['role'] == 'admin' ? 'selected' : '' ?>>Administrateur</option></select></div>
            <div class="fg"><label>Nouvelle photo</label><input type="file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp"><small style="color:var(--muted); display:block; margin-top:5px;">Laissez vide pour garder la photo actuelle</small></div>
            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="submit" name="edit_user" class="btn btn-em">Enregistrer</button>
                <a href="crud_users.php" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>
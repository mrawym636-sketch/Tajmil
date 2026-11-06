<?php
$active_page='users'; $page_title='Blacklist';
require_once __DIR__ . '/config.php';

if(isset($_POST['add_phone']) && !empty($_POST['telephone'])){
    $telephone = trim($_POST['telephone']);
    $motif = $_POST['motif'] ?? '';
    $pdo->prepare("INSERT IGNORE INTO blacklist (telephone, motif) VALUES (?, ?)")->execute([$telephone, $motif]);
    header('Location: blacklist.php'); exit();
}

if(isset($_GET['add'])){
    $telephone = $_GET['add'];
    $pdo->prepare("INSERT IGNORE INTO blacklist (telephone) VALUES (?)")->execute([$telephone]);
    header('Location: blacklist.php'); exit();
}

if(isset($_GET['delete'])){
    $pdo->prepare("DELETE FROM blacklist WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: blacklist.php'); exit();
}

$blacklist = $pdo->query("SELECT * FROM blacklist ORDER BY id DESC")->fetchAll();
require_once 'header.php';
?>
<div class="panel">
    <div class="panel-head"><div class="panel-title"><i class="fas fa-ban"></i> Numéros bloqués (<?= count($blacklist) ?>)</div></div>
    <div class="tbl-wrap">
        <table class="dtable">
            <thead><tr><th>ID</th><th>Téléphone</th><th>Motif</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach($blacklist as $b): ?>
                <tr><td style="color:var(--muted)">#<?= $b['id'] ?></td><td><?= htmlspecialchars($b['telephone']) ?></td><td><?= htmlspecialchars($b['motif'] ?? '—') ?></td><td><?= $b['date_ajout'] ?></td><td><a href="?delete=<?= $b['id'] ?>" class="btn btn-success btn-xs" onclick="return confirm('Débloquer ce numéro ?')"><i class="fas fa-unlock"></i> Débloquer</a></td></tr>
                <?php endforeach; ?>
                <?php if(empty($blacklist)): ?>
                <tr><td colspan="5" style="text-align:center">Aucun numéro bloqué</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="panel-body">
        <h4>Ajouter un numéro manuellement</h4>
        <form method="POST" style="display:flex; gap:10px; flex-wrap:wrap;">
            <input type="text" name="telephone" placeholder="+212XXXXXXXXX" required style="flex:2; padding:8px;">
            <input type="text" name="motif" placeholder="Motif (optionnel)" style="flex:3; padding:8px;">
            <button type="submit" name="add_phone" class="btn btn-danger"><i class="fas fa-ban"></i> Bloquer</button>
        </form>
    </div>
</div>
<?php require_once 'footer.php'; ?>
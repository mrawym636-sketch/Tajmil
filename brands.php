<?php
$active_page='products'; $page_title='Marques';
require_once __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $slug = strtolower(trim(str_replace(' ', '-', $_POST['nom'])));
        $pdo->prepare("INSERT INTO brands (nom, slug, description, logo) VALUES (?,?,?,?)")->execute([$_POST['nom'], $slug, $_POST['description'], $_POST['logo']]);
        logAction($pdo, 'Ajout marque', "Nom: {$_POST['nom']}");
        header('Location: brands.php?added=1'); exit();
    }
    if (isset($_POST['edit'])) {
        $slug = strtolower(trim(str_replace(' ', '-', $_POST['nom'])));
        $pdo->prepare("UPDATE brands SET nom=?, slug=?, description=?, logo=? WHERE id=?")->execute([$_POST['nom'], $slug, $_POST['description'], $_POST['logo'], (int)$_POST['id']]);
        logAction($pdo, 'Modification marque', "ID {$_POST['id']}");
        header('Location: brands.php?updated=1'); exit();
    }
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM brands WHERE id=?")->execute([$id]);
    logAction($pdo, 'Suppression marque', "ID $id");
    header('Location: brands.php?deleted=1'); exit();
}
$brands = $pdo->query("SELECT * FROM brands ORDER BY nom")->fetchAll();
require_once 'header.php';
?>
<div class="panel">
    <div class="panel-head"><div class="panel-title"><i class="fas fa-trademark"></i> Marques</div></div>
    <div class="tbl-wrap"><table class="dtable"><thead><tr><th>ID</th><th>Nom</th><th>Slug</th><th>Logo</th><th>Description</th><th>Actions</th></tr></thead><tbody>
    <?php foreach($brands as $b): ?>
    <tr><td><?= $b['id'] ?></td><td><?= htmlspecialchars($b['nom']) ?></td><td><?= htmlspecialchars($b['slug']) ?></td><td><?= htmlspecialchars($b['logo']) ?></td><td><?= htmlspecialchars($b['description']) ?></td>
    <td><button class="btn btn-ghost btn-xs" onclick="openEdit(<?= $b['id'] ?>, '<?= htmlspecialchars($b['nom']) ?>', '<?= htmlspecialchars($b['description']) ?>', '<?= htmlspecialchars($b['logo']) ?>')"><i class="fas fa-pen"></i></button>
    <a href="?delete=<?= $b['id'] ?>" class="btn btn-danger btn-xs" onclick="return confirm('Supprimer ?')"><i class="fas fa-trash"></i></a></td>
    </tr><?php endforeach; ?>
    </tbody></table></div>
    <div class="panel-body"><h4>Ajouter une marque</h4>
    <form method="POST" style="display:flex; gap:10px; flex-wrap:wrap;"><input type="text" name="nom" placeholder="Nom" required><input type="text" name="logo" placeholder="Logo (nom fichier)"><input type="text" name="description" placeholder="Description"><button type="submit" name="add" class="btn btn-em">Ajouter</button></form></div>
</div>
<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); align-items:center; justify-content:center;"><div style="background:var(--surface); padding:25px; border-radius:16px; width:400px;"><h3>Modifier</h3><form method="POST"><input type="hidden" name="id" id="edit_id"><input type="text" name="nom" id="edit_nom" required><input type="text" name="logo" id="edit_logo"><input type="text" name="description" id="edit_desc"><button type="submit" name="edit" class="btn btn-em">Enregistrer</button><button type="button" onclick="closeModal()" class="btn btn-ghost">Annuler</button></form></div></div>
<script>function openEdit(id,nom,desc,logo){document.getElementById('edit_id').value=id;document.getElementById('edit_nom').value=nom;document.getElementById('edit_desc').value=desc;document.getElementById('edit_logo').value=logo;document.getElementById('editModal').style.display='flex';}function closeModal(){document.getElementById('editModal').style.display='none';}</script>
<?php require_once 'footer.php'; ?>
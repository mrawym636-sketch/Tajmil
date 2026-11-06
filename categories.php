<?php
$active_page='products'; $page_title='Catégories';
require_once __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $slug = strtolower(trim(str_replace(' ', '-', $_POST['nom'])));
        $pdo->prepare("INSERT INTO categories (nom, slug, description) VALUES (?,?,?)")->execute([$_POST['nom'], $slug, $_POST['description']]);
        logAction($pdo, 'Ajout catégorie', "Nom: {$_POST['nom']}");
        header('Location: categories.php?added=1'); exit();
    }
    if (isset($_POST['edit'])) {
        $slug = strtolower(trim(str_replace(' ', '-', $_POST['nom'])));
        $pdo->prepare("UPDATE categories SET nom=?, slug=?, description=? WHERE id=?")->execute([$_POST['nom'], $slug, $_POST['description'], (int)$_POST['id']]);
        logAction($pdo, 'Modification catégorie', "ID {$_POST['id']}");
        header('Location: categories.php?updated=1'); exit();
    }
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
    logAction($pdo, 'Suppression catégorie', "ID $id");
    header('Location: categories.php?deleted=1'); exit();
}
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll();
require_once 'header.php';
?>
<div class="panel">
    <div class="panel-head"><div class="panel-title"><i class="fas fa-tags"></i> Catégories</div></div>
    <div class="tbl-wrap"><table class="dtable"><thead><tr><th>ID</th><th>Nom</th><th>Slug</th><th>Description</th><th>Actions</th></tr></thead><tbody>
    <?php foreach($categories as $c): ?>
    <tr><td><?= $c['id'] ?></td><td><?= htmlspecialchars($c['nom']) ?></td><td><?= htmlspecialchars($c['slug']) ?></td><td><?= htmlspecialchars($c['description']) ?></td>
    <td><button class="btn btn-ghost btn-xs" onclick="openEdit(<?= $c['id'] ?>, '<?= htmlspecialchars($c['nom']) ?>', '<?= htmlspecialchars($c['description']) ?>')"><i class="fas fa-pen"></i></button>
    <a href="?delete=<?= $c['id'] ?>" class="btn btn-danger btn-xs" onclick="return confirm('Supprimer ?')"><i class="fas fa-trash"></i></a></td>
    </tr><?php endforeach; ?>
    </tbody></table></div>
    <div class="panel-body"><h4>Ajouter une catégorie</h4>
    <form method="POST" style="display:flex; gap:10px;"><input type="text" name="nom" placeholder="Nom" required><input type="text" name="description" placeholder="Description"><button type="submit" name="add" class="btn btn-em">Ajouter</button></form></div>
</div>
<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); align-items:center; justify-content:center;"><div style="background:var(--surface); padding:25px; border-radius:16px; width:400px;"><h3>Modifier</h3><form method="POST"><input type="hidden" name="id" id="edit_id"><input type="text" name="nom" id="edit_nom" required><input type="text" name="description" id="edit_desc"><button type="submit" name="edit" class="btn btn-em">Enregistrer</button><button type="button" onclick="closeModal()" class="btn btn-ghost">Annuler</button></form></div></div>
<script>function openEdit(id,nom,desc){document.getElementById('edit_id').value=id;document.getElementById('edit_nom').value=nom;document.getElementById('edit_desc').value=desc;document.getElementById('editModal').style.display='flex';}function closeModal(){document.getElementById('editModal').style.display='none';}</script>
<?php require_once 'footer.php'; ?>
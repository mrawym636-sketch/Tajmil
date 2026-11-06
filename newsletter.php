<?php
$active_page='users'; $page_title='Newsletter';
require_once __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_email'])) {
        try { $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)")->execute([$_POST['email']]); logAction($pdo, 'Ajout abonné', $_POST['email']); header('Location: newsletter.php?added=1'); exit(); } catch(PDOException $e) { header('Location: newsletter.php?error=1'); exit(); }
    }
    if (isset($_POST['send'])) {
        $subject = $_POST['subject']; $message = $_POST['message'];
        $emails = $pdo->query("SELECT email FROM newsletter_subscribers")->fetchAll(PDO::FETCH_COLUMN);
        foreach($emails as $email) { mail($email, $subject, $message, "From: newsletter@parfumerie.ma\r\nContent-Type: text/plain; charset=utf-8"); }
        logAction($pdo, 'Envoi newsletter', "Sujet: $subject, destinataires: ".count($emails));
        header('Location: newsletter.php?sent=1'); exit();
    }
}
if (isset($_GET['delete'])) { $id = (int)$_GET['delete']; $pdo->prepare("DELETE FROM newsletter_subscribers WHERE id=?")->execute([$id]); logAction($pdo, 'Suppression abonné', "ID $id"); header('Location: newsletter.php?deleted=1'); exit(); }
$subscribers = $pdo->query("SELECT * FROM newsletter_subscribers ORDER BY id DESC")->fetchAll();
require_once 'header.php';
?>
<div class="panel"><div class="panel-head"><div class="panel-title"><i class="fas fa-envelope"></i> Newsletter</div></div><div class="panel-body"><form method="POST" style="margin-bottom:30px;"><h4>➕ Ajouter un abonné</h4><div style="display:flex; gap:10px;"><input type="email" name="email" required style="flex:1; padding:10px;"><button type="submit" name="add_email" class="btn btn-em">Ajouter</button></div></form><form method="POST" style="margin-bottom:30px;"><h4>📧 Envoyer une newsletter</h4><input type="text" name="subject" placeholder="Sujet" required style="width:100%; padding:10px; margin-bottom:10px;"><textarea name="message" rows="6" placeholder="Message" required style="width:100%; padding:10px; margin-bottom:10px;"></textarea><button type="submit" name="send" class="btn btn-em">Envoyer à <?= count($subscribers) ?> abonné(s)</button></form><h4>📋 Liste des abonnés (<?= count($subscribers) ?>)</h4><table class="dtable"><thead><tr><th>#</th><th>Email</th><th>Date</th><th>Action</th></tr></thead><tbody><?php foreach($subscribers as $s): ?><tr><td><?= $s['id'] ?></td><td><?= htmlspecialchars($s['email']) ?></td><td><?= $s['date_subscribed'] ?></td><td><a href="?delete=<?= $s['id'] ?>" class="btn btn-danger btn-xs">Supprimer</a></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php require_once 'footer.php'; ?>
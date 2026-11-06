<?php
require_once 'config.php';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=produits_'.date('Y-m-d').'.csv');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'Nom', 'Marque', 'Prix', 'Ancien prix', 'Stock', 'Catégorie', 'Description', 'Image']);
$rows = $pdo->query("SELECT p.*, c.nom as cat_name FROM produits p LEFT JOIN categories c ON p.category_id=c.id")->fetchAll();
foreach($rows as $r) fputcsv($out, [$r['id'], $r['nom'], $r['marque'], $r['prix'], $r['ancien_prix'], $r['stock'], $r['cat_name'], $r['description'], $r['image']]);
fclose($out); exit();
?>
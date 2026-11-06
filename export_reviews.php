<?php
require_once 'config.php';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=avis_'.date('Y-m-d').'.csv');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'Client', 'Note', 'Commentaire', 'Statut', 'Date']);
$rows = $pdo->query("SELECT id, nom, note, commentaire, statut, date_creation FROM avis")->fetchAll();
foreach($rows as $r) fputcsv($out, [$r['id'], $r['nom'], $r['note'], $r['commentaire'], $r['statut'], $r['date_creation']]);
fclose($out); exit();
?>
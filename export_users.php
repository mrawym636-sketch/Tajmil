<?php
require_once 'config.php';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=utilisateurs_'.date('Y-m-d').'.csv');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'Nom', 'Email', 'Téléphone', 'Rôle', 'Date inscription']);
$rows = $pdo->query("SELECT id, nom, email, telephone, role, date_inscription FROM utilisateurs")->fetchAll();
foreach($rows as $r) fputcsv($out, [$r['id'], $r['nom'], $r['email'], $r['telephone'], $r['role'], $r['date_inscription']]);
fclose($out); exit();
?>
<?php
require_once 'config.php';
$search = $_GET['search'] ?? ''; $status = $_GET['status'] ?? ''; $date_from = $_GET['date_from'] ?? ''; $date_to = $_GET['date_to'] ?? '';
$where = []; $params = [];
if (!empty($search)) { $where[] = "(numero LIKE ? OR nom_client LIKE ? OR email_client LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
if (!empty($status)) { $where[] = "statut = ?"; $params[] = $status; }
if (!empty($date_from)) { $where[] = "DATE(date_commande) >= ?"; $params[] = $date_from; }
if (!empty($date_to)) { $where[] = "DATE(date_commande) <= ?"; $params[] = $date_to; }
$sql = "SELECT * FROM commandes";
if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY date_commande DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=commandes_'.date('Y-m-d').'.csv');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'N°', 'Client', 'Email', 'Téléphone', 'Total', 'Statut', 'Date']);
foreach($rows as $r) fputcsv($out, [$r['id'], $r['numero'], $r['nom_client'], $r['email_client'], $r['telephone_client'], $r['total'], $r['statut'], $r['date_commande']]);
fclose($out); exit();
?>
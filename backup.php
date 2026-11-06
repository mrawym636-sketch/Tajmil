<?php
require_once 'config.php';
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename=backup_'.date('Y-m-d_H-i-s').'.sql');
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach($tables as $table) {
    $create = $pdo->query("SHOW CREATE TABLE $table")->fetch(PDO::FETCH_ASSOC);
    echo "\n\n-- Structure $table\n";
    echo $create['Create Table'] . ";\n";
    $rows = $pdo->query("SELECT * FROM $table");
    while($row = $rows->fetch(PDO::FETCH_ASSOC)) {
        $cols = array_keys($row);
        $vals = array_map([$pdo, 'quote'], array_values($row));
        echo "INSERT INTO $table (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ");\n";
    }
}
exit();
?>
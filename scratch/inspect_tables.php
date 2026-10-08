<?php
$pdo = new PDO("mysql:host=127.0.0.1;port=3307;dbname=prime_aviation", "root", "");

echo "=== TABLES ===\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo implode(', ', $tables) . "\n\n";

foreach (['locations', 'properties', 'rooms'] as $table) {
    if (in_array($table, $tables)) {
        echo "=== SCHEMA: $table ===\n";
        $cols = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $col) {
            echo "  {$col['Field']} ({$col['Type']}) " . ($col['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . " default: {$col['Default']}\n";
        }
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "Total rows in $table: $count\n\n";
    }
}

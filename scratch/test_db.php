<?php
$ports = [3307, 3306];
foreach ($ports as $port) {
    echo "Testing MySQL on 127.0.0.1:$port ...\n";
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;port=$port", "root", "", [PDO::ATTR_TIMEOUT => 2]);
        echo "  SUCCESS! Connected to port $port\n";
        $stmt = $pdo->query("SHOW DATABASES");
        $dbs = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "  Databases: " . implode(', ', $dbs) . "\n";
    } catch (Exception $e) {
        echo "  FAILED: " . $e->getMessage() . "\n";
    }
}

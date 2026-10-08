<?php
$pdo = new PDO("mysql:host=127.0.0.1;port=3307;dbname=prime_aviation", "root", "");
$stmt = $pdo->query("SELECT id, name, city, price_per_night, star_rating, status, created_at FROM properties ORDER BY id ASC");
$props = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "=== PROPERTIES (" . count($props) . ") ===\n";
foreach ($props as $p) {
    echo "ID: {$p['id']} | {$p['name']} | City: {$p['city']} | Price: {$p['price_per_night']} | Stars: {$p['star_rating']} | Status: {$p['status']}\n";
}

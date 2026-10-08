<?php
$pdo = new PDO("mysql:host=127.0.0.1;port=3307;dbname=prime_aviation", "root", "");
$stmt = $pdo->query("SELECT id, name, primary_image, images FROM properties LIMIT 5");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']} | {$row['name']}\n";
    echo "  primary_image: {$row['primary_image']}\n";
    echo "  images: {$row['images']}\n\n";
}

$stmt2 = $pdo->query("SELECT id, name, images FROM rooms LIMIT 5");
while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    echo "Room ID: {$row['id']} | {$row['name']}\n";
    echo "  images: {$row['images']}\n\n";
}

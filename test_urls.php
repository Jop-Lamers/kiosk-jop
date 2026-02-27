<?php
require_once 'config/db.php';
$conn->set_charset("utf8mb4");

echo "<h2>Testing URL Encoding</h2>";

// Get a few products
$sql = "SELECT p.name, i.filename FROM products p 
        JOIN images i ON p.image_id = i.image_id 
        LIMIT 5";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $filename = $row['filename'];
    $encoded = rawurlencode($filename);
    $filePath = __DIR__ . '/menu-images/' . $filename;
    $encodedPath = __DIR__ . '/menu-images/' . $encoded;

    echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
    echo "<p>Filename: " . htmlspecialchars($filename) . "</p>";
    echo "<p>Encoded: " . htmlspecialchars($encoded) . "</p>";
    echo "<p>File exists (original): " . (file_exists($filePath) ? '✅' : '❌') . "</p>";
    echo "<p>File exists (encoded): " . (file_exists($encodedPath) ? '✅' : '❌') . "</p>";

    // Test actual URL
    $url = 'menu-images/' . $encoded;
    echo "<p>URL: <code>" . htmlspecialchars($url) . "</code></p>";
    echo "<p>Test: <img src='" . $url . "' style='width: 100px; height: 100px;' alt='test'></p>";
    echo "<hr>";
}

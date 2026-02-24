<?php
require_once 'config/db.php';
$res = $conn->query("SELECT name FROM categories");
while($r = $res->fetch_assoc()) echo "CAT: " . $r['name'] . "\n";
$res = $conn->query("SELECT name FROM products");
while($r = $res->fetch_assoc()) echo "PROD: " . $r['name'] . "\n";
?>

<?php
require_once 'config/db.php';

$tables = ['products', 'categories'];
foreach ($tables as $table) {
    echo "<h3>Table: $table</h3>";
    $result = $conn->query("DESCRIBE $table");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo $row['Field'] . " (" . $row['Type'] . ")<br>";
        }
    } else {
        echo "Error describing $table: " . $conn->error;
    }
}
?>

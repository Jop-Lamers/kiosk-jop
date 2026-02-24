<?php
require_once 'config/db.php';
$conn->set_charset("utf8mb4");

echo "<h2>Products in Database</h2>";
$sql = "SELECT p.product_id, p.name, c.name as category, i.filename FROM products p 
        JOIN categories c ON p.category_id = c.category_id
        JOIN images i ON p.image_id = i.image_id
        ORDER BY p.category_id, p.name";
$result = $conn->query($sql);

echo "<table border='1'><tr><th>ID</th><th>Category</th><th>Name</th><th>Image File</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['product_id']}</td>
            <td>{$row['category']}</td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['filename']) . "</td>
          </tr>";
}
echo "</table>";
?>

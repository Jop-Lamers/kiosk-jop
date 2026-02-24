<?php
require_once 'config/db.php';

// Update items with 0.00 price to 0.75
$sql = "UPDATE products SET price = 1.00 WHERE price = 0.00 AND category_id = 5"; // Category 5 is Sauces
if ($conn->query($sql) === TRUE) {
    echo "Updated sauce prices to €1.00";
} else {
    echo "Error updating prices: " . $conn->error;
}
?>

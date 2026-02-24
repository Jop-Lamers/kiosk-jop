<?php
require_once 'config/db.php';

$sql = "
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(10) NOT NULL,
    order_type ENUM('eat_in', 'take_away') NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'preparing', 'ready', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    extras JSON,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);
";

if ($conn->multi_query($sql)) {
    echo "Database updated successfully!";
} else {
    echo "Error updating database: " . $conn->error;
}
?>

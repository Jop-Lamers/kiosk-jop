<?php
require_once '../config/db.php';
header('Content-Type: application/json');

// Fetch orders that are Placed (2) or Preparing (3)
$sql = "SELECT o.order_id, o.order_status_id, o.pickup_number, o.datetime, 
               p.name, op.quantity, op.price 
        FROM orders o
        JOIN order_product op ON o.order_id = op.order_id
        JOIN products p ON op.product_id = p.product_id
        WHERE o.order_status_id IN (2, 3)
        ORDER BY o.datetime ASC";

$result = $conn->query($sql);
$orders = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $id = $row['order_id'];
        if (!isset($orders[$id])) {
            $orders[$id] = [
                'order_id' => $id,
                'status' => $row['order_status_id'],
                'pickup_number' => $row['pickup_number'],
                'time' => date('H:i', strtotime($row['datetime'])),
                'items' => []
            ];
        }
        $orders[$id]['items'][] = [
            'name' => $row['name'],
            'quantity' => $row['quantity']
        ];
    }
}

echo json_encode(array_values($orders));
?>

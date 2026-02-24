<?php
require_once '../config/db.php';

header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$items = $input['items'];
$total = $input['total'];

// Create Order (Status 2 = Placed and paid)
$stmt = $conn->prepare("INSERT INTO orders (order_status_id, price_total, pickup_number) VALUES (2, ?, ?)");
$pickup_number = rand(100, 999); // Simple random pickup number
$stmt->bind_param("di", $total, $pickup_number);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    
    // Insert Items
    $item_stmt = $conn->prepare("INSERT INTO order_product (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)");
    
    foreach ($items as $item) {
        $item_stmt->bind_param("iidi", $order_id, $item['product_id'], $item['price'], $item['quantity']);
        $item_stmt->execute();
    }
    
    echo json_encode(['success' => true, 'order_id' => $pickup_number]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>

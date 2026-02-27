<?php
require_once '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No data received']);
    exit;
}

$order_number = $data['order_number'];
$type = $data['type'];
$total = $data['total'];
$items = $data['items'];

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO orders (order_number, order_type, total_price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $order_number, $type, $total);
    $stmt->execute();
    $order_id = $conn->insert_id;

    $stmt_item = $conn->prepare("INSERT INTO order_product (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

    foreach ($items as $item) {
        $pid = $item['product_id'];
        $qty = $item['quantity'];
        $price = $item['price'];
        
        $stmt_item->bind_param("iidi", $order_id, $pid, $qty, $price);
    echo json_encode(['success' => true, 'order_id' => $order_id]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

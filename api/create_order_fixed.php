<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    apiResponse(false, null, 'Method not allowed', 405);
}

$input = getJsonInput();

if (!$input) {
    apiResponse(false, null, 'Invalid JSON input', 400);
}

// Validate input
if (!isset($input['items']) || !is_array($input['items']) || empty($input['items'])) {
    apiResponse(false, null, 'Items array is required', 400);
}

if (!isset($input['total'])) {
    apiResponse(false, null, 'Total price is required', 400);
}

$items = $input['items'];
$total = floatval($input['total']);

// Start transaction
if (!$conn->begin_transaction()) {
    handleDbError($conn->error);
}

try {
    // Create Order (Status 2 = Placed and paid)
    $pickup_number = rand(100, 999);
    $stmt = $conn->prepare("INSERT INTO orders (order_status_id, price_total, pickup_number, datetime) VALUES (?, ?, ?, NOW())");

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $status = 2;
    $stmt->bind_param("idi", $status, $total, $pickup_number);

    if (!$stmt->execute()) {
        throw new Exception($conn->error);
    }

    $order_id = $conn->insert_id;

    // Insert Items
    $item_stmt = $conn->prepare("INSERT INTO order_product (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)");

    if (!$item_stmt) {
        throw new Exception($conn->error);
    }

    foreach ($items as $item) {
        if (!isset($item['product_id']) || !isset($item['quantity']) || !isset($item['price'])) {
            throw new Exception('Missing required item fields: product_id, quantity, price');
        }

        $product_id = intval($item['product_id']);
        $price = floatval($item['price']);
        $quantity = intval($item['quantity']);

        $item_stmt->bind_param("iidi", $order_id, $product_id, $price, $quantity);

        if (!$item_stmt->execute()) {
            throw new Exception($conn->error);
        }
    }

    $conn->commit();

    apiResponse(true, [
        'order_id' => $order_id,
        'pickup_number' => $pickup_number,
        'total' => $total,
        'items_count' => count($items)
    ], 'Order created successfully', 201);
} catch (Exception $e) {
    $conn->rollback();
    apiResponse(false, null, $e->getMessage(), 400);
}

$item_stmt->close();
$stmt->close();
$conn->close();

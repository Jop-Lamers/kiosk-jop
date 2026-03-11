<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    apiResponse(false, null, 'Method not allowed', 405);
}

if (!isset($_GET['id'])) {
    apiResponse(false, null, 'Order ID is required', 400);
}

$order_id = intval($_GET['id']);

try {
    $sql = "SELECT o.order_id, o.order_status_id, o.pickup_number, o.price_total, o.datetime, 
                   p.product_id, p.name, op.quantity, op.price 
            FROM orders o
            LEFT JOIN order_product op ON o.order_id = op.order_id
            LEFT JOIN products p ON op.product_id = p.product_id
            WHERE o.order_id = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param("i", $order_id);

    if (!$stmt->execute()) {
        throw new Exception($conn->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        apiResponse(false, null, 'Order not found', 404);
    }

    $order = null;
    while ($row = $result->fetch_assoc()) {
        if ($order === null) {
            $order = [
                'order_id' => intval($row['order_id']),
                'pickup_number' => intval($row['pickup_number']),
                'status' => intval($row['order_status_id']),
                'total' => floatval($row['price_total']),
                'datetime' => $row['datetime'],
                'time' => date('H:i', strtotime($row['datetime'])),
                'items' => []
            ];
        }

        if ($row['product_id'] !== null) {
            $order['items'][] = [
                'product_id' => intval($row['product_id']),
                'name' => $row['name'],
                'quantity' => intval($row['quantity']),
                'price' => floatval($row['price'])
            ];
        }
    }

    apiResponse(true, $order, 'Order retrieved successfully');
} catch (Exception $e) {
    apiResponse(false, null, $e->getMessage(), 400);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}

<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    apiResponse(false, null, 'Method not allowed', 405);
}

// Optional filters
$status_filter = isset($_GET['status']) ? intval($_GET['status']) : null;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

try {
    // Build query
    $where_clause = '';
    $params = [];

    if ($status_filter !== null) {
        $where_clause = " WHERE o.order_status_id = ?";
        $params[] = $status_filter;
    }

    $sql = "SELECT o.order_id, o.order_status_id, o.pickup_number, o.price_total, o.datetime, 
                   p.product_id, p.name, op.quantity, op.price 
            FROM orders o
            LEFT JOIN order_product op ON o.order_id = op.order_id
            LEFT JOIN products p ON op.product_id = p.product_id
            $where_clause
            ORDER BY o.datetime DESC
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $params[] = $limit;
    $params[] = $offset;

    // Build type string for bind_param
    $type_string = str_repeat('i', count($params) - 2) . 'ii';

    if (count($params) > 0) {
        $stmt->bind_param($type_string, ...$params);
    }

    if (!$stmt->execute()) {
        throw new Exception($conn->error);
    }

    $result = $stmt->get_result();
    $orders = [];
    $order_map = [];

    while ($row = $result->fetch_assoc()) {
        $order_id = $row['order_id'];

        if (!isset($order_map[$order_id])) {
            $order_map[$order_id] = [
                'order_id' => intval($order_id),
                'pickup_number' => intval($row['pickup_number']),
                'status' => intval($row['order_status_id']),
                'total' => floatval($row['price_total']),
                'datetime' => $row['datetime'],
                'time' => date('H:i', strtotime($row['datetime'])),
                'items' => []
            ];
        }

        if ($row['product_id'] !== null) {
            $order_map[$order_id]['items'][] = [
                'product_id' => intval($row['product_id']),
                'name' => $row['name'],
                'quantity' => intval($row['quantity']),
                'price' => floatval($row['price'])
            ];
        }
    }

    $orders = array_values($order_map);

    apiResponse(true, [
        'orders' => $orders,
        'count' => count($orders),
        'limit' => $limit,
        'offset' => $offset
    ], 'Orders retrieved successfully');
} catch (Exception $e) {
    apiResponse(false, null, $e->getMessage(), 400);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}

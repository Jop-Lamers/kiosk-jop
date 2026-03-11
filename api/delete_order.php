<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    apiResponse(false, null, 'Method not allowed', 405);
}

// Handle both DELETE and POST with _method override
$order_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = getJsonInput();
    $order_id = isset($input['order_id']) ? intval($input['order_id']) : null;
} else {
    // POST request
    $input = getJsonInput();
    $order_id = isset($input['order_id']) ? intval($input['order_id']) : null;
}

if (!$order_id) {
    apiResponse(false, null, 'Order ID is required', 400);
}

try {
    // Validate order exists
    $check_stmt = $conn->prepare("SELECT order_id FROM orders WHERE order_id = ?");
    if (!$check_stmt) {
        throw new Exception($conn->error);
    }

    $check_stmt->bind_param("i", $order_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        apiResponse(false, null, 'Order not found', 404);
    }

    $check_stmt->close();

    // Start transaction
    if (!$conn->begin_transaction()) {
        throw new Exception($conn->error);
    }

    // Delete order items first (foreign key)
    $delete_items_stmt = $conn->prepare("DELETE FROM order_product WHERE order_id = ?");
    if (!$delete_items_stmt) {
        throw new Exception($conn->error);
    }

    $delete_items_stmt->bind_param("i", $order_id);
    if (!$delete_items_stmt->execute()) {
        throw new Exception($conn->error);
    }

    $delete_items_stmt->close();

    // Delete order
    $delete_order_stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    if (!$delete_order_stmt) {
        throw new Exception($conn->error);
    }

    $delete_order_stmt->bind_param("i", $order_id);
    if (!$delete_order_stmt->execute()) {
        throw new Exception($conn->error);
    }

    $delete_order_stmt->close();

    $conn->commit();

    apiResponse(true, [
        'order_id' => $order_id
    ], 'Order deleted successfully');
} catch (Exception $e) {
    if ($conn->connect_error === null) {
        $conn->rollback();
    }
    apiResponse(false, null, $e->getMessage(), 400);
} finally {
    $conn->close();
}

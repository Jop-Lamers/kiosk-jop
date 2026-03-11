<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    apiResponse(false, null, 'Method not allowed', 405);
}

$input = getJsonInput();

if (!$input) {
    apiResponse(false, null, 'Invalid JSON input', 400);
}

if (!isset($input['order_id'])) {
    apiResponse(false, null, 'Order ID is required', 400);
}

$order_id = intval($input['order_id']);

// Validate order exists
$check_stmt = $conn->prepare("SELECT order_id FROM orders WHERE order_id = ?");
if (!$check_stmt) {
    handleDbError($conn->error);
}

$check_stmt->bind_param("i", $order_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    apiResponse(false, null, 'Order not found', 404);
}

$check_stmt->close();

try {
    $updates = [];
    $params = [];
    $types = '';

    // Update status if provided
    if (isset($input['status'])) {
        $updates[] = "order_status_id = ?";
        $params[] = intval($input['status']);
        $types .= 'i';
    }

    // Update pickup number if provided
    if (isset($input['pickup_number'])) {
        $updates[] = "pickup_number = ?";
        $params[] = intval($input['pickup_number']);
        $types .= 'i';
    }

    // Update total price if provided
    if (isset($input['total'])) {
        $updates[] = "price_total = ?";
        $params[] = floatval($input['total']);
        $types .= 'd';
    }

    if (empty($updates)) {
        apiResponse(false, null, 'No fields to update', 400);
    }

    $params[] = $order_id;
    $types .= 'i';

    $sql = "UPDATE orders SET " . implode(", ", $updates) . " WHERE order_id = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        throw new Exception($conn->error);
    }

    apiResponse(true, [
        'order_id' => $order_id,
        'updated_fields' => count($updates)
    ], 'Order updated successfully');
} catch (Exception $e) {
    apiResponse(false, null, $e->getMessage(), 400);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}

<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if ($input && isset($input['order_id']) && isset($input['status'])) {
    $stmt = $conn->prepare("UPDATE orders SET order_status_id = ? WHERE order_id = ?");
    $stmt->bind_param("ii", $input['status'], $input['order_id']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
}
?>

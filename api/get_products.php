<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    apiResponse(false, null, 'Method not allowed', 405);
}

// Optional filters
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

try {
    $where_clause = '';
    $params = [];
    $types = '';

    if ($category_id !== null) {
        $where_clause = " WHERE category_id = ?";
        $params[] = $category_id;
        $types = 'i';
    }

    $sql = "SELECT product_id, name, category_id, description, price, image_url 
            FROM products
            $where_clause
            ORDER BY name ASC
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';

    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        throw new Exception($conn->error);
    }

    $result = $stmt->get_result();
    $products = [];

    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'product_id' => intval($row['product_id']),
            'name' => $row['name'],
            'category_id' => intval($row['category_id']),
            'description' => $row['description'],
            'price' => floatval($row['price']),
            'image_url' => $row['image_url']
        ];
    }

    apiResponse(true, [
        'products' => $products,
        'count' => count($products),
        'limit' => $limit,
        'offset' => $offset
    ], 'Products retrieved successfully');
} catch (Exception $e) {
    apiResponse(false, null, $e->getMessage(), 400);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}

<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    apiResponse(false, null, 'Method not allowed', 405);
}

try {
    $sql = "SELECT category_id, name, description, image_url 
            FROM category
            ORDER BY name ASC";

    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception($conn->error);
    }

    $categories = [];

    while ($row = $result->fetch_assoc()) {
        $categories[] = [
            'category_id' => intval($row['category_id']),
            'name' => $row['name'],
            'description' => $row['description'],
            'image_url' => $row['image_url']
        ];
    }

    apiResponse(true, [
        'categories' => $categories,
        'count' => count($categories)
    ], 'Categories retrieved successfully');
} catch (Exception $e) {
    apiResponse(false, null, $e->getMessage(), 400);
} finally {
    $conn->close();
}

<?php
require_once 'config/db.php';
$conn->set_charset("utf8mb4");

// Map English names to Dutch and English
$cat_maps = [
    'Breakfast' => ['nl' => 'Ontbijt', 'en' => 'Breakfast'],
    'Bowls' => ['nl' => 'Bowls', 'en' => 'Bowls'],
    'Wraps & Toasties' => ['nl' => 'Wraps & Toasties', 'en' => 'Wraps & Toasties'],
    'Sides' => ['nl' => 'Bijgerechten', 'en' => 'Sides'],
    'Sauces' => ['nl' => 'Sauzen', 'en' => 'Sauces'],
    'Drinks' => ['nl' => 'Dranken', 'en' => 'Drinks']
];

foreach ($cat_maps as $current_name => $langs) {
    $stmt = $conn->prepare("UPDATE categories SET name = ?, name_en = ? WHERE name = ? OR name = ?");
    $stmt->bind_param("ssss", $langs['nl'], $langs['en'], $current_name, $langs['nl']);
    $stmt->execute();
}

echo "Category names updated to Dutch/English pairs.";
?>

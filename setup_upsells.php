<?php
require_once 'config/db.php';

echo "<h2>Cross-Sell & Extras Setup</h2>";

// 1. Set some cross-sell associations
// Example: Bowls suggest Drinks, Snacks suggest Sauces
$upsells = [
    'Tofu Power Tahini Bowl (VG)' => 'Green Glow Smoothie (VG)',
    'The Supergreen Harvest (VG)' => 'Citrus Cooler (VG)',
    'Mediterranean Falafel Bowl (VG)' => 'Iced Matcha Latte (VG)',
    'Warm Teriyaki Tempeh Bowl (VG)' => 'Citrus Cooler (VG)',
    'Morning Boost Açaí Bowl (VG)' => 'Berry Blast Smoothie (VG)',
    'Zesty Chickpea Hummus Wrap (VG)' => 'Fruit-Infused Water (VG)',
    'Avocado & Halloumi Toastie (V)' => 'Green Glow Smoothie (VG)',
    'Smoky BBQ Jackfruit Slider (VG)' => 'Oven-Baked Sweet Potato Wedges (VG)',
    'Oven-Baked Sweet Potato Wedges (VG)' => 'Avocado Lime Crema (VG)',
    'Baked Falafel Bites - 5pcs (VG)' => 'Classic Hummus (VG)'
];

foreach ($upsells as $prod_name => $upsell_name) {
    // Get upsell product id
    $res = $conn->query("SELECT product_id FROM products WHERE name = '$upsell_name'");
    if ($r = $res->fetch_assoc()) {
        $upsell_id = $r['product_id'];
        $conn->query("UPDATE products SET cross_sell_id = $upsell_id WHERE name = '$prod_name'");
        echo "Linked $prod_name -> $upsell_name ($upsell_id)<br>";
    }
}

echo "Cross-sell IDs updated.";
?>

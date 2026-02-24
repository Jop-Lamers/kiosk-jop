<?php
require_once 'config/db.php';

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Clear existing data
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("TRUNCATE TABLE order_product");
$conn->query("TRUNCATE TABLE products");
$conn->query("TRUNCATE TABLE images");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

$items = [
    // Breakfast
    [
        'file' => 'Morning Boost Açaí Bowl (VG) – €7.50 (320 kcal).webp',
        'name' => 'Morning Boost Açaí Bowl (VG)',
        'desc' => 'A chilled blend of açaí and banana topped with crunchy granola.',
        'price' => 7.50,
        'kcal' => 320,
        'cat' => 1
    ],
    [
        'file' => 'Overnight Oats Apple Pie Style (VG) – €5.50 (290 kcal).webp',
        'name' => 'Overnight Oats Apple Pie Style (VG)',
        'desc' => 'Oats soaked in almond milk with grated apple, cinnamon and walnuts.',
        'price' => 5.50,
        'kcal' => 290,
        'cat' => 1
    ],
    [
        'file' => 'The Garden Breakfast Wrap (V) – €6.50 (280 kcal).webp',
        'name' => 'The Garden Breakfast Wrap (V)',
        'desc' => 'Whole grain wrap with fluffy scrambled eggs, baby spinach and tomato.',
        'price' => 6.50,
        'kcal' => 280,
        'cat' => 1
    ],
    [
        'file' => 'Peanut Butter & Cacao Toast (VG) – €5.00 (240 kcal).webp',
        'name' => 'Peanut Butter & Cacao Toast (VG)',
        'desc' => 'Sourdough toast with natural peanut butter, banana slices and cacao nibs.',
        'price' => 5.00,
        'kcal' => 240,
        'cat' => 1
    ],
    // Bowls
    [
        'file' => 'Tofu Power Tahini Bowl (VG) – €10.50 (480 kcal).webp',
        'name' => 'Tofu Power Tahini Bowl (VG)',
        'desc' => 'Tri-color quinoa, maple-glazed tofu, roasted sweet potato and tahini dressing.',
        'price' => 10.50,
        'kcal' => 480,
        'cat' => 2
    ],
    [
        'file' => 'The Supergreen Harvest (VG) – €9.50 (310 kcal).webp',
        'name' => 'The Supergreen Harvest (VG)',
        'desc' => 'Kale, edamame, avocado, cucumber, pumpkin seeds and lemon vinaigrette.',
        'price' => 9.50,
        'kcal' => 310,
        'cat' => 2
    ],
    [
        'file' => 'Mediterranean Falafel Bowl (VG) – €10.00 (440 kcal).webp',
        'name' => 'Mediterranean Falafel Bowl (VG)',
        'desc' => 'Baked falafel, hummus, pickled red onions, cherry tomatoes and couscous.',
        'price' => 10.00,
        'kcal' => 440,
        'cat' => 2
    ],
    [
        'file' => 'Warm Teriyaki Tempeh Bowl (VG) – €11.00 (500 kcal).webp',
        'name' => 'Warm Teriyaki Tempeh Bowl (VG)',
        'desc' => 'Brown rice, seared tempeh, broccoli, carrots with sticky teriyaki sauce.',
        'price' => 11.00,
        'kcal' => 500,
        'cat' => 2
    ],
    // Wraps
    [
        'file' => 'Zesty Chickpea Hummus Wrap (VG) – €8.50 (410 kcal).webp',
        'name' => 'Zesty Chickpea Hummus Wrap (VG)',
        'desc' => 'Spiced chickpeas, carrots, lettuce, signature hummus in a spinach wrap.',
        'price' => 8.50,
        'kcal' => 410,
        'cat' => 3
    ],
    [
        'file' => 'Avocado & Halloumi Toastie (V) – €9.00 (460 kcal).webp',
        'name' => 'Avocado & Halloumi Toastie (V)',
        'desc' => 'Grilled halloumi, smashed avocado, chili flakes on sourdough bread.',
        'price' => 9.00,
        'kcal' => 460,
        'cat' => 3
    ],
    [
        'file' => 'Smoky BBQ Jackfruit Slider (VG) – €7.50 (350 kcal).webp',
        'name' => 'Smoky BBQ Jackfruit Slider (VG)',
        'desc' => 'Pulled jackfruit in BBQ sauce with purple slaw on a soft bun.',
        'price' => 7.50,
        'kcal' => 350,
        'cat' => 3
    ],
    // Sides
    [
        'file' => 'Oven-Baked Sweet Potato Wedges (VG) – €4.50 (260 kcal).webp',
        'name' => 'Oven-Baked Sweet Potato Wedges (VG)',
        'desc' => 'Seasoned with smoked paprika.',
        'price' => 4.50,
        'kcal' => 260,
        'cat' => 4
    ],
    [
        'file' => 'Zucchini Fries (V) – €4.50 (190 kcal).webp',
        'name' => 'Zucchini Fries (V)',
        'desc' => 'Crispy breaded zucchini sticks.',
        'price' => 4.50,
        'kcal' => 190,
        'cat' => 4
    ],
    [
        'file' => 'Baked Falafel Bites - 5pcs (VG) – €5.00 (230 kcal).webp',
        'name' => 'Baked Falafel Bites - 5pcs (VG)',
        'desc' => 'Five oven baked falafel bites.',
        'price' => 5.00,
        'kcal' => 230,
        'cat' => 4
    ],
    [
        'file' => 'Mini Veggie Platter & Hummus (VG) – €4.00 (160 kcal).webp',
        'name' => 'Mini Veggie Platter & Hummus (VG)',
        'desc' => 'Celery, carrots and cucumber.',
        'price' => 4.00,
        'kcal' => 160,
        'cat' => 4
    ],
    // Sauces
    [
        'file' => 'Classic Hummus (VG) – 120 kcal.webp',
        'name' => 'Classic Hummus (VG)',
        'desc' => 'Creamy classic hummus.',
        'price' => 0.00,
        'kcal' => 120,
        'cat' => 5
    ],
    [
        'file' => 'Avocado Lime Crema (VG) – 110 kcal.webp',
        'name' => 'Avocado Lime Crema (VG)',
        'desc' => 'Fresh avocado lime sauce.',
        'price' => 0.00,
        'kcal' => 110,
        'cat' => 5
    ],
    [
        'file' => 'Greek Yogurt Ranch (V) – 90 kcal.webp',
        'name' => 'Greek Yogurt Ranch (V)',
        'desc' => 'Creamy greek yogurt ranch.',
        'price' => 0.00,
        'kcal' => 90,
        'cat' => 5
    ],
    [
        'file' => 'Spicy Sriracha Mayo (VG) – 180 kcal.webp',
        'name' => 'Spicy Sriracha Mayo (VG)',
        'desc' => 'Spicy sriracha mayo.',
        'price' => 0.00,
        'kcal' => 180,
        'cat' => 5
    ],
    [
        'file' => 'Peanut Satay Sauce (VG) – 200 kcal.webp',
        'name' => 'Peanut Satay Sauce (VG)',
        'desc' => 'Rich peanut satay sauce.',
        'price' => 0.00,
        'kcal' => 200,
        'cat' => 5
    ],
    // Drinks
    [
        'file' => 'Green Glow Smoothie (VG) – €3.50 (120 kcal).webp',
        'name' => 'Green Glow Smoothie (VG)',
        'desc' => 'Spinach, pineapple, cucumber and coconut water.',
        'price' => 3.50,
        'kcal' => 120,
        'cat' => 6
    ],
    [
        'file' => 'Iced Matcha Latte (VG) – €3.00 (90 kcal).webp',
        'name' => 'Iced Matcha Latte (VG)',
        'desc' => 'Sweetened matcha green tea with almond milk.',
        'price' => 3.00,
        'kcal' => 90,
        'cat' => 6
    ],
    [
        'file' => 'Fruit-Infused Water - €1.50 (0 kcal).webp',
        'name' => 'Fruit-Infused Water (VG)',
        'desc' => 'Lemon-mint, strawberry-basil or cucumber-lime.',
        'price' => 1.50,
        'kcal' => 0,
        'cat' => 6
    ],
    [
        'file' => 'Berry Blast Smoothie - €3.80 (140 kcal).webp',
        'name' => 'Berry Blast Smoothie (VG)',
        'desc' => 'Strawberries, blueberries, raspberries with almond milk.',
        'price' => 3.80,
        'kcal' => 140,
        'cat' => 6
    ],
    [
        'file' => 'Citrus Cooler - €3.00 (90 kcal).webp',
        'name' => 'Citrus Cooler (VG)',
        'desc' => 'Orange juice, sparkling water and hint of lime.',
        'price' => 3.00,
        'kcal' => 90,
        'cat' => 6
    ],
];

$img_stmt = $conn->prepare("INSERT INTO images (filename, description) VALUES (?, ?)");
$prod_stmt = $conn->prepare("INSERT INTO products (category_id, image_id, name, description, price, kcal) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($items as $item) {
    // Insert Image
    $img_stmt->bind_param("ss", $item['file'], $item['name']);
    if (!$img_stmt->execute()) {
        echo "Error inserting image: " . $item['file'] . " - " . $conn->error . "<br>";
        continue;
    }
    $image_id = $img_stmt->insert_id;

    // Insert Product
    $prod_stmt->bind_param("iisssi", $item['cat'], $image_id, $item['name'], $item['desc'], $item['price'], $item['kcal']);
    if (!$prod_stmt->execute()) {
        echo "Error inserting product: " . $item['name'] . " - " . $conn->error . "<br>";
    } else {
        echo "Inserted: " . $item['file'] . "<br>";
    }
}

echo "Database repair complete.";

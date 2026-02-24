<?php
require_once 'config/db.php';
$conn->set_charset("utf8mb4");

echo "<h2>Full Multi-Language Support Update</h2>";

// 1. Category Translations (Name + Description)
$categories = [
    'Breakfast'        => ['en' => 'Breakfast', 'desc_nl' => 'Begin je dag goed met onze gezonde ontbijtjes.', 'desc_en' => 'Start your day right with our healthy breakfast options.'],
    'Bowls'            => ['en' => 'Bowls', 'desc_nl' => 'Rijkgevulde kommen vol verse groenten en granen.', 'desc_en' => 'Hearty bowls filled with fresh vegetables and grains.'],
    'Wraps & Toasties' => ['en' => 'Wraps & Toasties', 'desc_nl' => 'Lekkere warme toasties en vers gerolde wraps.', 'desc_en' => 'Delicious warm toasties and freshly rolled wraps.'],
    'Sides'            => ['en' => 'Sides', 'desc_nl' => 'De perfecte aanvulling op je maaltijd.', 'desc_en' => 'The perfect accompaniment to your meal.'],
    'Sauces'           => ['en' => 'Sauces', 'desc_nl' => 'Huisgemaakte sauzen voor extra smaak.', 'desc_en' => 'Homemade sauces for extra flavor.'],
    'Drinks'           => ['en' => 'Drinks', 'desc_nl' => 'Verfrissende smoothies en koude dranken.', 'desc_en' => 'Refreshing smoothies and cold drinks.']
];

foreach ($categories as $nl => $data) {
    $stmt = $conn->prepare("UPDATE categories SET name_en = ?, description = ?, description_en = ? WHERE name = ?");
    $stmt->bind_param("ssss", $data['en'], $data['desc_nl'], $data['desc_en'], $nl);
    $stmt->execute();
}
echo "✅ Categories updated.<br>";

// 2. Product Translations (Description)
$products = [
    'Morning Boost Açaí Bowl (VG)' => 'Açaí bessen blend met granola en vers fruit.',
    'Overnight Oats Apple Pie Style (VG)' => 'Havermout geweekt in amandelmelk met appel en kaneel.',
    'The Garden Breakfast Wrap (V)' => 'Wrap met roerei, spinazie en feta.',
    'Peanut Butter & Cacao Toast (VG)' => 'Volkoren toast met pindakaas, banaan en cacao nibs.',
    'Tofu Power Tahini Bowl (VG)' => 'Bowl met gemarineerde tofu, quinoa en tahinicrème.',
    'The Supergreen Harvest (VG)' => 'Groene bowl met broccoli, edamame en avocado.',
    'Mediterranean Falafel Bowl (VG)' => 'Hummus, falafel, couscous en gegrilde groenten.',
    'Warm Teriyaki Tempeh Bowl (VG)' => 'Warme bowl met tempeh, rijst en sojasaus.',
    'Zesty Chickpea Hummus Wrap (VG)' => 'Smaakvolle wrap met kikkererwten en verse kruiden.',
    'Avocado & Halloumi Toastie (V)' => 'Warme toastie met avocado en gegrilde halloumi kaas.',
    'Smoky BBQ Jackfruit Slider (VG)' => 'Kleine burgers met malse jackfruit en BBQ-saus.',
    'Oven-Baked Sweet Potato Wedges (VG)' => 'Geroosterde zoete aardappel uit de oven.',
    'Zucchini Fries (V)' => 'Krokante frietjes gemaakt van courgette.',
    'Baked Falafel Bites - 5pcs (VG)' => 'Vijf stuks ovengebakken falafel balletjes.',
    'Mini Veggie Platter & Hummus (VG)' => 'Selectie van rauwe groenten met dip.',
    'Classic Hummus (VG)' => 'Onze beroemde huisgemaakte klassieke hummus.',
    'Avocado Lime Crema (VG)' => 'Frisse romige dip van avocado en limoen.',
    'Greek Yogurt Ranch (V)' => 'Romige ranch saus op basis van griekse yoghurt.',
    'Spicy Sriracha Mayo (VG)' => 'Pittige plantaardige mayonaise.',
    'Peanut Satay Sauce (VG)' => 'Huisgemaakte pindasaus.',
    'Green Glow Smoothie (VG)' => 'Groene smoothie met spinazie, appel en gember.',
    'Iced Matcha Latte (VG)' => 'Koude matcha thee met havermelk.',
    'Fruit-Infused Water (VG)' => 'Water met verse munt en seizoensruit.',
    'Berry Blast Smoothie (VG)' => 'Smoothie vol blauwe bessen en aardbeien.',
    'Citrus Cooler (VG)' => 'Verfrissende mix van citroen, munt en ijs.'
];

// For products, we'll keep English names as they are (since they are already mostly EN) 
// but ensure name_en is set for the toggle logic.
foreach ($products as $nl_name => $nl_desc) {
    // Basic translation for desc_en (mapping to existing logic or providing defaults)
    $en_desc_map = [
        'Morning Boost Açaí Bowl (VG)' => 'Açaí berry blend with granola and fresh fruit.',
        'Overnight Oats Apple Pie Style (VG)' => 'Oats soaked in almond milk with apple and cinnamon.',
        'The Garden Breakfast Wrap (V)' => 'Wrap with scrambled eggs, spinach, and feta.',
        'Peanut Butter & Cacao Toast (VG)' => 'Whole grain toast with peanut butter, banana, and cacao nibs.',
        'Tofu Power Tahini Bowl (VG)' => 'Bowl with marinated tofu, quinoa, and tahini cream.',
        'The Supergreen Harvest (VG)' => 'Green bowl with broccoli, edamame, and avocado.',
        'Mediterranean Falafel Bowl (VG)' => 'Hummus, falafel, couscous, and grilled vegetables.',
        'Warm Teriyaki Tempeh Bowl (VG)' => 'Warm bowl with tempeh, rice, and soy sauce.',
        'Zesty Chickpea Hummus Wrap (VG)' => 'Flavorful wrap with chickpeas and fresh herbs.',
        'Avocado & Halloumi Toastie (V)' => 'Warm toastie with avocado and grilled halloumi cheese.',
        'Smoky BBQ Jackfruit Slider (VG)' => 'Small burgers with tender jackfruit and BBQ sauce.',
        'Oven-Baked Sweet Potato Wedges (VG)' => 'Roasted sweet potato wedges from the oven.',
        'Zucchini Fries (V)' => 'Crispy fries made from zucchini.',
        'Baked Falafel Bites - 5pcs (VG)' => 'Five pieces of oven-baked falafel bites.',
        'Mini Veggie Platter & Hummus (VG)' => 'Selection of raw vegetables with dip.',
        'Classic Hummus (VG)' => 'Our famous homemade classic hummus.',
        'Avocado Lime Crema (VG)' => 'Fresh creamy dip made from avocado and lime.',
        'Greek Yogurt Ranch (V)' => 'Creamy ranch sauce based on Greek yogurt.',
        'Spicy Sriracha Mayo (VG)' => 'Spicy plant-based mayonnaise.',
        'Peanut Satay Sauce (VG)' => 'Homemade peanut sauce.',
        'Green Glow Smoothie (VG)' => 'Green smoothie with spinach, apple, and ginger.',
        'Iced Matcha Latte (VG)' => 'Cold matcha tea with oat milk.',
        'Fruit-Infused Water (VG)' => 'Water with fresh mint and seasonal fruit.',
        'Berry Blast Smoothie (VG)' => 'Smoothie full of blueberries and strawberries.',
        'Citrus Cooler (VG)' => 'Refreshing mix of lemon, mint, and ice.'
    ];
    
    $en_desc = isset($en_desc_map[$nl_name]) ? $en_desc_map[$nl_name] : substr($nl_desc, 0, 50);
    
    $stmt = $conn->prepare("UPDATE products SET name_en = ?, description = ?, description_en = ? WHERE name = ?");
    $stmt->bind_param("ssss", $nl_name, $nl_desc, $en_desc, $nl_name);
    $stmt->execute();
}
echo "✅ Products updated.<br>";

echo "Update complete.";
?>

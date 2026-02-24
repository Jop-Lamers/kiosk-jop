<?php
require_once 'config/db.php';
// Ensure proper charset for special characters
$conn->set_charset("utf8mb4");

// Fetch Categories
$cat_sql = "SELECT * FROM categories";
$cat_result = $conn->query($cat_sql);
$categories = [];
if ($cat_result->num_rows > 0) {
    while ($row = $cat_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Fetch Products with Image info
$prod_sql = "SELECT p.*, i.filename FROM products p 
             JOIN images i ON p.image_id = i.image_id 
             WHERE p.available = 1";
$prod_result = $conn->query($prod_sql);
$products = [];
if ($prod_result->num_rows > 0) {
    while ($row = $prod_result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Hardcoded fallback: resolve image files from folder based on product name
$imgDirPath = __DIR__ . '/menu-images';
$allImages = [];
if (is_dir($imgDirPath)) {
    $scan = scandir($imgDirPath);
    foreach ($scan as $f) {
        if ($f === '.' || $f === '..') continue;
        $allImages[] = $f;
    }
}

function _normalize_title($s)
{
    // Remove leading dashes, spaces, and punctuation
    $s = preg_replace('/^[\-\–\—\s\.]+/u', '', $s);
    // Remove diet tags like (VG) or (V)
    $s = preg_replace('/\s*\((VG|V)\)\s*/i', '', $s);
    // Remove " - €..." price patterns if present in filename
    $s = preg_replace('/\s*–?\s*€\d+(\.\d+)?\s*/u', '', $s);
    // Remove kcal patterns
    $s = preg_replace('/\s*\(\d+\s*kcal\)\s*/i', '', $s);
    return trim($s);
}

function pick_image_for_product($product, $allImages)
{
    $dbFile = isset($product['filename']) ? $product['filename'] : '';
    $name = isset($product['name']) ? $product['name'] : '';

    // 1) Exact DB filename exists
    if ($dbFile && in_array($dbFile, $allImages, true)) {
        return $dbFile;
    }

    $base = _normalize_title($name);
    $matches = [];

    // 2) Try matching by normalized name against normalized filenames
    foreach ($allImages as $rf) {
        $rfNorm = _normalize_title($rf);
        // Remove extension for matching
        $rfBase = preg_replace('/\.[^.]+$/', '', $rfNorm);
        
        if (strcasecmp($rfBase, $base) === 0) {
            return $rf;
        }
        
        if (stripos($rfBase, $base) === 0 || stripos($base, $rfBase) === 0) {
            $matches[] = ['file' => $rf, 'sim' => similarity($base, $rfBase)];
        }
    }

    // 3) Similarity fallback
    $best = '';
    $bestSim = 0;
    foreach ($allImages as $rf) {
        $a = $base;
        $b = _normalize_title(preg_replace('/\.[^.]+$/', '', $rf));
        similar_text($a, $b, $perc);
        if ($perc > $bestSim) {
            $bestSim = $perc;
            $best = $rf;
        }
    }
    if ($bestSim > 40) return $best;
    return '';
}

function similarity($a, $b) {
    similar_text($a, $b, $perc);
    return $perc;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Happy Herbivore</title>
    <?php
    $verStyle = @filemtime(__DIR__ . '/assets/css/style.css') ?: time();
    $verMenuCss = @filemtime(__DIR__ . '/assets/css/menu.css') ?: time();
    $verMenuJs = @filemtime(__DIR__ . '/assets/js/menu.js') ?: time();
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $verStyle; ?>">
    <link rel="stylesheet" href="assets/css/menu.css?v=<?php echo $verMenuCss; ?>">

</head>

<body class="menu-mode">

    <div class="kiosk-container">
        <!-- Sidebar Categories -->
        <aside class="sidebar">
            <div class="logo-small">
                <img src="logos/logo-images/logo happy herbivore.webp" alt="Logo">
            </div>

            <nav class="category-nav">
                <button class="cat-btn active" data-id="all" id="cat-all" 
                        data-nl="Alle" data-en="All" 
                        data-desc-nl="Bekijk ons volledige assortiment van heerlijke plant-based opties."
                        data-desc-en="View our full range of delicious plant-based options.">
                    Alle
                </button>
                <?php foreach ($categories as $cat): ?>
                    <button class="cat-btn" 
                            data-id="<?php echo $cat['category_id']; ?>"
                            data-nl="<?php echo htmlspecialchars($cat['name']); ?>"
                            data-en="<?php echo htmlspecialchars($cat['name_en'] ?: $cat['name']); ?>"
                            data-desc-nl="<?php echo htmlspecialchars($cat['description'] ?: ''); ?>"
                            data-desc-en="<?php echo htmlspecialchars($cat['description_en'] ?: ''); ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </button>
                <?php endforeach; ?>
            </nav>

            <div class="sidebar-footer">
                <div class="lang-toggle">
                    <button class="lang-btn active" id="lang-menu-nl" onclick="setLang('NL')">NL</button>
                    <button class="lang-btn" id="lang-menu-en" onclick="setLang('EN')">EN</button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="content-area">
            <header class="category-header">
                <div class="header-main">
                    <h1 id="active-cat-name">Alle</h1>
                    <p id="active-cat-desc">Bekijk ons volledige assortiment van heerlijke plant-based opties.</p>
                </div>
                
                <div class="header-controls">
                    <div class="search-wrapper">
                        <span class="search-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </span>
                        <input type="text" id="product-search" placeholder="Zoek een gerecht..." autocomplete="off">
                    </div>
                    
                    <div class="filter-chips">
                        <button class="filter-chip" data-filter="popular" data-nl="Populair" data-en="Popular">
                            <svg class="chip-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M17.51 3.82L15.73 2.04C15.24 1.55 14.45 1.55 13.96 2.04L12.18 3.82C11.69 4.31 11.69 5.1 12.18 5.59L13.96 7.37C14.45 7.86 15.24 7.86 15.73 7.37L17.51 5.59C18 5.1 18 4.31 17.51 3.82ZM12.18 20.18L13.96 21.96C14.45 22.45 15.24 22.45 15.73 21.96L17.51 20.18C18 19.69 18 18.9 17.51 18.41L15.73 16.63C15.24 16.14 14.45 16.14 13.96 16.63L12.18 18.41C11.69 18.9 11.69 19.69 12.18 20.18ZM3.82 17.51L2.04 15.73C1.55 15.24 1.55 14.45 2.04 13.96L3.82 12.18C4.31 11.69 5.1 11.69 5.59 12.18L7.37 13.96C7.86 14.45 7.86 15.24 7.37 15.73L5.59 17.51C5.1 18 4.31 18 3.82 17.51ZM3.82 6.49L5.59 8.27C6.08 8.76 6.87 8.76 7.36 8.27L9.14 6.49C9.63 6 9.63 5.21 9.14 4.72L7.36 2.94C6.87 2.45 6.08 2.45 5.59 2.94L3.82 4.72C3.33 5.21 3.33 6 3.82 6.49ZM20.18 12.18L21.96 13.96C22.45 14.45 22.45 15.24 21.96 15.73L20.18 17.51C19.69 18 18.9 18 18.41 17.51L16.63 15.73C16.14 15.24 16.14 14.45 16.63 13.96L18.41 12.18C18.9 11.69 19.69 11.69 20.18 12.18ZM8.46 11.5L8.46 12.5C8.46 14.43 10.07 16 12 16C13.93 16 15.54 14.43 15.54 12.5L15.54 11.5L12 8L8.46 11.5Z"/></svg>
                            <span class="chip-text">Populair</span>
                        </button>
                    </div>
                </div>
            </header>
            
            <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card" onclick="openCustomizer(<?php echo $product['product_id']; ?>)"
                     data-category="<?php echo $product['category_id']; ?>"
                     data-name-nl="<?php echo htmlspecialchars($product['name']); ?>"
                     data-name-en="<?php echo htmlspecialchars($product['name_en'] ?: $product['name']); ?>"
                     data-desc-nl="<?php echo htmlspecialchars($product['description']); ?>"
                     data-desc-en="<?php echo htmlspecialchars($product['description_en'] ?: $product['description']); ?>"
                     data-popular="<?php echo in_array($product['product_id'], [1, 3, 5, 10, 12]) ? '1' : '0'; ?>">
                    <?php
                    $imgResolved = pick_image_for_product($product, $allImages);
                    $imgSrc = $imgResolved ? ('menu-images/' . rawurlencode($imgResolved)) : 'logos/logo-images/logo happy herbivore.webp';
                    ?>
                    <div class="img-container">
                        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <div class="info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <?php if (!empty($product['description'])): ?>
                            <p class="desc product-desc"><?php echo htmlspecialchars($product['description']); ?></p>
                        <?php endif; ?>
                        <div class="meta">
                            <span class="calories"><?php echo $product['kcal']; ?> kcal</span>
                            <span class="price">€<?php echo number_format($product['price'], 2); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </main>

        <!-- Order Sidebar / Cart -->
        <aside class="order-panel">
            <h2 id="order-title">Uw Bestelling</h2>
            <div id="cart-items" class="cart-items">
                <!-- Cart items here -->
                <div class="empty-msg">Select items to add</div>
            </div>

            <div class="cart-footer">
                <div class="total-row">
                    <span>Total:</span>
                    <span id="cart-total">€0.00</span>
                </div>
                <button id="checkout-btn" class="checkout-btn" disabled onclick="window.location.href='checkout.php'">Nu Betalen</button>
            </div>
        </aside>
    </div>

    <!-- Product Customization Modal -->
    <div id="custom-modal" class="modal-overlay hidden">
        <div class="modal-content">
            <button class="close-modal" onclick="closeCustomizer()">&times;</button>
            
            <div id="customizer-view" class="modal-body">
                <div class="modal-left">
                    <img id="modal-img" src="" alt="Product">
                </div>
                <div class="modal-right">
                    <h2 id="modal-title">Product Name</h2>
                    <p id="modal-desc">Product description goes here.</p>
                    
                    <div class="custom-section" id="section-extras">
                        <h3 id="modal-extras-title">Extra's</h3>
                        <div class="extras-grid" id="grid-extras">
                            <!-- Dynamically populated -->
                        </div>
                    </div>

                    <div class="custom-section" id="section-wishes">
                        <h3 id="modal-wishes-title">Wensen</h3>
                        <div class="extras-grid" id="grid-wishes">
                            <!-- Dynamically populated -->
                        </div>
                    </div>

                    <div id="upsell-container" class="upsell-section hidden">
                        <h3 id="modal-upsell-title">Maak er een deal van!</h3>
                        <div class="upsell-card">
                            <img id="upsell-img" src="" alt="Upsell">
                            <div class="upsell-info">
                                <h4 id="upsell-name">Upsell Item</h4>
                                <p id="upsell-price">€0.00</p>
                                <button class="upsell-btn" id="upsell-btn-action" onclick="toggleUpsell()">Voeg toe voor deal</button>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="modal-total">Total: <span id="modal-total-price">€0.00</span></div>
                        <button class="confirm-btn" id="modal-confirm-btn" onclick="confirmCustomization()">Toevoegen aan bestelling</button>
                    </div>
                </div>
            </div>

            <!-- New Dedicated Upsell View -->
            <div id="upsell-view" class="modal-body hidden">
                <div class="upsell-screen-content">
                    <h2 id="upsell-view-title">Lekker voor erbij?</h2>
                    <div id="upsell-suggestions-grid" class="upsell-suggestions-grid">
                        <!-- Dynamically filled -->
                    </div>
                    <button id="upsell-skip-btn" class="skip-btn" onclick="skipUpsell()">Nee bedankt, verder gaan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Store categories for logic
        window.allCategories = {
            <?php foreach ($categories as $cat): ?>
                "<?php echo $cat['category_id']; ?>": "<?php echo addslashes($cat['name']); ?>",
            <?php endforeach; ?>
        };
        // Store products globally for JS access
        window.allProducts = <?php echo json_encode($products); ?>;
        // Map images
        window.productImages = {
            <?php foreach ($products as $p): ?>
                "<?php echo $p['product_id']; ?>": "<?php echo pick_image_for_product($p, $allImages) ? ('menu-images/' . rawurlencode(pick_image_for_product($p, $allImages))) : 'logos/logo-images/logo happy herbivore.webp'; ?>",
            <?php endforeach; ?>
        };
    </script>
    <script src="assets/js/menu.js?v=<?php echo $verMenuJs; ?>"></script>
    <script>
        function setLang(lang) {
            localStorage.setItem('kiosk_lang', lang);
            const texts = {
                'NL': { all: 'Alle', order: 'Uw Bestelling', pay: 'Nu Betalen', add: 'Voeg toe' },
                'EN': { all: 'All', order: 'Your Order', pay: 'Pay Now', add: 'Add to Order' }
            };
            const current = texts[lang];
            
            // 1. Static UI elements
            document.getElementById('order-title').innerText = current.order;
            document.getElementById('checkout-btn').innerText = current.pay;
            
            // 2. Category buttons
            document.querySelectorAll('.cat-btn').forEach(btn => {
                const nl = btn.getAttribute('data-nl');
                const en = btn.getAttribute('data-en');
                if (nl && en) {
                    btn.innerText = (lang === 'NL' ? nl : en);
                }
            });

            // 3. Update active category header
            const activeBtn = document.querySelector('.cat-btn.active');
            if (activeBtn) {
                document.getElementById('active-cat-name').innerText = activeBtn.getAttribute('data-' + lang.toLowerCase());
                document.getElementById('active-cat-desc').innerText = activeBtn.getAttribute('data-desc-' + lang.toLowerCase());
            }

            // 4. Product Cards
            document.querySelectorAll('.product-card').forEach(card => {
                const nameEl = card.querySelector('.product-name');
                const descEl = card.querySelector('.product-desc');
                const btnEl = card.querySelector('.btn-add-text');
                
                if (nameEl) nameEl.innerText = card.getAttribute('data-name-' + lang.toLowerCase());
                if (descEl) descEl.innerText = card.getAttribute('data-desc-' + lang.toLowerCase());
                if (btnEl) btnEl.innerText = current.add;
            });

            // 5. Toggle active class on lang buttons
            document.getElementById('lang-menu-nl').classList.toggle('active', lang === 'NL');
            document.getElementById('lang-menu-en').classList.toggle('active', lang === 'EN');

            // 6. Search & Filter Labels
            const searchInput = document.getElementById('product-search');
            if (searchInput) {
                searchInput.placeholder = (lang === 'NL' ? 'Zoek een gerecht...' : 'Search for a dish...');
            }
            document.querySelectorAll('.filter-chip').forEach(chip => {
                const textEl = chip.querySelector('.chip-text');
                if (textEl) {
                    textEl.innerText = chip.getAttribute('data-' + lang.toLowerCase());
                }
            });

            window.kiosk_texts = current;
            if (typeof updateCartUI === 'function') updateCartUI();
            if (typeof runFilter === 'function') runFilter(false); // Refresh filtering with new language data
        }
        setLang(localStorage.getItem('kiosk_lang') || 'NL');
    </script>
</body>

</html>
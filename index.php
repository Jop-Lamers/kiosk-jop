<?php
require_once 'config/db.php';
// Ensure proper charset for special characters
$conn->set_charset("utf8mb4");

// Strongly discourage browser/proxy caching for kiosk
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

// Fetch random products for the slideshow
$sql = "SELECT p.name, p.name_en, p.price, p.kcal, i.filename FROM products p 
        JOIN images i ON p.image_id = i.image_id 
        WHERE p.available = 1 
        ORDER BY RAND() LIMIT 5";
$result = $conn->query($sql);
$slides = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $slides[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Happy Herbivore Kiosk</title>
    <?php
    $verStyle = @filemtime(__DIR__ . '/assets/css/style.css') ?: time();
    $verIdleJs = @filemtime(__DIR__ . '/assets/js/idle.js') ?: time();
    $logoPath = __DIR__ . '/logos/logo-images/logo happy herbivore.webp';
    $verLogo = @filemtime($logoPath) ?: time();
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $verStyle; ?>">
    <style>
        /* Inline fallback to ensure ring appears even with stale caches */
        .logo-circle {
            --logo-ring-color: var(--color-orange);
            --logo-ring-size: 10px;
            width: clamp(220px, 35vw, 460px);
            aspect-ratio: 1/1;
            border-radius: 50%;
            border: var(--logo-ring-size) solid var(--logo-ring-color);
            display: grid;
            place-items: center;
            background: var(--color-light-green);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .35), inset 0 0 0 8px rgba(255, 255, 255, .06);
            margin-bottom: 2rem
        }

        .logo-circle .main-logo {
            width: 80%;
            height: 80%;
            object-fit: contain;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, .3))
        }
    </style>
</head>

<body class="idle-mode">

    <!-- Dedicated Aurora Background -->
    <div class="aurora-bg" aria-hidden="true">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="blob blob-4"></div>
    </div>

    <div class="screensaver" id="screensaver" style="background: transparent;">

        <div class="lang-toggle">
            <button class="lang-btn active" onclick="event.stopPropagation(); setLang('NL')">NL</button>
            <button class="lang-btn" onclick="event.stopPropagation(); setLang('EN')">EN</button>
        </div>

        <div class="overlay">
            <div class="logo-container">
                <div class="logo-circle">
                    <img src="logos/logo-images/logo happy herbivore.webp?v=<?php echo $verLogo; ?>" alt="Happy Herbivore Logo" class="main-logo">
                </div>
            </div>
            <div class="cta-container">
                <h2 class="pulse-anim" id="tap-text">Tik om te bestellen</h2>
            </div>

            <!-- Animated dish showcase panel (loop) -->
            <div class="dish-panel fade-in">
                <div class="dish-track">
                    <?php 
                    $doubledSlides = array_merge($slides, $slides); 
                    foreach ($doubledSlides as $slide): 
                    ?>
                        <?php
                        $sFile = $slide['filename'];
                        $sPath = __DIR__ . '/menu-images/' . $sFile;
                        $sVer = @filemtime($sPath) ?: time();
                        $sUrl = 'menu-images/' . rawurlencode($sFile) . '?v=' . $sVer;
                        ?>
                        <div class="dish-card" 
                             data-name-nl="<?php echo htmlspecialchars($slide['name']); ?>"
                             data-name-en="<?php echo htmlspecialchars($slide['name_en'] ?: $slide['name']); ?>">
                            <div class="dish-thumb" style="background-image: url('<?php echo $sUrl; ?>')"></div>
                            <div class="dish-info">
                                <div class="dish-name"><?php echo htmlspecialchars($slide['name']); ?></div>
                                <div class="dish-meta">
                                    <span>€<?php echo number_format($slide['price'], 2); ?></span>
                                    <span><?php echo $slide['kcal']; ?> kcal</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/idle.js?v=<?php echo $verIdleJs; ?>"></script>
    <script>
        function setLang(lang) {
            localStorage.setItem('kiosk_lang', lang);
            document.querySelectorAll('.lang-btn').forEach(b => {
                b.classList.remove('active');
                if (b.innerText === lang) b.classList.add('active');
            });
            
            const texts = {
                'NL': 'Tik om te bestellen',
                'EN': 'Touch to order'
            };
            document.getElementById('tap-text').innerText = texts[lang];

            // Update carousel items
            document.querySelectorAll('.dish-card').forEach(card => {
                const nameEl = card.querySelector('.dish-name');
                if (nameEl) {
                    nameEl.innerText = card.getAttribute('data-name-' + lang.toLowerCase());
                }
            });
        }

        document.body.addEventListener('click', () => {
            window.location.href = 'mode.php';
        });
    </script>
</body>

</html>
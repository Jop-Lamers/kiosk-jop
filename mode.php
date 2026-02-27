<?php
// Simple intermediate screen to choose order mode
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Select Order Mode</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <?php
    $verStyle = @filemtime(__DIR__ . '/assets/css/style.css') ?: time();
    $verResponsiveJs = @filemtime(__DIR__ . '/assets/js/responsive.js') ?: time();
    $logoPath = __DIR__ . '/logos/logo-images/logo happy herbivore.webp';
    $verLogo = @filemtime($logoPath) ?: time();
    ?>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $verStyle; ?>" />
    <style>
        body {
            background: var(--color-dark-blue);
            color: #fff;
        }

        .mode-screen {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 2rem;
        }

        .mode-container {
            width: min(900px, 92vw);
        }

        .mode-title {
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--color-light-green);
            font-size: 2.2rem;
            font-weight: 800;
        }

        .mode-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .mode-card {
            background: rgba(var(--dark-blue-rgb, 5, 54, 49), 0.45);
            border: 3px solid rgba(var(--light-green-rgb, 222, 255, 120), 0.8);
            border-radius: 22px;
            padding: 2.2rem 2rem;
            text-align: center;
            cursor: pointer;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.35);
            transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
            min-height: 220px;
            position: relative;
        }

        .mode-card:active {
            transform: scale(0.98);
        }

        .mode-card:focus-visible {
            outline: none;
            box-shadow: 0 0 0 6px rgba(var(--light-green-rgb, 222, 255, 120), 0.35);
        }

        .mode-icon {
            display: grid;
            place-items: center;
            margin-bottom: 0.75rem;
        }

        .mode-icon svg {
            width: 84px;
            height: 84px;
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.35));
        }

        .mode-card .label {
            font-size: 2.1rem;
            font-weight: 900;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .mode-card .sub {
            margin-top: .5rem;
            opacity: .9;
        }

        .eat-in {
            border-color: var(--color-orange);
            box-shadow: 0 10px 24px rgba(var(--orange-rgb, 255, 117, 32), 0.35);
        }

        .take-away {
            border-color: var(--color-green);
            box-shadow: 0 10px 24px rgba(var(--green-rgb, 140, 208, 3), 0.35);
        }

        .mode-footer {
            text-align: center;
            margin-top: 1rem;
            opacity: .8;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 1.25rem;
            font-weight: 800;
            background: rgba(var(--dark-blue-rgb, 5, 54, 49), 0.45);
            border: 3px solid var(--color-orange);
            color: var(--color-orange);
            padding: 0.8rem 1.4rem;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.35);
            transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
        }

        .back-btn:hover {
            background: rgba(var(--orange-rgb, 255, 117, 32), 0.15);
        }

        .back-btn:active {
            transform: scale(0.98);
        }

        .back-btn:focus-visible {
            outline: none;
            box-shadow: 0 0 0 6px rgba(var(--light-green-rgb, 222, 255, 120), 0.45);
        }

        @media (max-width: 640px) {
            .mode-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Dedicated Aurora Background -->
    <div class="aurora-bg" aria-hidden="true">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="blob blob-4"></div>
    </div>

    <div class="lang-toggle">
        <button class="lang-btn active" id="lang-nl" onclick="setLang('NL')">NL</button>
        <button class="lang-btn" id="lang-en" onclick="setLang('EN')">EN</button>
    </div>

    <div class="overlay">
        <div class="logo-container">
            <div class="logo-circle">
                <img src="logos/logo-images/logo happy herbivore.webp?v=<?php echo $verLogo; ?>" alt="Happy Herbivore Logo" class="main-logo">
            </div>
        </div>

        <div class="mode-selection">
            <h1 id="mode-title" class="mode-title">Hoe wilt u genieten?</h1>
            <div class="mode-options">
                <div class="mode-card eat-in" onclick="selectMode('eat-in')">
                    <div class="mode-icon">
                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Plate with inner rim -->
                            <circle cx="32" cy="35" r="18" stroke="currentColor" stroke-width="4" />
                            <circle cx="32" cy="35" r="11" stroke="currentColor" stroke-width="4" opacity="0.3" />
                            <!-- Fork (Left) -->
                            <path d="M8 50v-10" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                            <path d="M4 26v6c0 2.2 1.8 4 4 4s4-1.8 4-4v-6" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M8 26v6" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                            <!-- Knife (Right) -->
                            <path d="M56 50v-10" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                            <path d="M56 40c0-12-4-15-4-15v15h4" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <p class="label" id="eat-in-label">Hier eten</p>
                    <p class="sub" id="eat-in-sub">Wij serveren het direct.</p>
                </div>
                <div class="mode-card take-away" onclick="selectMode('take-away')">
                    <div class="mode-icon">
                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="16" y="22" width="32" height="28" rx="6" stroke="currentColor" stroke-width="4" />
                            <path d="M24 22c0-6 4-10 8-10s8 4 8 10" stroke="currentColor" stroke-width="4" />
                        </svg>
                    </div>
                    <p class="label" id="take-away-label">Meenemen</p>
                    <p class="sub" id="take-away-sub">Handig voor onderweg.</p>
                </div>
            </div>
            <div class="mode-footer">
                <button class="back-btn" onclick="goBack()" id="back-btn-text">
                    Terug
                </button>
            </div>
        </div>
    </div>
    <script>
        function setLang(lang) {
            localStorage.setItem('kiosk_lang', lang);
            document.getElementById('lang-nl').classList.toggle('active', lang === 'NL');
            document.getElementById('lang-en').classList.toggle('active', lang === 'EN');

            const texts = {
                'NL': {
                    title: 'Hoe wilt u genieten?',
                    eatin: 'Hier eten',
                    eatinsub: 'Wij serveren het direct.',
                    takeaway: 'Meenemen',
                    takeawaysub: 'Handig voor onderweg.',
                    back: 'Terug'
                },
                'EN': {
                    title: 'How would you like to eat?',
                    eatin: 'Eat In',
                    eatinsub: 'We serve it fresh here.',
                    takeaway: 'Take Away',
                    takeawaysub: 'Convenient for on the go.',
                    back: 'Back'
                }
            };

            const t = texts[lang];
            document.getElementById('mode-title').innerText = t.title;
            document.getElementById('eat-in-label').innerText = t.eatin;
            document.getElementById('eat-in-sub').innerText = t.eatinsub;
            document.getElementById('take-away-label').innerText = t.takeaway;
            document.getElementById('take-away-sub').innerText = t.takeawaysub;
            document.getElementById('back-btn-text').innerText = t.back;
        }

        const currentLang = localStorage.getItem('kiosk_lang') || 'NL';
        setLang(currentLang);

        function selectMode(m) {
            localStorage.setItem('kiosk_order_mode', m);
            window.location.href = 'menu.php';
        }

        function goBack() {
            window.location.href = 'index.php';
        }
    </script>
    <script src="assets/js/responsive.js?v=<?php echo $verResponsiveJs; ?>"></script>
</body>

</html>
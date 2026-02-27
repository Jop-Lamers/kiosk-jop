<?php
require_once 'config/db.php';
$verStyle = @filemtime(__DIR__ . '/assets/css/style.css') ?: time();
$verMenuCss = @filemtime(__DIR__ . '/assets/css/menu.css') ?: time();
$verResponsiveJs = @filemtime(__DIR__ . '/assets/js/responsive.js') ?: time();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Checkout - Happy Herbivore</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $verStyle; ?>">
    <link rel="stylesheet" href="assets/css/menu.css?v=<?php echo $verMenuCss; ?>">
    <style>
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
            color: var(--color-dark-blue);
        }

        .checkout-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .checkout-header h1 {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
            letter-spacing: -2px;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 4rem;
            align-items: start;
        }

        .review-panel {
            background: white;
            border-radius: 32px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        }

        .review-title {
            font-size: 1.8rem;
            font-weight: 900;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .checkout-item {
            display: flex;
            gap: 1.5rem;
            padding: 1.5rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .checkout-item:last-child {
            border-bottom: none;
        }

        .checkout-item-img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            background: #f9f9f9;
            border-radius: 16px;
        }

        .checkout-item-info {
            flex: 1;
        }

        .checkout-item-info h4 {
            font-size: 1.4rem;
            margin: 0 0 0.5rem 0;
            font-weight: 800;
        }

        .checkout-item-extras {
            font-size: 1rem;
            color: var(--color-orange);
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .checkout-item-price {
            font-size: 1.2rem;
            font-weight: 800;
        }

        .checkout-item-qty {
            font-size: 1.1rem;
            background: #eee;
            padding: 0.5rem 1rem;
            border-radius: 12px;
            height: fit-content;
        }

        .summary-panel {
            position: sticky;
            top: 2rem;
        }

        .type-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid white;
            border-radius: 20px;
            padding: 0.5rem;
            margin-bottom: 2rem;
        }

        .type-btn {
            padding: 1.5rem;
            border-radius: 16px;
            border: none;
            background: transparent;
            color: white;
            font-size: 1.2rem;
            font-weight: 900;
            cursor: pointer;
            transition: all 0.3s;
        }

        .type-btn.active {
            background: white;
            color: var(--color-dark-blue);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .total-box {
            background: white;
            border-radius: 32px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            font-weight: 900;
        }

        .total-row span:last-child {
            color: var(--color-orange);
        }

        .pay-btn {
            width: 100%;
            background: var(--color-green);
            color: white;
            border: none;
            padding: 2rem;
            border-radius: 24px;
            font-size: 1.8rem;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 15px 45px rgba(140, 208, 3, 0.4);
            transition: all 0.3s;
        }

        .pay-btn:active {
            transform: scale(0.96);
        }

        .back-btn {
            display: block;
            text-align: center;
            margin-top: 2rem;
            color: white;
            text-decoration: none;
            font-weight: 800;
            font-size: 1.2rem;
            opacity: 0.8;
        }

        /* Payment Animation Overlay */
        .payment-overlay {
            position: fixed;
            inset: 0;
            background: rgba(5, 54, 49, 0.95);
            backdrop-filter: blur(15px);
            z-index: 5000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        .payment-status {
            font-size: 2.5rem;
            font-weight: 900;
            margin-top: 2rem;
            animation: pulse 2s infinite;
        }

        .success-screen {
            text-align: center;
        }

        .order-number {
            font-size: 8rem;
            font-weight: 900;
            color: var(--color-light-green);
            margin: 2rem 0;
            text-shadow: 0 10px 40px rgba(140, 208, 3, 0.4);
        }

        .success-icon-wrapper {
            width: 200px;
            height: 200px;
            background: var(--color-light-green);
            border-radius: 50%;
            display: grid;
            place-items: center;
            margin: 0 auto 3rem;
            box-shadow: 0 20px 50px rgba(140, 208, 3, 0.3);
            color: var(--color-dark-blue);
        }

        .success-icon-wrapper svg {
            width: 100px;
            height: 100px;
        }

        .back-btn svg {
            transition: transform 0.2s;
        }

        .back-btn:hover svg {
            transform: translateX(-5px);
        }

        @media (max-width: 1000px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body class="menu-mode" style="background: var(--color-dark-blue); height: auto; overflow: auto;">

    <div class="checkout-container">
        <div class="checkout-header">
            <h1 style="color: white;">Rond uw bestelling af</h1>
            <p style="color: rgba(255,255,255,0.7); font-size: 1.3rem;">Bijna klaar! Controleer uw gerechten hieronder.</p>
        </div>

        <div class="checkout-grid">
            <div class="review-panel">
                <div class="review-title">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    Uw Mandje
                </div>
                <div id="checkout-items-list">
                    <!-- Loaded via JS -->
                </div>
            </div>

            <div class="summary-panel">
                <div class="type-selector">
                    <button class="type-btn active" id="type-eat-in" onclick="setType('eat_in')">Hier opeten</button>
                    <button class="type-btn" id="type-take-away" onclick="setType('take_away')">Meenemen</button>
                </div>

                <div class="total-box">
                    <div class="total-row">
                        <span>Totaal</span>
                        <span id="final-total">€0.00</span>
                    </div>
                    <button class="pay-btn" onclick="startPayment()">NU BETALEN</button>
                </div>
                <a href="menu.php" class="back-btn">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Terug naar het menu
                </a>
            </div>
        </div>
    </div>

    <!-- Hidden Payment UI -->
    <div id="payment-view" class="payment-overlay hidden">
        <div id="payment-instruction">
            <div class="payment-icon-wrapper">
                <svg viewBox="0 0 24 24" width="200" height="200" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                    <line x1="1" y1="10" x2="23" y2="10"></line>
                </svg>
            </div>
            <div class="payment-status">Volg de instructies op de pinautomaat...</div>
        </div>

        <div id="success-view" class="success-screen hidden">
            <div class="success-icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h2 style="font-size: 3rem;">Bedankt voor uw bestelling!</h2>
            <p style="font-size: 1.5rem; opacity: 0.8;">Uw bestelnummer is:</p>
            <div class="order-number" id="order-num-display">#042</div>
            <p style="font-size: 1.3rem;">U kunt uw bestelling ophalen bij de counter.</p>
            <button class="pay-btn" style="width: auto; margin-top: 3rem; padding: 1.5rem 4rem;" onclick="location.href='index.php'">NIEUWE BESTELLING</button>
        </div>
    </div>

    <script>
        let cart = JSON.parse(localStorage.getItem('kiosk_cart') || '[]');
        let orderType = 'eat_in';

        function renderCheckout() {
            const list = document.getElementById('checkout-items-list');
            const totalEl = document.getElementById('final-total');
            list.innerHTML = '';
            let total = 0;

            if (cart.length === 0) {
                location.href = 'menu.php';
                return;
            }

            cart.forEach(item => {
                total += item.price * item.quantity;
                const div = document.createElement('div');
                div.className = 'checkout-item';

                const extras = item.extras ? item.extras.map(e => e.name).join(', ') : '';

                div.innerHTML = `
                    <div class="checkout-item-info">
                        <h4>${item.name}</h4>
                        ${extras ? `<div class="checkout-item-extras">+ ${extras}</div>` : ''}
                        <div class="checkout-item-price">€${item.price.toFixed(2)}</div>
                    </div>
                    <div class="checkout-item-qty">x${item.quantity}</div>
                `;
                list.appendChild(div);
            });

            totalEl.innerText = '€' + total.toFixed(2);
        }

        function setType(type) {
            orderType = type;
            document.getElementById('type-eat-in').classList.toggle('active', type === 'eat_in');
            document.getElementById('type-take-away').classList.toggle('active', type === 'take_away');
        }

        async function startPayment() {
            document.getElementById('payment-view').classList.remove('hidden');

            // Mock payment delay
            setTimeout(async () => {
                document.getElementById('payment-instruction').classList.add('hidden');
                document.getElementById('success-view').classList.remove('hidden');

                // Generate order number
                const orderNum = Math.floor(Math.random() * 90) + 10;
                document.getElementById('order-num-display').innerText = '#' + (orderNum < 100 ? '0' + orderNum : orderNum);

                // Save to DB via fetch
                const total = cart.reduce((acc, i) => acc + (i.price * i.quantity), 0);

                try {
                    await fetch('api/save_order.php', {
                        method: 'POST',
                        body: JSON.stringify({
                            order_number: document.getElementById('order-num-display').innerText,
                            type: orderType,
                            total: total,
                            items: cart
                        })
                    });
                    // Clear cart
                    localStorage.removeItem('kiosk_cart');
                } catch (e) {
                    console.error(e);
                }

            }, 3000);
        }

        renderCheckout();
    </script>
    <script src="assets/js/responsive.js?v=<?php echo $verResponsiveJs; ?>"></script>
</body>

</html>
<?php
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Simulation - Happy Herbivore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .payment-container {
            text-align: center;
            padding: 4rem;
            max-width: 600px;
            margin: 10vh auto;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 2px solid var(--color-light-green);
        }
        .spinner {
            width: 80px;
            height: 80px;
            border: 8px solid var(--color-light-orange);
            border-top: 8px solid var(--color-orange);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 2rem auto;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .success-icon {
            font-size: 80px;
            color: var(--color-green);
            display: none;
        }
    </style>
</head>
<body>
    <div class="overlay">
        <div id="charity-step" class="payment-container fade-in">
            <h2>Wilt u een bijdrage leveren aan een betere wereld?</h2>
            <p>Kies een goed doel om uw ecologische voetafdruk te compenseren.</p>
            <div style="display: grid; gap: 1rem; margin-top: 2rem;">
                <button class="lang-btn" onclick="nextStep(0.50, 'Bosaanplant')">€0,50 - Bosaanplant</button>
                <button class="lang-btn" onclick="nextStep(1.00, 'Plastic Soup Foundation')">€1,00 - Plastic Soup</button>
                <button class="lang-btn" onclick="nextStep(0, 'Geen bijdrage')">Nee, bedankt</button>
            </div>
        </div>

        <div id="payment-step" class="payment-container fade-in" style="display:none;">
            <h1 id="pay-status">Verbinding maken met betaalterminal...</h1>
            <div id="pay-spinner" class="spinner"></div>
            <div id="pay-success" class="success-icon">✅</div>
            <p id="pay-msg">Houd uw pas tegen de lezer.</p>
        </div>
    </div>

    <script>
        function nextStep(extra, cause) {
            document.getElementById('charity-step').style.display = 'none';
            document.getElementById('payment-step').style.display = 'block';
            startPayment();
        }

        function startPayment() {
            setTimeout(() => {
                document.getElementById('pay-status').innerText = 'Betaling verwerken...';
                document.getElementById('pay-msg').innerText = 'Een moment geduld aub.';
            }, 2000);

            setTimeout(() => {
                document.getElementById('pay-status').innerText = 'Betaling geslaagd!';
                document.getElementById('pay-spinner').style.display = 'none';
                document.getElementById('pay-success').style.display = 'block';
                document.getElementById('pay-msg').innerText = 'Uw bestelling wordt voorbereid.';
                
                // Clear cart
                localStorage.removeItem('kiosk_cart');

                setTimeout(() => {
                    window.location.href = 'index.php'; // Back to start
                }, 3000);
            }, 5000);
        }
    </script>
</body>
</html>

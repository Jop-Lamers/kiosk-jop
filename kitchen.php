<?php
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Display - Happy Herbivore</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .kitchen-container {
            padding: 2rem;
        }
        .order-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .order-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
            border-left: 5px solid #ccc;
        }
        .order-card.status-2 { border-left-color: var(--color-orange); } /* Placed */
        .order-card.status-3 { border-left-color: var(--color-green); }  /* Preparing */
        
        .order-header {
            background: #eee;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }
        .order-body {
            padding: 1rem;
            min-height: 150px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        .order-footer {
            padding: 1rem;
            background: #f9f9f9;
            text-align: center;
        }
        .status-btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
        }
        .btn-start { background: var(--color-light-orange); color: var(--color-dark-blue); }
        .btn-done { background: var(--color-green); color: white; }
    </style>
</head>
<body>
    <div class="kitchen-container">
        <h1>Kitchen Display System</h1>
        <div id="orders-grid" class="order-grid">
            <!-- Populated by JS -->
        </div>
    </div>

    <script src="assets/js/kitchen.js"></script>
</body>
</html>

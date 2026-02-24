<?php
require_once 'config/db.php';

// Total Revenue
$rev_sql = "SELECT SUM(price_total) as total FROM orders WHERE order_status_id = 4 OR order_status_id = 5"; // Ready or Picked up (assuming paid)
$rev_result = $conn->query($rev_sql);
$revenue = $rev_result->fetch_assoc()['total'] ?? 0;

// Top Products
$top_sql = "SELECT p.name, SUM(op.quantity) as sold 
            FROM order_product op 
            JOIN products p ON op.product_id = p.product_id 
            JOIN orders o ON op.order_id = o.order_id
            WHERE o.order_status_id IN (2,3,4,5)
            GROUP BY op.product_id 
            ORDER BY sold DESC LIMIT 5";
$top_result = $conn->query($top_sql);

// Recent Orders
$recent_sql = "SELECT * FROM orders ORDER BY datetime DESC LIMIT 10";
$recent_result = $conn->query($recent_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Happy Herbivore</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-container { padding: 3rem; max-width: 1200px; margin: 0 auto; }
        .stats-row { display: flex; gap: 2rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); flex: 1; }
        .stat-card h3 { margin-top: 0; color: #666; }
        .stat-card .value { font-size: 2.5rem; font-weight: bold; color: var(--color-orange); }
        
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: var(--color-dark-blue); color: white; }
    </style>
</head>
<body style="background: #f4f4f4; color: #333;">

    <div class="admin-container">
        <h1>Dashboard & Analytics</h1>
        
        <div class="stats-row">
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <div class="value">€<?php echo number_format($revenue, 2); ?></div>
            </div>
            <div class="stat-card">
                <h3>Top Selling Product</h3>
                <div class="value" style="font-size: 1.5rem;">
                    <?php 
                    if ($top_result->num_rows > 0) {
                        $top = $top_result->fetch_assoc();
                        echo htmlspecialchars($top['name']) . ' (' . $top['sold'] . ')';
                        // Reset pointer for table below
                        $top_result->data_seek(0);
                    } else {
                        echo "No data";
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="stats-row">
            <div style="flex: 1;">
                <h2>Top Products</h2>
                <table>
                    <thead><tr><th>Product</th><th>Sold</th></tr></thead>
                    <tbody>
                        <?php while($row = $top_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo $row['sold']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="flex: 1;">
                <h2>Recent Orders</h2>
                <table>
                    <thead><tr><th>ID</th><th>Time</th><th>Total</th></tr></thead>
                    <tbody>
                        <?php while($row = $recent_result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['pickup_number']; ?></td>
                                <td><?php echo date('H:i d/m', strtotime($row['datetime'])); ?></td>
                                <td>€<?php echo $row['price_total']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>

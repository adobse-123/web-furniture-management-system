<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_report.php';

$monthly = get_monthly_revenue();
$top = top_customers_by_orders(10);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reports</title>
    <link href="/assets/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h1>Reports</h1>
<h3>Monthly Revenue (Current Year)</h3>
<table class="table">
    <thead><tr><th>Month</th><th>Revenue</th></tr></thead>
    <tbody>
    <?php foreach ($monthly as $m): ?>
        <tr><td><?php echo htmlspecialchars($m['month']); ?></td><td><?php echo htmlspecialchars($m['revenue']); ?></td></tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h3>Top Customers</h3>
<table class="table">
    <thead><tr><th>Customer</th><th>Orders</th><th>Total Spent</th></tr></thead>
    <tbody>
    <?php foreach ($top as $t): ?>
        <tr><td><?php echo htmlspecialchars($t['name']); ?></td><td><?php echo htmlspecialchars($t['orders_count']); ?></td><td><?php echo htmlspecialchars($t['total_spent']); ?></td></tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>

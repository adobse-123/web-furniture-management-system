<?php
require_once __DIR__ . '/../includes/check_customer.php';
require_once __DIR__ . '/../includes/functions_order.php';

$customer_id = $_SESSION['user_id'] ?? null;
$orders = [];
if ($customer_id) {
    $orders = get_orders(['customer_id' => $customer_id]);
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Orders</title>
    <link href="/assets/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h1>My Orders</h1>
<table class="table">
    <thead><tr><th>#</th><th>Date</th><th>Status</th><th>Total</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($orders as $o): ?>
        <tr>
            <td><?php echo htmlspecialchars($o['id']); ?></td>
            <td><?php echo htmlspecialchars($o['created_at'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($o['status']); ?></td>
            <td><?php echo htmlspecialchars($o['total_amount']); ?></td>
            <td><a href="../admin/order_view.php?id=<?php echo $o['id']; ?>" class="btn btn-sm btn-secondary">View</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>

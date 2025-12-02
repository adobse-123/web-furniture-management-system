<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_order.php';
require_once __DIR__ . '/../includes/functions_product.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: orders.php');
    exit;
}
$order = get_order_by_id($id);
if (!$order) {
    echo 'Order not found';
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order #<?php echo $order['id']; ?></title>
    <link href="/assets/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h1>Order #<?php echo $order['id']; ?></h1>
<p>Customer: <?php echo htmlspecialchars($order['customer_name']); ?> (<?php echo htmlspecialchars($order['customer_email']); ?>)</p>
<p>Status: <?php echo htmlspecialchars($order['status']); ?></p>
<h3>Items</h3>
<table class="table">
    <thead><tr><th>Product</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead>
    <tbody>
    <?php foreach ($order['items'] as $it): ?>
        <tr>
            <td><?php echo htmlspecialchars($it['product_name']); ?></td>
            <td><?php echo htmlspecialchars($it['quantity']); ?></td>
            <td><?php echo htmlspecialchars($it['unit_price']); ?></td>
            <td><?php echo htmlspecialchars($it['total_price']); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<h4>Payments</h4>
<?php if (!empty($order['payments'])): ?>
    <ul>
        <?php foreach ($order['payments'] as $p): ?>
            <li><?php echo htmlspecialchars($p['amount']); ?> on <?php echo htmlspecialchars($p['created_at']); ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No payments recorded</p>
<?php endif; ?>

<div class="mt-3">
    <a href="order_edit.php?id=<?php echo $order['id']; ?>" class="btn btn-warning">Edit</a>
    <a href="order_produce.php?order_id=<?php echo $order['id']; ?>" class="btn btn-primary">Go to Production</a>
    <a href="payments.php?order_id=<?php echo $order['id']; ?>" class="btn btn-info">Record Payment</a>
</div>
</body>
</html>

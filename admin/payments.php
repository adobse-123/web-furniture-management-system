<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_order.php';

$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    header('Location: orders.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $method = $_POST['method'] ?? 'Manual';
    add_payment($order_id, $amount, $method, $_SESSION['user_id'] ?? null);
    header('Location: order_view.php?id=' . $order_id);
    exit;
}
$order = get_order_by_id($order_id);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payments for Order #<?php echo $order_id; ?></title>
    <link href="/assets/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h1>Record Payment for Order #<?php echo $order_id; ?></h1>
<p>Order Total: <?php echo htmlspecialchars($order['total_amount'] ?? ''); ?></p>
<form method="post">
    <div class="mb-3">
        <label class="form-label">Amount</label>
        <input type="number" step="0.01" name="amount" class="form-control" required />
    </div>
    <div class="mb-3">
        <label class="form-label">Method</label>
        <input type="text" name="method" class="form-control" value="Cash" />
    </div>
    <button class="btn btn-primary">Record Payment</button>
</form>
</body>
</html>

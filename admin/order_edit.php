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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'delivery_date' => $_POST['delivery_date'] ?? null,
        'notes' => $_POST['notes'] ?? '',
    ];
    update_order($id, $data);
    header('Location: order_view.php?id=' . $id);
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Order #<?php echo $order['id']; ?></title>
    <link href="/assets/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h1>Edit Order #<?php echo $order['id']; ?></h1>
<form method="post">
    <div class="mb-3">
        <label class="form-label">Delivery Date</label>
        <input type="date" name="delivery_date" class="form-control" value="<?php echo htmlspecialchars($order['delivery_date'] ?? ''); ?>" />
    </div>
    <div class="mb-3">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control"><?php echo htmlspecialchars($order['notes'] ?? ''); ?></textarea>
    </div>
    <button class="btn btn-primary">Save</button>
</form>
</body>
</html>

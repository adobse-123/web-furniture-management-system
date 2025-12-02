<?php
require_once __DIR__ . '/../includes/check_employee.php';
require_once __DIR__ . '/../includes/functions_order.php';

$employee_id = $_SESSION['user_id'] ?? null;
$orders = [];
if ($employee_id) {
    $orders = get_orders(['status' => 'In Progress']);
    // filter assigned to this employee
    $orders = array_filter($orders, function($o) use ($employee_id) { return ($o['assigned_employee_id'] == $employee_id); });
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
<h1>My Assigned Orders</h1>
<table class="table">
    <thead><tr><th>#</th><th>Customer</th><th>Status</th><th>Due</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($orders as $o): ?>
        <tr>
            <td><?php echo htmlspecialchars($o['id']); ?></td>
            <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
            <td><?php echo htmlspecialchars($o['status']); ?></td>
            <td><?php echo htmlspecialchars($o['delivery_date'] ?? ''); ?></td>
            <td><a href="../admin/order_view.php?id=<?php echo $o['id']; ?>" class="btn btn-sm btn-secondary">View</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>

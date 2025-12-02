<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_order.php';
$orders = get_orders();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders</title>
    <link href="/assets/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h1>Orders</h1>
<p><a href="order_create.php" class="btn btn-primary">Create New Order</a></p>
<table class="table table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th>Customer</th>
        <th>Date</th>
        <th>Status</th>
        <th>Total</th>
        <th>Assigned</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($orders as $o): ?>
        <tr>
            <td><?php echo htmlspecialchars($o['id']); ?></td>
            <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
            <td><?php echo htmlspecialchars($o['created_at'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($o['status']); ?></td>
            <td><?php echo htmlspecialchars($o['total_amount']); ?></td>
            <td><?php echo htmlspecialchars($o['assigned_employee_id'] ?? ''); ?></td>
            <td>
                <a href="order_view.php?id=<?php echo $o['id']; ?>" class="btn btn-sm btn-secondary">View</a>
                <a href="order_edit.php?id=<?php echo $o['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="payments.php?order_id=<?php echo $o['id']; ?>" class="btn btn-sm btn-info">Payments</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>

<?php
session_start();
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_stock.php';

$materials = get_all_materials();
$totalItems = get_total_items_count();
$lowCount = get_low_stock_count();
$totalValue = get_total_stock_value();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Raw Materials - Admin</title>
    <link href="/assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Raw Materials</h2>
        <div>
            <a href="stock_create.php" class="btn btn-primary">Add New Material</a>
            <a href="suppliers.php" class="btn btn-secondary">View Suppliers</a>
            <a href="stock_transactions.php" class="btn btn-info">Stock In/Out</a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card p-3">
                <strong>Total Items</strong>
                <div class="fs-4"><?php echo $totalItems; ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3">
                <strong>Low Stock Items</strong>
                <div class="fs-4 text-danger"><?php echo $lowCount; ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3">
                <strong>Total Stock Value</strong>
                <div class="fs-4">$<?php echo number_format($totalValue,2); ?></div>
            </div>
        </div>
    </div>

    <table id="materialsTable" class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Alert Level</th>
            <th>Unit Price</th>
            <th>Value</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($materials as $m):
            $isLow = ($m['current_quantity'] <= $m['alert_threshold']);
            ?>
            <tr class="<?php echo $isLow ? 'table-danger':''; ?>">
                <td><?php echo $m['id']; ?></td>
                <td><?php echo htmlspecialchars($m['name']); ?></td>
                <td><?php echo $m['current_quantity']; ?></td>
                <td><?php echo htmlspecialchars($m['unit']); ?></td>
                <td><?php echo $m['alert_threshold']; ?></td>
                <td>$<?php echo number_format($m['unit_price'],2); ?></td>
                <td>$<?php echo number_format($m['current_quantity'] * $m['unit_price'],2); ?></td>
                <td>
                    <a href="stock_edit.php?id=<?php echo $m['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="stock_transactions.php?material_id=<?php echo $m['id']; ?>" class="btn btn-sm btn-info">Transact</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function(){
        $('#materialsTable').DataTable();
    });
</script>
</body>
</html>


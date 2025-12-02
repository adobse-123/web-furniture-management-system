<?php
session_start();
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_stock.php';

$materials = get_all_materials();
$suppliers = get_suppliers();
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? 'IN';
    $material_id = intval($_POST['material_id'] ?? 0);
    $quantity = floatval($_POST['quantity'] ?? 0);
    $price = floatval($_POST['price_per_unit'] ?? 0);
    $supplier_id = !empty($_POST['supplier_id']) ? intval($_POST['supplier_id']) : null;
    $order_id = !empty($_POST['order_id']) ? intval($_POST['order_id']) : null;
    $notes = $_POST['notes'] ?? null;
    if ($material_id && $quantity > 0) {
        $ok = record_stock_transaction([
            'material_id'=>$material_id,
            'type'=>$type,
            'quantity'=>$quantity,
            'price_per_unit'=>$price,
            'supplier_id'=>$supplier_id,
            'order_id'=>$order_id,
            'notes'=>$notes
        ]);
        if ($ok) {
            header('Location: stock.php'); exit;
        } else {
            $error = 'Failed to record transaction.';
        }
    } else {
        $error = 'Select material and enter valid quantity.';
    }
}

$transactions = get_stock_transactions();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<div class="container mt-4">
    <h3>Stock Transactions</h3>
    <?php if ($error): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
    <form method="post" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Mode</label>
            <select name="type" class="form-select">
                <option value="IN">Stock IN (from Supplier)</option>
                <option value="OUT">Stock OUT (to Production)</option>
                <option value="ADJUST">ADJUST</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Material</label>
            <select name="material_id" class="form-select">
                <?php foreach($materials as $m): ?>
                    <option value="<?=$m['id']?>"><?=htmlspecialchars($m['name'])?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Quantity</label>
            <input name="quantity" type="number" step="any" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Price/Unit</label>
            <input name="price_per_unit" type="number" step="0.01" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Supplier (optional)</label>
            <select name="supplier_id" class="form-select">
                <option value="">--</option>
                <?php foreach($suppliers as $s): ?>
                    <option value="<?=$s['id']?>"><?=htmlspecialchars($s['name'])?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Notes / Order ID</label>
            <input name="order_id" class="form-control" placeholder="Order ID (for OUT links)">
            <textarea name="notes" class="form-control mt-2" placeholder="Notes"></textarea>
        </div>
        <div class="col-12">
            <button class="btn btn-primary">Record Transaction</button>
            <a href="stock.php" class="btn btn-secondary">Back</a>
        </div>
    </form>

    <h4>Recent Transactions</h4>
    <table id="txTable" class="table table-striped">
        <thead><tr><th>ID</th><th>Material</th><th>Type</th><th>Qty</th><th>Price</th><th>Supplier</th><th>Order</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach($transactions as $t): ?>
            <tr>
                <td><?=$t['id']?></td>
                <td><?=htmlspecialchars($t['material_name'])?></td>
                <td><?=$t['type']?></td>
                <td><?=$t['quantity']?></td>
                <td><?=$t['price_per_unit']?></td>
                <td><?=htmlspecialchars($t['supplier_name'])?></td>
                <td><?=$t['order_id']?></td>
                <td><?=$t['created_at']?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>$(document).ready(function(){ $('#txTable').DataTable(); });</script>
</body>
</html>
<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_stock.php';

$materials = get_all_materials();
$suppliers = get_all_suppliers();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'material_id' => $_POST['material_id'],
        'type' => $_POST['type'],
        'quantity' => $_POST['quantity'],
        'supplier_id' => $_POST['supplier_id'] ?? null,
        'unit_price' => $_POST['unit_price'] ?? null,
        'notes' => $_POST['notes'] ?? null,
        'order_id' => $_POST['order_id'] ?? null,
    ];
    if ($_POST['type'] === 'ADJUST') {
        if (isset($_POST['new_quantity'])) { $data['new_quantity'] = $_POST['new_quantity']; }
        if (isset($_POST['adjustment'])) { $data['adjustment'] = $_POST['adjustment']; }
    }
    create_stock_transaction($data);
    header('Location: stock_transactions.php'); exit;
}

$transactions = get_stock_transactions();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Stock Transactions</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="p-4">
<h1>Stock Transactions</h1>
<div class="row">
    <div class="col-md-6">
        <form method="post">
            <div class="mb-2"><label>Type</label><select name="type" class="form-control"><option>IN</option><option>OUT</option><option>ADJUST</option></select></div>
            <div class="mb-2"><label>Material</label><select name="material_id" class="form-control"><?php foreach($materials as $m) echo "<option value='{$m['id']}'>".htmlspecialchars($m['name'])."</option>"; ?></select></div>
            <div class="mb-2"><label>Quantity</label><input name="quantity" type="number" step="any" class="form-control" value="0"></div>
            <div class="mb-2"><label>Supplier (IN)</label><select name="supplier_id" class="form-control"><option value="">--</option><?php foreach($suppliers as $s) echo "<option value='{$s['id']}'>".htmlspecialchars($s['name'])."</option>"; ?></select></div>
            <div class="mb-2"><label>Unit Price (optional)</label><input name="unit_price" type="number" step="0.01" class="form-control"></div>
            <div class="mb-2"><label>Order ID (for OUT)</label><input name="order_id" class="form-control"></div>
            <div class="mb-2"><label>Notes</label><textarea name="notes" class="form-control"></textarea></div>
            <div class="mb-2"><label>Adjust new quantity (for ADJUST)</label><input name="new_quantity" class="form-control"></div>
            <div class="mb-2"><label>Adjustment +/- (for ADJUST)</label><input name="adjustment" class="form-control"></div>
            <button class="btn btn-primary">Record Transaction</button>
        </form>
    </div>
    <div class="col-md-6">
        <h4>Recent Transactions</h4>
        <table class="table table-sm">
            <thead><tr><th>Date</th><th>Material</th><th>Type</th><th>Qty</th><th>Supplier</th><th>Notes</th></tr></thead>
            <tbody>
            <?php foreach($transactions as $t): ?>
                <tr>
                    <td><?= $t['created_at'] ?></td>
                    <td><?= htmlspecialchars($t['material_name']) ?></td>
                    <td><?= $t['type'] ?></td>
                    <td><?= $t['quantity'] ?></td>
                    <td><?= htmlspecialchars($t['supplier_name']) ?></td>
                    <td><?= htmlspecialchars($t['notes']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body></html>

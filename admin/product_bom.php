<?php
session_start();
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_product.php';
require_once __DIR__ . '/../includes/functions_stock.php';

$product_id = intval($_GET['id'] ?? 0);
if (!$product_id) { header('Location: products.php'); exit; }
$product = get_product_by_id($product_id);
if (!$product) { header('Location: products.php'); exit; }

//$ Handle adding/removing BOM items
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_bom'])) {
        $material_id = intval($_POST['material_id'] ?? 0);
        $qty = floatval($_POST['quantity'] ?? 0);
        if ($material_id && $qty > 0) {
            add_bom_item($product_id, $material_id, $qty);
            header('Location: product_bom.php?id='.$product_id); exit;
        } else {
            $error = 'Select material and enter quantity.';
        }
    }
    if (isset($_POST['remove_bom'])) {
        $bom_id = intval($_POST['bom_id'] ?? 0);
        if ($bom_id) { remove_bom_item($bom_id); header('Location: product_bom.php?id='.$product_id); exit; }
    }
}

$bom = get_bom_for_product($product_id);
$materials = get_all_materials();
$estimated_cost = calculate_bom_cost($product_id);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>BOM - <?=htmlspecialchars($product['name'] ?? $product['product_name'] ?? '')?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<div class="container mt-4">
    <h3>Bill of Materials for: <?=htmlspecialchars($product['name'] ?? $product['product_name'] ?? '')?></h3>
    <?php if ($error): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-6">
            <form method="post" class="row g-2">
                <div class="col-7">
                    <select name="material_id" class="form-select">
                        <option value="">-- Select material --</option>
                        <?php foreach($materials as $m): ?>
                            <option value="<?=$m['id']?>"><?=htmlspecialchars($m['name'] ?? $m['material_name'] ?? '')?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-3"><input name="quantity" class="form-control" placeholder="Qty required"></div>
                <div class="col-2"><button name="add_bom" class="btn btn-primary">Add</button></div>
            </form>
        </div>
        <div class="col-md-6 text-end">
            <strong>Estimated BOM Cost:</strong> $<?=number_format($estimated_cost,2)?></div>
    </div>

    <table id="bomTable" class="table table-striped">
        <thead><tr><th>ID</th><th>Material</th><th>Qty Required</th><th>Unit Price</th><th>Cost</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach($bom as $b):
            $unit_price = $b['unit_price'] ?? $b['unit_price'] ?? 0;
            $cost = ($b['quantity_required'] ?? 0) * ($unit_price);
        ?>
            <tr>
                <td><?=$b['id']?></td>
                <td><?=htmlspecialchars($b['material_name'] ?? $b['name'] ?? '')?></td>
                <td><?=$b['quantity_required']?></td>
                <td>$<?=number_format($unit_price,2)?></td>
                <td>$<?=number_format($cost,2)?></td>
                <td>
                    <form method="post" style="display:inline-block" onsubmit="return confirm('Remove material from BOM?')">
                        <input type="hidden" name="bom_id" value="<?=$b['id']?>">
                        <button name="remove_bom" class="btn btn-sm btn-danger">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>$(function(){ $('#bomTable').DataTable(); });</script>
</body>
</html>


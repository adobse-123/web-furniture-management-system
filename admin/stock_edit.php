<?php
session_start();
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_stock.php';

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: stock.php'); exit; }
$material = get_material_by_id($id);
if (!$material) { header('Location: stock.php'); exit; }

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'unit' => $_POST['unit'] ?? '',
        'current_quantity' => floatval($_POST['current_quantity'] ?? 0),
        'alert_threshold' => floatval($_POST['alert_threshold'] ?? 0),
        'unit_price' => floatval($_POST['unit_price'] ?? 0),
    ];
    if (update_material($id, $data)) {
        header('Location: stock.php'); exit;
    } else {
        $error = 'Failed to update material.';
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Material</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<div class="container mt-4">
    <h3>Edit Raw Material</h3>
    <?php if ($error): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Material Name</label>
            <input name="name" class="form-control" value="<?=htmlspecialchars($material['name'])?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"><?=htmlspecialchars($material['description'])?></textarea>
        </div>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Unit</label>
                <input name="unit" class="form-control" value="<?=htmlspecialchars($material['unit'])?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Quantity</label>
                <input name="current_quantity" type="number" step="any" class="form-control" value="<?=htmlspecialchars($material['current_quantity'])?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Alert Threshold</label>
                <input name="alert_threshold" type="number" step="any" class="form-control" value="<?=htmlspecialchars($material['alert_threshold'])?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Unit Price</label>
                <input name="unit_price" type="number" step="0.01" class="form-control" value="<?=htmlspecialchars($material['unit_price'])?>">
            </div>
        </div>
        <button class="btn btn-primary">Save</button>
        <a href="stock.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>

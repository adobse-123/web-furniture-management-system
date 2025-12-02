<?php
session_start();
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_stock.php';

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
    if (empty($data['name'])) {
        $error = 'Material name is required.';
    } else {
        if (create_material($data)) {
            header('Location: stock.php'); exit;
        } else {
            $error = 'Failed to create material.';
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Add Material</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<div class="container mt-4">
    <h3>Add New Raw Material</h3>
    <?php if ($error): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Material Name</label>
            <input name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Unit</label>
                <input name="unit" class="form-control">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Initial Quantity</label>
                <input name="current_quantity" type="number" step="any" class="form-control" value="0">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Alert Threshold</label>
                <input name="alert_threshold" type="number" step="any" class="form-control" value="0">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Unit Price</label>
                <input name="unit_price" type="number" step="0.01" class="form-control" value="0.00">
            </div>
        </div>
        <button class="btn btn-primary">Create</button>
        <a href="stock.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_stock.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'],
        'description' => $_POST['description'] ?? null,
        'unit' => $_POST['unit'] ?? null,
        'initial_quantity' => $_POST['initial_quantity'] ?? 0,
        'alert_threshold' => $_POST['alert_threshold'] ?? 0,
        'unit_price' => $_POST['unit_price'] ?? 0.0,
    ];
    create_material($data);
    header('Location: stock.php'); exit;
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Add Material</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-4">
<h1>Add New Raw Material</h1>
<form method="post">
    <div class="mb-3"><label>Name</label><input name="name" required class="form-control"></div>
    <div class="mb-3"><label>Description</label><textarea name="description" class="form-control"></textarea></div>
    <div class="mb-3"><label>Unit</label><input name="unit" class="form-control"></div>
    <div class="mb-3"><label>Initial Quantity</label><input name="initial_quantity" type="number" step="any" class="form-control" value="0"></div>
    <div class="mb-3"><label>Alert Threshold</label><input name="alert_threshold" type="number" class="form-control" value="0"></div>
    <div class="mb-3"><label>Unit Price</label><input name="unit_price" type="number" step="0.01" class="form-control" value="0.00"></div>
    <button class="btn btn-primary">Create</button>
    <a class="btn btn-secondary" href="stock.php">Cancel</a>
</form>
</body></html>

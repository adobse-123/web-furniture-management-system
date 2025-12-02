<?php
session_start();
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_product.php';
require_once __DIR__ . '/../includes/functions_stock.php';
require_once __DIR__ . '/../config/database.php';

$order_id = intval($_GET['order_id'] ?? 0);
if (!$order_id) { header('Location: orders.php'); exit; }

$pdo = get_pdo();
$stmt = $pdo->prepare('SELECT o.*, c.id as customer_id, u.full_name as customer_name FROM orders o LEFT JOIN customers c ON o.customer_id = c.id LEFT JOIN users u ON c.user_id = u.id WHERE o.id = ?');
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) { header('Location: orders.php'); exit; }

// load items
$stmt = $pdo->prepare('SELECT oi.*, p.product_code, p.name as product_name FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?');
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// compute required materials for the whole order
$required = [];
foreach ($items as $it) {
    $bom = get_bom_for_product($it['product_id']);
    foreach ($bom as $b) {
        $mat_id = $b['raw_material_id'] ?? $b['material_id'] ?? $b['raw_material_id'];
        $qty_required_per_unit = $b['quantity_required'] ?? $b['quantity_required'];
        $total_needed = $qty_required_per_unit * $it['quantity'];
        if (!isset($required[$mat_id])) $required[$mat_id] = 0;
        $required[$mat_id] += $total_needed;
    }
}

// helper to check availability
function check_availability($required)
{
    $ok = true;
    foreach ($required as $mid => $qty) {
        $m = get_material_by_id($mid);
        if (!$m || ($m['current_quantity'] ?? $m['current_quantity'] ?? 0) < $qty) { $ok = false; break; }
    }
    return $ok;
}

$can_start = check_availability($required);

$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_in_progress'])) {
        $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute(['In Progress', $order_id]);
        $message = 'Order marked In Progress.';
        $order['status'] = 'In Progress';
    }
    if (isset($_POST['mark_completed'])) {
        // Re-check availability then create OUT transactions and update order
        if (!check_availability($required)) {
            $message = 'Insufficient materials; cannot complete.';
        } else {
            try {
                $pdo->beginTransaction();
                foreach ($required as $mid => $qty) {
                    // create OUT transaction
                    $ok = record_stock_transaction([
                        'material_id'=>$mid,
                        'type'=>'OUT',
                        'quantity'=>$qty,
                        'price_per_unit'=>null,
                        'supplier_id'=>null,
                        'order_id'=>$order_id,
                        'notes'=>'Production consumption for order #'.$order['order_number'],
                        'performed_by'=>$_SESSION['user_id'] ?? null
                    ]);
                    if (!$ok) throw new Exception('Failed to record transaction');
                }
                $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute(['Completed', $order_id]);
                $pdo->commit();
                $message = 'Order completed and materials deducted from stock.';
                $order['status'] = 'Completed';
            } catch (Exception $e) {
                $pdo->rollBack();
                $message = 'Error completing order: '.$e->getMessage();
            }
        }
    }
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Produce Order #<?=htmlspecialchars($order['order_number'])?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<div class="container mt-4">
    <h3>Production - Order #<?=htmlspecialchars($order['order_number'])?></h3>
    <?php if ($message): ?><div class="alert alert-info"><?=htmlspecialchars($message)?></div><?php endif; ?>
    <div class="mb-3">
        <strong>Customer:</strong> <?=htmlspecialchars($order['customer_name'] ?? '')?><br>
        <strong>Status:</strong> <?=htmlspecialchars($order['status'])?> <br>
        <strong>Order Date:</strong> <?=htmlspecialchars($order['order_date'])?> 
    </div>

    <h5>Order Items</h5>
    <table class="table table-sm">
        <thead><tr><th>Product</th><th>Qty</th></tr></thead>
        <tbody>
        <?php foreach($items as $it): ?>
            <tr><td><?=htmlspecialchars($it['product_name'])?></td><td><?=$it['quantity']?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h5>Materials Required</h5>
    <table class="table table-sm">
        <thead><tr><th>Material</th><th>Required Qty</th><th>On Hand</th></tr></thead>
        <tbody>
        <?php foreach($required as $mid => $qty):
            $m = get_material_by_id($mid);
        ?>
            <tr class="<?=($m['current_quantity'] < $qty)?'table-danger':''?>">
                <td><?=htmlspecialchars($m['name'] ?? $m['material_name'] ?? '')?></td>
                <td><?=$qty?></td>
                <td><?=number_format($m['current_quantity'] ?? 0,2)?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <form method="post">
        <div class="d-flex gap-2">
            <button name="mark_in_progress" class="btn btn-warning">Mark as In Progress</button>
            <button name="mark_completed" class="btn btn-success" <?= $can_start ? '' : 'disabled' ?>>Mark as Completed</button>
            <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
        </div>
    </form>
</div>
</body>
</html>


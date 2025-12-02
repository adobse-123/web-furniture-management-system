<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_order.php';
require_once __DIR__ . '/../includes/functions_product.php';
require_once __DIR__ . '/../includes/functions_customers.php';

$pdo = get_pdo();
$products = get_all_products();
$customers = $pdo->query('SELECT id, name FROM customers ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'] ?? null;
    $delivery_date = $_POST['delivery_date'] ?? null;
    $notes = $_POST['notes'] ?? '';
    $advance = floatval($_POST['advance_payment'] ?? 0);
    $items = [];
    if (!empty($_POST['items']) && is_array($_POST['items'])) {
        foreach ($_POST['items'] as $it) {
            // expected: product_id, quantity, unit_price
            $items[] = [
                'product_id' => $it['product_id'],
                'quantity' => (int)$it['quantity'],
                'unit_price' => (float)$it['unit_price'],
            ];
        }
    }
    $total = 0; foreach ($items as $i) $total += $i['quantity'] * $i['unit_price'];
    $orderId = create_order([
        'customer_id' => $customer_id,
        'delivery_date' => $delivery_date,
        'notes' => $notes,
        'advance_payment' => $advance,
        'total_amount' => $total,
        'created_by' => $_SESSION['user_id'] ?? null,
    ], $items);
    header('Location: order_view.php?id=' . $orderId);
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Create Order</title>
    <link href="/assets/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h1>Create Order</h1>
<form method="post">
    <div class="mb-3">
        <label class="form-label">Customer</label>
        <select name="customer_id" class="form-select" required>
            <?php foreach ($customers as $c): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <h5>Items</h5>
    <div id="items">
        <div class="item-row mb-2">
            <select name="items[0][product_id]" class="form-select d-inline-block" style="width:40%">
                <?php foreach ($products as $p): ?>
                    <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="items[0][quantity]" value="1" class="form-control d-inline-block" style="width:20%" />
            <input type="text" name="items[0][unit_price]" value="0" class="form-control d-inline-block" style="width:20%" />
        </div>
    </div>
    <button type="button" id="addItem" class="btn btn-sm btn-outline-primary">Add Item</button>

    <div class="mb-3 mt-3">
        <label class="form-label">Delivery Date</label>
        <input type="date" name="delivery_date" class="form-control" />
    </div>
    <div class="mb-3">
        <label class="form-label">Advance Payment</label>
        <input type="number" step="0.01" name="advance_payment" class="form-control" />
    </div>
    <div class="mb-3">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control"></textarea>
    </div>
    <button class="btn btn-primary">Create Order</button>
</form>

<script>
// simple client-side add item logic
let idx = 1;
document.getElementById('addItem').addEventListener('click', () => {
    const tpl = document.querySelector('.item-row').cloneNode(true);
    tpl.querySelectorAll('select, input').forEach(el => {
        const name = el.getAttribute('name');
        if (!name) return;
        const newName = name.replace(/items\[0\]/, 'items[' + idx + ']');
        el.setAttribute('name', newName);
        if (el.tagName === 'INPUT') el.value = el.type === 'number' ? '1' : '0';
    });
    document.getElementById('items').appendChild(tpl);
    idx++;
});
</script>
</body>
</html>

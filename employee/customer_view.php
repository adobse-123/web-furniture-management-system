<?php
require_once __DIR__ . '/../includes/check_employee.php';
require_once __DIR__ . '/../includes/functions_customer.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: customers.php'); exit; }
$customer = customer_get_by_id($id);
if (!$customer) { header('Location: customers.php'); exit; }

// fetch orders (simplified)
$pdo = get_pdo();
$stmt = $pdo->prepare('SELECT id, order_number, order_date, status, total_amount FROM orders WHERE customer_id = :cid ORDER BY order_date DESC');
$stmt->execute(['cid' => $id]);
$orders = $stmt->fetchAll();

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>
    <div class="container py-4">
      <h3>Customer Info</h3>
      <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Name:</strong> <?=htmlspecialchars($customer['full_name'])?></li>
        <li class="list-group-item"><strong>Email:</strong> <?=htmlspecialchars($customer['email'])?></li>
        <li class="list-group-item"><strong>Phone:</strong> <?=htmlspecialchars($customer['phone'])?></li>
        <li class="list-group-item"><strong>Company:</strong> <?=htmlspecialchars($customer['company_name'])?></li>
      </ul>

      <h4>Recent Orders</h4>
      <table class="table table-sm">
        <thead><tr><th>Order #</th><th>Date</th><th>Status</th><th>Total</th></tr></thead>
        <tbody>
        <?php foreach($orders as $o): ?>
          <tr>
            <td><?=htmlspecialchars($o['order_number'])?></td>
            <td><?=htmlspecialchars($o['order_date'])?></td>
            <td><?=htmlspecialchars($o['status'])?></td>
            <td><?=number_format($o['total_amount'],2)?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php include __DIR__ . '/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>

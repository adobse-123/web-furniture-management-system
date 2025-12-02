<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_customer.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: customers.php'); exit; }
$customer = customer_get_by_id($id);
if (!$customer) { header('Location: customers.php'); exit; }

// fetch orders
$pdo = get_pdo();
$stmt = $pdo->prepare('SELECT id, order_number, order_date, status, total_amount FROM orders WHERE customer_id = :cid ORDER BY order_date DESC');
$stmt->execute(['cid' => $id]);
$orders = $stmt->fetchAll();

// calculate payment summary
$totalSpent = 0; $advance = 0;
foreach ($orders as $o) { $totalSpent += $o['total_amount'] ?? 0; }

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
      <div class="row">
        <div class="col-md-6">
          <h3>Customer Info</h3>
          <ul class="list-group">
            <li class="list-group-item"><strong>Name:</strong> <?=htmlspecialchars($customer['full_name'])?></li>
            <li class="list-group-item"><strong>Email:</strong> <?=htmlspecialchars($customer['email'])?></li>
            <li class="list-group-item"><strong>Phone:</strong> <?=htmlspecialchars($customer['phone'])?></li>
            <li class="list-group-item"><strong>Company:</strong> <?=htmlspecialchars($customer['company_name'])?></li>
            <li class="list-group-item"><strong>Address:</strong> <?=nl2br(htmlspecialchars($customer['address']))?></li>
            <li class="list-group-item"><strong>Tax No:</strong> <?=htmlspecialchars($customer['tax_number'])?></li>
          </ul>
          <div class="mt-3">
            <a class="btn btn-secondary" href="customer_edit.php?id=<?=$id?>">Edit</a>
            <a class="btn btn-outline-primary" href="customers.php">Back</a>
          </div>
        </div>

        <div class="col-md-6">
          <h3>Orders</h3>
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
          <h5>Payment Summary</h5>
          <p>Total Spent: <?=number_format($totalSpent,2)?></p>
        </div>
      </div>
    </div>
    <?php include __DIR__ . '/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>

<?php
require_once __DIR__ . '/../includes/check_employee.php';
require_once __DIR__ . '/../includes/functions_customer.php';

$customers = customer_get_all();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customers - Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>
    <div class="container py-4">
      <h2>Customers</h2>
      <table class="table table-striped">
        <thead><tr><th>Name</th><th>Company</th><th>Phone</th><th>Email</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach($customers as $c): ?>
            <tr>
              <td><?=htmlspecialchars($c['full_name'])?></td>
              <td><?=htmlspecialchars($c['company_name'])?></td>
              <td><?=htmlspecialchars($c['phone'])?></td>
              <td><?=htmlspecialchars($c['email'])?></td>
              <td>
                <a class="btn btn-sm btn-outline-primary" href="customer_view.php?id=<?=$c['customer_id']?>">View</a>
                <a class="btn btn-sm btn-outline-secondary" href="customer_edit.php?id=<?=$c['customer_id']?>">Edit</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php include __DIR__ . '/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>

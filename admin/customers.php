<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_customer.php';
// Fetch customers
$customers = customer_get_all();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customers - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="/assets/css/dashboard.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>
    <div class="container-fluid px-4 py-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Customers</h2>
        <a class="btn btn-primary" href="customer_create.php">Add New Customer</a>
      </div>

      <div class="card">
        <div class="card-body">
          <table id="customersTable" class="table table-striped">
            <thead>
              <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Company</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Total Orders</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($customers as $c): ?>
                <tr>
                  <td><?=htmlspecialchars($c['customer_id'])?></td>
                  <td><?=htmlspecialchars($c['full_name'])?></td>
                  <td><?=htmlspecialchars($c['company_name'])?></td>
                  <td><?=htmlspecialchars($c['phone'])?></td>
                  <td><?=htmlspecialchars($c['email'])?></td>
                  <td><?=htmlspecialchars($c['total_orders'])?></td>
                  <td>
                    <a class="btn btn-sm btn-outline-primary" href="customer_view.php?id=<?=$c['customer_id']?>">View</a>
                    <a class="btn btn-sm btn-outline-secondary" href="customer_edit.php?id=<?=$c['customer_id']?>">Edit</a>
                    <form method="post" action="customer_delete.php" style="display:inline" onsubmit="return confirm('Delete this customer?');">
                      <input type="hidden" name="customer_id" value="<?=$c['customer_id']?>">
                      <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
      $(document).ready(function(){
        $('#customersTable').DataTable();
      });
    </script>
  </body>
</html>

<?php
// Admin dashboard - uses include checks and header/footer includes
require_once __DIR__ . '/../../includes/check_admin.php';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin Dashboard - Furniture Sys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="/assets/css/style.css" rel="stylesheet">
    <link href="/assets/css/dashboard.css" rel="stylesheet">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>

    <!-- Dashboard hero / metrics -->
    <div class="container-fluid">
      <div class="row">
        <main class="col-12 px-4 py-4">
          <h1 class="h3">Dashboard Overview</h1>
          <div class="row g-3 mt-3">
            <div class="col-md-3">
              <div class="card metric">
                <div class="card-body">
                  <h5 class="card-title">Total Employees</h5>
                  <p class="card-text display-6">0</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card metric">
                <div class="card-body">
                  <h5 class="card-title">Total Customers</h5>
                  <p class="card-text display-6">0</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card metric">
                <div class="card-body">
                  <h5 class="card-title">Low Stock Items</h5>
                  <p class="card-text display-6">0</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card metric">
                <div class="card-body">
                  <h5 class="card-title">Pending Orders</h5>
                  <p class="card-text display-6">0</p>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>

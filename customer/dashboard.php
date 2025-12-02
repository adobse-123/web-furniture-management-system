<?php
require_once __DIR__ . '/../../includes/check_customer.php';
session_start();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Customer Dashboard - Furniture Sys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="/assets/css/style.css" rel="stylesheet">
    <link href="/assets/css/dashboard.css" rel="stylesheet">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>

    <div class="container-fluid">
      <div class="row">
        <main class="col-12 px-4 py-4">
          <h1 class="h3">Order Summary</h1>
          <p>Recent Orders, Payment Status, and Notifications will appear here.</p>
        </main>
      </div>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>

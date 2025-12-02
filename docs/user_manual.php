<?php
require_once __DIR__ . '/../config/config.php';
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Manual - <?php echo SITE_NAME; ?></title>
    <link href="/assets/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h1>User Manual</h1>
<p>This manual explains how to use the system.</p>
<h2>Login</h2>
<p>Go to `login.php` and enter your credentials.</p>
<h2>Admin Guide (Short)</h2>
<ul>
    <li>Manage Customers: Admin -> Customers</li>
    <li>Orders: Admin -> Orders (create, assign, view)</li>
    <li>Stock: Admin -> Stock (view materials, record transactions)</li>
</ul>
<h2>Employee</h2>
<p>Employees can view assigned orders under Employee -> My Orders.</p>
<h2>Customer</h2>
<p>Customers can view orders at Customer -> My Orders.</p>
</body>
</html>

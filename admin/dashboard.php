<?php
require_once __DIR__ . '/../../auth_functions.php';
// Only allow admins
session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../../login.php');
    exit;
}
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <link href="/assets/css/style.css" rel="stylesheet">
  </head>
  <body>
    <div class="container py-5">
      <h1>Admin Dashboard</h1>
      <p>Welcome, <?=htmlspecialchars($_SESSION['user_email'])?></p>
      <p><a href="../../logout.php">Logout</a></p>
    </div>
  </body>
</html>

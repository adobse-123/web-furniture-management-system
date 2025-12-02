<?php
// admin header + sidebar (assumes check_admin.php already ran or include it here if needed)
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="/admin/dashboard.php">Furniture Sys - Admin</a>
    <div class="d-flex align-items-center">
      <span class="text-white me-3">Welcome, <?=htmlspecialchars($_SESSION['user_email'] ?? '')?></span>
      <div class="dropdown">
        <a class="btn btn-light btn-sm dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Account</a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#">Profile</a></li>
          <li><a class="dropdown-item" href="/logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">
    <nav id="sidebarMenu" class="col-md-2 d-md-block bg-light sidebar collapse">
      <div class="position-sticky pt-4">
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link" href="/admin/dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Manage Employees</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Manage Customers</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Stock</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Products</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Reports</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Settings</a></li>
        </ul>
      </div>
    </nav>
    <main class="col-md-10 ms-sm-auto px-4">

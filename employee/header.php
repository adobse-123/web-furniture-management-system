<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
  <div class="container-fluid">
    <a class="navbar-brand" href="/employee/dashboard.php">Furniture Sys - Employee</a>
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
    <nav id="sidebarMenuEmp" class="col-md-2 d-md-block bg-light sidebar collapse">
      <div class="position-sticky pt-4">
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link" href="/employee/dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="#">My Orders</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Customers</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Stock View</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Products</a></li>
          <li class="nav-item"><a class="nav-link" href="#">My Profile</a></li>
        </ul>
      </div>
    </nav>
    <main class="col-md-10 ms-sm-auto px-4">

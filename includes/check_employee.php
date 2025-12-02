<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
// Ensure user is logged in and is employee
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'employee') {
    header('Location: /login.php');
    exit;
}

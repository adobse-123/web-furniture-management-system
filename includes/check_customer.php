<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
// Ensure user is logged in and is customer
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'customer') {
    header('Location: /login.php');
    exit;
}

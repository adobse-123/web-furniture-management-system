<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_customer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: customers.php'); exit;
}
$cid = intval($_POST['customer_id'] ?? 0);
if (!$cid) { header('Location: customers.php'); exit; }

// prevent deletion if orders exist
if (customer_has_orders($cid)) {
    header('Location: customers.php?error=has_orders'); exit;
}

if (customer_delete($cid)) {
    header('Location: customers.php?deleted=1'); exit;
} else {
    header('Location: customers.php?error=delete_failed'); exit;
}

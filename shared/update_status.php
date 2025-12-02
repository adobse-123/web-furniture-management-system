<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_order.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}
$order_id = $_POST['order_id'] ?? null;
$new = $_POST['status'] ?? null;
if (!$order_id || !$new) {
    http_response_code(400);
    echo 'Missing parameters';
    exit;
}
$ord = get_order_by_id($order_id);
$old = $ord['status'] ?? null;
if ($old === $new) {
    echo 'No change';
    exit;
}
update_order($order_id, ['status' => $new]);
log_order_status_change($order_id, $old, $new, $_SESSION['user_id'] ?? null);
header('Location: ../admin/order_view.php?id=' . $order_id);

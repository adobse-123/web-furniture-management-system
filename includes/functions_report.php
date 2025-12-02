<?php
require_once __DIR__ . '/functions_order.php';

function get_monthly_revenue($year = null)
{
    $pdo = get_pdo();
    $year = $year ?? date('Y');
    $stmt = $pdo->prepare("SELECT MONTH(created_at) AS month, SUM(total_amount) AS revenue FROM orders WHERE YEAR(created_at) = :y AND status IN ('Completed','Delivered') GROUP BY MONTH(created_at)");
    $stmt->execute([':y' => $year]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function top_customers_by_orders($limit = 10)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT c.id, c.name, COUNT(o.id) AS orders_count, SUM(o.total_amount) AS total_spent FROM customers c LEFT JOIN orders o ON o.customer_id = c.id GROUP BY c.id ORDER BY orders_count DESC LIMIT :l");
    $stmt->bindValue(':l', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

<?php
require_once __DIR__ . '/../config/database.php';

function create_order(array $orderData, array $items)
{
    $pdo = get_pdo();
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO orders (customer_id, delivery_date, notes, status, total_amount, advance_payment, created_by, created_at) VALUES (:customer_id, :delivery_date, :notes, :status, :total_amount, :advance_payment, :created_by, NOW())");
        $stmt->execute([
            ':customer_id' => $orderData['customer_id'] ?? null,
            ':delivery_date' => $orderData['delivery_date'] ?? null,
            ':notes' => $orderData['notes'] ?? '',
            ':status' => $orderData['status'] ?? 'Pending',
            ':total_amount' => $orderData['total_amount'] ?? 0,
            ':advance_payment' => $orderData['advance_payment'] ?? 0,
            ':created_by' => $orderData['created_by'] ?? null,
        ]);

        $orderId = $pdo->lastInsertId();

        $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price, total_price) VALUES (:order_id, :product_id, :quantity, :unit_price, :total_price)");
        foreach ($items as $it) {
            $itemStmt->execute([
                ':order_id' => $orderId,
                ':product_id' => $it['product_id'],
                ':quantity' => $it['quantity'],
                ':unit_price' => $it['unit_price'],
                ':total_price' => $it['quantity'] * $it['unit_price'],
            ]);
        }

        // If advance payment provided, record in payments table if exists
        if (!empty($orderData['advance_payment'])) {
            if (table_exists('payments')) {
                $pstmt = $pdo->prepare("INSERT INTO payments (order_id, amount, method, performed_by, created_at) VALUES (:order_id, :amount, :method, :performed_by, NOW())");
                $pstmt->execute([
                    ':order_id' => $orderId,
                    ':amount' => $orderData['advance_payment'],
                    ':method' => $orderData['payment_method'] ?? 'Advance',
                    ':performed_by' => $orderData['created_by'] ?? null,
                ]);
            }
        }

        $pdo->commit();
        return $orderId;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function table_exists($table)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SHOW TABLES LIKE :t");
    $stmt->execute([':t' => $table]);
    return (bool) $stmt->fetchColumn();
}

function get_orders(array $filters = [])
{
    $pdo = get_pdo();
    $sql = "SELECT o.*, c.name AS customer_name FROM orders o LEFT JOIN customers c ON o.customer_id = c.id";
    $where = [];
    $params = [];
    if (!empty($filters['status'])) {
        $where[] = 'o.status = :status';
        $params[':status'] = $filters['status'];
    }
    if (!empty($filters['customer_id'])) {
        $where[] = 'o.customer_id = :customer_id';
        $params[':customer_id'] = $filters['customer_id'];
    }
    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY o.created_at DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_order_by_id($id)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT o.*, c.name AS customer_name, c.email AS customer_email FROM orders o LEFT JOIN customers c ON o.customer_id = c.id WHERE o.id = :id");
    $stmt->execute([':id' => $id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($order) {
        $it = $pdo->prepare("SELECT oi.*, p.name AS product_name FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = :order_id");
        $it->execute([':order_id' => $id]);
        $order['items'] = $it->fetchAll(PDO::FETCH_ASSOC);
        if (table_exists('payments')) {
            $p = $pdo->prepare("SELECT * FROM payments WHERE order_id = :order_id ORDER BY created_at DESC");
            $p->execute([':order_id' => $id]);
            $order['payments'] = $p->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $order['payments'] = [];
        }
    }
    return $order;
}

function update_order($id, array $data)
{
    $pdo = get_pdo();
    $fields = [];
    $params = [':id' => $id];
    foreach (['delivery_date', 'notes', 'status', 'total_amount', 'advance_payment', 'assigned_employee_id'] as $f) {
        if (array_key_exists($f, $data)) {
            $fields[] = "$f = :$f";
            $params[":$f"] = $data[$f];
        }
    }
    if (empty($fields)) return false;
    $sql = 'UPDATE orders SET ' . implode(', ', $fields) . ' WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

function add_payment($order_id, $amount, $method = 'Manual', $performed_by = null)
{
    $pdo = get_pdo();
    if (!table_exists('payments')) {
        // create a simple payments table if missing
        $pdo->exec("CREATE TABLE IF NOT EXISTS payments (id INT AUTO_INCREMENT PRIMARY KEY, order_id INT, amount DECIMAL(10,2), method VARCHAR(50), performed_by INT, created_at DATETIME)");
    }
    $stmt = $pdo->prepare("INSERT INTO payments (order_id, amount, method, performed_by, created_at) VALUES (:order_id, :amount, :method, :performed_by, NOW())");
    $stmt->execute([
        ':order_id' => $order_id,
        ':amount' => $amount,
        ':method' => $method,
        ':performed_by' => $performed_by,
    ]);
    // update advance_payment on orders
    $stmt2 = $pdo->prepare("UPDATE orders SET advance_payment = COALESCE(advance_payment,0) + :amt WHERE id = :id");
    $stmt2->execute([':amt' => $amount, ':id' => $order_id]);
    return $pdo->lastInsertId();
}

function get_payments($order_id)
{
    $pdo = get_pdo();
    if (!table_exists('payments')) return [];
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE order_id = :order_id ORDER BY created_at DESC");
    $stmt->execute([':order_id' => $order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function log_order_status_change($order_id, $old_status, $new_status, $changed_by = null)
{
    $pdo = get_pdo();
    if (!table_exists('order_status_logs')) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS order_status_logs (id INT AUTO_INCREMENT PRIMARY KEY, order_id INT, old_status VARCHAR(50), new_status VARCHAR(50), changed_by INT, changed_at DATETIME)");
    }
    $stmt = $pdo->prepare("INSERT INTO order_status_logs (order_id, old_status, new_status, changed_by, changed_at) VALUES (:order_id, :old_status, :new_status, :changed_by, NOW())");
    $stmt->execute([':order_id' => $order_id, ':old_status' => $old_status, ':new_status' => $new_status, ':changed_by' => $changed_by]);
}

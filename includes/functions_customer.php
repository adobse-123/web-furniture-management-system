<?php
// Customer related DB functions
require_once __DIR__ . '/../auth_functions.php';

function customer_get_all() {
    $pdo = get_pdo();
    $sql = "SELECT c.id as customer_id, u.id as user_id, u.full_name, u.email, u.phone, c.company_name, c.created_at,
            (SELECT COUNT(*) FROM orders o WHERE o.customer_id = c.id) as total_orders
            FROM customers c
            JOIN users u ON u.id = c.user_id
            ORDER BY c.created_at DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function customer_create($userData, $customerData) {
    $pdo = get_pdo();
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO users (full_name, email, phone, password, role) VALUES (:full_name, :email, :phone, :password, :role)');
        $stmt->execute([
            'full_name' => $userData['full_name'],
            'email' => $userData['email'],
            'phone' => $userData['phone'] ?? null,
            'password' => $userData['password'],
            'role' => 'customer'
        ]);
        $user_id = $pdo->lastInsertId();

        $stmt2 = $pdo->prepare('INSERT INTO customers (user_id, company_name, address, tax_number) VALUES (:user_id, :company_name, :address, :tax_number)');
        $stmt2->execute([
            'user_id' => $user_id,
            'company_name' => $customerData['company_name'] ?? null,
            'address' => $customerData['address'] ?? null,
            'tax_number' => $customerData['tax_number'] ?? null
        ]);

        $pdo->commit();
        return $user_id;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function customer_get_by_id($customer_id) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT c.*, u.full_name, u.email, u.phone FROM customers c JOIN users u ON u.id = c.user_id WHERE c.id = :id LIMIT 1');
    $stmt->execute(['id' => $customer_id]);
    return $stmt->fetch();
}

function customer_update($customer_id, $userUpdates, $customerUpdates) {
    $pdo = get_pdo();
    try {
        $pdo->beginTransaction();
        // fetch current
        $stmt = $pdo->prepare('SELECT user_id FROM customers WHERE id = :id');
        $stmt->execute(['id' => $customer_id]);
        $row = $stmt->fetch();
        if (!$row) { $pdo->rollBack(); return false; }
        $user_id = $row['user_id'];

        if (!empty($userUpdates)) {
            $sets = [];
            $params = ['user_id' => $user_id];
            if (isset($userUpdates['full_name'])) { $sets[] = 'full_name = :full_name'; $params['full_name'] = $userUpdates['full_name']; }
            if (isset($userUpdates['email'])) { $sets[] = 'email = :email'; $params['email'] = $userUpdates['email']; }
            if (isset($userUpdates['phone'])) { $sets[] = 'phone = :phone'; $params['phone'] = $userUpdates['phone']; }
            if (isset($userUpdates['password'])) { $sets[] = 'password = :password'; $params['password'] = $userUpdates['password']; }
            if (!empty($sets)) {
                $sql = 'UPDATE users SET ' . implode(',', $sets) . ' WHERE id = :user_id';
                $stmt2 = $pdo->prepare($sql);
                $stmt2->execute($params);
            }
        }

        if (!empty($customerUpdates)) {
            $sets = [];
            $params = ['id' => $customer_id];
            if (isset($customerUpdates['company_name'])) { $sets[] = 'company_name = :company_name'; $params['company_name'] = $customerUpdates['company_name']; }
            if (isset($customerUpdates['address'])) { $sets[] = 'address = :address'; $params['address'] = $customerUpdates['address']; }
            if (isset($customerUpdates['tax_number'])) { $sets[] = 'tax_number = :tax_number'; $params['tax_number'] = $customerUpdates['tax_number']; }
            if (!empty($sets)) {
                $sql = 'UPDATE customers SET ' . implode(',', $sets) . ' WHERE id = :id';
                $stmt3 = $pdo->prepare($sql);
                $stmt3->execute($params);
            }
        }

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function customer_has_orders($customer_id) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT COUNT(*) as cnt FROM orders WHERE customer_id = :id');
    $stmt->execute(['id' => $customer_id]);
    $row = $stmt->fetch();
    return ($row && $row['cnt'] > 0);
}

function customer_delete($customer_id) {
    $pdo = get_pdo();
    // fetch user id
    $stmt = $pdo->prepare('SELECT user_id FROM customers WHERE id = :id');
    $stmt->execute(['id' => $customer_id]);
    $row = $stmt->fetch();
    if (!$row) return false;
    $user_id = $row['user_id'];

    try {
        $pdo->beginTransaction();
        // delete user (will cascade to customers via FK)
        $stmt2 = $pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt2->execute(['id' => $user_id]);
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

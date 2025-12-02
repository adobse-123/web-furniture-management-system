<?php
// Employee related DB functions
require_once __DIR__ . '/auth_functions.php';

function employee_get_all() {
    $pdo = get_pdo();
    $sql = "SELECT e.id as emp_id, e.employee_id, u.id as user_id, u.full_name, u.email, u.phone, e.department, e.position, e.salary, e.hire_date
            FROM employees e
            JOIN users u ON u.id = e.user_id
            ORDER BY e.hire_date DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function employee_create($userData, $employeeData) {
    $pdo = get_pdo();
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO users (full_name, email, phone, password, role) VALUES (:full_name, :email, :phone, :password, :role)');
        $stmt->execute([
            'full_name' => $userData['full_name'],
            'email' => $userData['email'],
            'phone' => $userData['phone'] ?? null,
            'password' => $userData['password'],
            'role' => 'employee'
        ]);
        $user_id = $pdo->lastInsertId();

        $stmt2 = $pdo->prepare('INSERT INTO employees (user_id, employee_id, department, position, salary, hire_date, emergency_contact) VALUES (:user_id, :employee_id, :department, :position, :salary, :hire_date, :emergency_contact)');
        $stmt2->execute([
            'user_id' => $user_id,
            'employee_id' => $employeeData['employee_id'] ?? null,
            'department' => $employeeData['department'] ?? null,
            'position' => $employeeData['position'] ?? null,
            'salary' => $employeeData['salary'] ?? 0,
            'hire_date' => $employeeData['hire_date'] ?? null,
            'emergency_contact' => $employeeData['emergency_contact'] ?? null
        ]);

        $pdo->commit();
        return $user_id;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function employee_get_by_id($emp_id) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT e.*, u.full_name, u.email, u.phone FROM employees e JOIN users u ON u.id = e.user_id WHERE e.id = :id LIMIT 1');
    $stmt->execute(['id' => $emp_id]);
    return $stmt->fetch();
}

function employee_get_by_user_id($user_id) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT e.* FROM employees e WHERE e.user_id = :uid LIMIT 1');
    $stmt->execute(['uid' => $user_id]);
    return $stmt->fetch();
}

function employee_update($emp_id, $userUpdates, $employeeUpdates) {
    $pdo = get_pdo();
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('SELECT user_id FROM employees WHERE id = :id');
        $stmt->execute(['id' => $emp_id]);
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

        if (!empty($employeeUpdates)) {
            $sets = [];
            $params = ['id' => $emp_id];
            if (isset($employeeUpdates['employee_id'])) { $sets[] = 'employee_id = :employee_id'; $params['employee_id'] = $employeeUpdates['employee_id']; }
            if (isset($employeeUpdates['department'])) { $sets[] = 'department = :department'; $params['department'] = $employeeUpdates['department']; }
            if (isset($employeeUpdates['position'])) { $sets[] = 'position = :position'; $params['position'] = $employeeUpdates['position']; }
            if (isset($employeeUpdates['salary'])) { $sets[] = 'salary = :salary'; $params['salary'] = $employeeUpdates['salary']; }
            if (isset($employeeUpdates['hire_date'])) { $sets[] = 'hire_date = :hire_date'; $params['hire_date'] = $employeeUpdates['hire_date']; }
            if (isset($employeeUpdates['emergency_contact'])) { $sets[] = 'emergency_contact = :emergency_contact'; $params['emergency_contact'] = $employeeUpdates['emergency_contact']; }
            if (!empty($sets)) {
                $sql = 'UPDATE employees SET ' . implode(',', $sets) . ' WHERE id = :id';
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

function employee_delete($emp_id) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT user_id FROM employees WHERE id = :id');
    $stmt->execute(['id' => $emp_id]);
    $row = $stmt->fetch();
    if (!$row) return false;
    $user_id = $row['user_id'];

    try {
        $pdo->beginTransaction();
        $stmt2 = $pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt2->execute(['id' => $user_id]);
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

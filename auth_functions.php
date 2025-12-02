<?php
// Common authentication helper functions
// Usage: require_once __DIR__ . '/auth_functions.php';

function get_pdo() {
    // database.php returns a PDO instance
    $pdo = require __DIR__ . '/config/database.php';
    return $pdo;
}

function find_user_by_email($email) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    return $stmt->fetch();
}

function register_user($data) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('INSERT INTO users (full_name, email, phone, password, role) VALUES (:full_name, :email, :phone, :password, :role)');
    return $stmt->execute([
        'full_name' => $data['full_name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'password' => $data['password'],
        'role' => $data['role']
    ]);
}

function attempt_login($email, $password) {
    $user = find_user_by_email($email);
    if (!$user) return false;
    if (password_verify($password, $user['password'])) return $user;
    return false;
}

function login_and_redirect($user) {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];

    // Redirect based on role
    switch ($user['role']) {
        case 'admin':
            header('Location: admin/dashboard.php');
            exit;
        case 'employee':
            header('Location: employee/dashboard.php');
            exit;
        default:
            header('Location: customer/dashboard.php');
            exit;
    }
}

function require_login() {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit;
    }
}

function logout_user() {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
    header('Location: index.html');
    exit;
}

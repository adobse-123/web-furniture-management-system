<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions_users.php'; // if exists - user helpers

function hasPermission($requiredRole)
{
    if (empty($_SESSION['user_role'])) return false;
    $role = $_SESSION['user_role'];
    if ($role === 'admin') return true;
    if (is_array($requiredRole)) {
        return in_array($role, $requiredRole);
    }
    return $role === $requiredRole;
}

function require_role($role)
{
    if (!hasPermission($role)) {
        // redirect to home or show unauthorized
        header('HTTP/1.1 403 Forbidden');
        echo 'Access denied';
        exit;
    }
}

function logout_and_destroy()
{
    // Unset session and destroy
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'], $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

// Helper to enforce page role at top of pages. Example: require_page_role('admin');
function require_page_role($role)
{
    require_login();
    require_role($role);
}

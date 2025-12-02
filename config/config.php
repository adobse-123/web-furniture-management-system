<?php
// Central configuration included at top of pages.
// Start session and apply some security settings.

// Session settings
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
// If your site uses HTTPS, enable the secure flag
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
}

session_start();

// Session timeout (30 minutes)
$timeout = 30 * 60; // seconds
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
    // last request was more than $timeout ago
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Load DB helper
require_once __DIR__ . '/database.php';

// Site-wide settings
define('SITE_NAME', 'Furniture Management System');
define('BASE_URL', '/');

// Simple helper to require login
function require_login()
{
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

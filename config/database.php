<?php
// Database configuration (PDO)
// Update the DSN, username and password for your environment.
$dbHost = '127.0.0.1';
$dbName = 'furniture_db';
$dbUser = 'root';
$dbPass = '';
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // In production, avoid echoing DB errors. Log them instead.
    exit('Database connection failed: ' . $e->getMessage());
}

return $pdo;

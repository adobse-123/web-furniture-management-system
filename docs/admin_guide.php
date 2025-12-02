<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';
require_page_role('admin');
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Guide - <?php echo SITE_NAME; ?></title>
    <link href="/assets/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h1>Admin Guide</h1>
<p>This guide covers administration tasks.</p>
<h2>Creating Admin User</h2>
<p>Use SQL to insert into `users` the first admin account if you don't have web registration.</p>
<pre>INSERT INTO users (name, email, password, role) VALUES ('Admin', 'admin@example.com', '<hashed_password>', 'admin');</pre>

<h2>Backups</h2>
<p>See `BACKUP_README.txt` in repository root for quick steps.</p>
</body>
</html>

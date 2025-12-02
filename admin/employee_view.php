<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_employee.php';
require_once __DIR__ . '/../includes/functions_attendance.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: employees.php'); exit; }
$emp = employee_get_by_id($id);
if (!$emp) { header('Location: employees.php'); exit; }

$year = date('Y'); $month = date('m');
$summary = attendance_month_summary($id, $year, $month);

// assigned orders - simple query
$pdo = get_pdo();
$stmt = $pdo->prepare('SELECT id, order_number, status FROM orders WHERE assigned_employee_id = :eid');
$stmt->execute(['eid' => $id]);
$orders = $stmt->fetchAll();

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>
    <div class="container py-4">
      <div class="row">
        <div class="col-md-6">
          <h3>Employee Info</h3>
          <ul class="list-group">
            <li class="list-group-item"><strong>Name:</strong> <?=htmlspecialchars($emp['full_name'])?></li>
            <li class="list-group-item"><strong>Email:</strong> <?=htmlspecialchars($emp['email'])?></li>
            <li class="list-group-item"><strong>Employee ID:</strong> <?=htmlspecialchars($emp['employee_id'])?></li>
            <li class="list-group-item"><strong>Department:</strong> <?=htmlspecialchars($emp['department'])?></li>
            <li class="list-group-item"><strong>Position:</strong> <?=htmlspecialchars($emp['position'])?></li>
            <li class="list-group-item"><strong>Salary:</strong> <?=number_format($emp['salary'],2)?></li>
          </ul>
        </div>
        <div class="col-md-6">
          <h3>Attendance Summary (This Month)</h3>
          <ul class="list-group mb-3">
            <li class="list-group-item">Present: <?=intval($summary['Present'] ?? 0)?></li>
            <li class="list-group-item">Absent: <?=intval($summary['Absent'] ?? 0)?></li>
            <li class="list-group-item">Late: <?=intval($summary['Late'] ?? 0)?></li>
          </ul>
          <h3>Assigned Orders</h3>
          <ul class="list-group">
            <?php foreach($orders as $o): ?><li class="list-group-item"><?=htmlspecialchars($o['order_number'])?> — <?=htmlspecialchars($o['status'])?></li><?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
    <?php include __DIR__ . '/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>

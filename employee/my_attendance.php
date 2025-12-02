<?php
require_once __DIR__ . '/../includes/check_employee.php';
require_once __DIR__ . '/../includes/functions_employee.php';
require_once __DIR__ . '/../includes/functions_attendance.php';

session_start();
$user_id = $_SESSION['user_id'] ?? null;
$emp = employee_get_by_user_id($user_id);
if (!$emp) { header('Location: /'); exit; }

$year = intval($_GET['year'] ?? date('Y'));
$month = intval($_GET['month'] ?? date('m'));
$records = attendance_get_for_employee_month($emp['id'], $year, $month);
$summary = attendance_month_summary($emp['id'], $year, $month);

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>
    <div class="container py-4">
      <h2>Attendance - <?=htmlspecialchars($month)?>/<?=htmlspecialchars($year)?></h2>
      <p>Summary: Present <?=intval($summary['Present'] ?? 0)?> — Absent <?=intval($summary['Absent'] ?? 0)?> — Late <?=intval($summary['Late'] ?? 0)?></p>
      <table class="table table-sm">
        <thead><tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach($records as $r): ?>
          <tr>
            <td><?=htmlspecialchars($r['date'])?></td>
            <td><?=htmlspecialchars($r['check_in'])?></td>
            <td><?=htmlspecialchars($r['check_out'])?></td>
            <td><?=htmlspecialchars($r['status'])?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php include __DIR__ . '/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>

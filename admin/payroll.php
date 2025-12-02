<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_employee.php';
require_once __DIR__ . '/../includes/functions_attendance.php';

$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? date('m');
$emps = employee_get_all();

// simple payroll calc: salary - (absent_days * (salary/30))
function calc_pay($salary, $absent_days) {
    $daily = $salary / 30.0;
    return max(0, $salary - ($absent_days * $daily));
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payroll</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>
    <div class="container py-4">
      <h2>Payroll - <?=htmlspecialchars($month)?>/<?=htmlspecialchars($year)?></h2>
      <table class="table">
        <thead><tr><th>Employee</th><th>Salary</th><th>Absent Days</th><th>Payable</th></tr></thead>
        <tbody>
        <?php foreach($emps as $e):
          $empRec = employee_get_by_id($e['emp_id']);
          $summary = attendance_month_summary($e['emp_id'], $year, $month);
          $abs = intval($summary['Absent'] ?? 0);
          $pay = calc_pay(floatval($e['salary']), $abs);
        ?>
          <tr>
            <td><?=htmlspecialchars($e['full_name'])?></td>
            <td><?=number_format($e['salary'],2)?></td>
            <td><?=$abs?></td>
            <td><?=number_format($pay,2)?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php include __DIR__ . '/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>

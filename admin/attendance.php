<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_employee.php';
require_once __DIR__ . '/../includes/functions_attendance.php';

$date = $_GET['date'] ?? date('Y-m-d');
$employees = employee_get_all();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // simple bulk update: mark present/absent for selected
    foreach ($_POST['status'] ?? [] as $empId => $status) {
        $pdo = get_pdo();
        // ensure record exists
        $stmt = $pdo->prepare('SELECT id FROM attendances WHERE employee_id = :eid AND date = :d LIMIT 1');
        $stmt->execute(['eid'=>$empId,'d'=>$date]);
        $row = $stmt->fetch();
        if ($row) {
            $stmt2 = $pdo->prepare('UPDATE attendances SET status = :st WHERE id = :id');
            $stmt2->execute(['st'=>$status,'id'=>$row['id']]);
        } else {
            $stmt3 = $pdo->prepare('INSERT INTO attendances (employee_id, date, status) VALUES (:eid, :d, :st)');
            $stmt3->execute(['eid'=>$empId,'d'=>$date,'st'=>$status]);
        }
    }
    header('Location: attendance.php?date=' . urlencode($date) . '&saved=1'); exit;
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>
    <div class="container py-4">
      <h2>Attendance for <input type="date" id="datePicker" value="<?=$date?>"></h2>
      <form method="post" id="attForm">
        <table class="table">
          <thead><tr><th>Name</th><th>Department</th><th>Status</th></tr></thead>
          <tbody>
            <?php foreach($employees as $e):
              $pdo = get_pdo();
              $stmt = $pdo->prepare('SELECT * FROM attendances WHERE employee_id = :eid AND date = :d LIMIT 1');
              $stmt->execute(['eid'=>$e['emp_id'],'d'=>$date]);
              $rec = $stmt->fetch();
              $cur = $rec['status'] ?? 'Absent';
            ?>
            <tr>
              <td><?=htmlspecialchars($e['full_name'])?></td>
              <td><?=htmlspecialchars($e['department'])?></td>
              <td>
                <select name="status[<?=$e['emp_id']?>]" class="form-select form-select-sm">
                  <option value="Present" <?=($cur=='Present'?'selected':'')?>>Present</option>
                  <option value="Absent" <?=($cur=='Absent'?'selected':'')?>>Absent</option>
                  <option value="Late" <?=($cur=='Late'?'selected':'')?>>Late</option>
                  <option value="Leave" <?=($cur=='Leave'?'selected':'')?>>Leave</option>
                </select>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="d-flex gap-2"><button class="btn btn-primary">Save</button><a class="btn btn-secondary" href="attendance.php">Today</a></div>
      </form>
    </div>
    <?php include __DIR__ . '/footer.php'; ?>
    <script>
      document.getElementById('datePicker').addEventListener('change', function(){
        location.href = 'attendance.php?date=' + encodeURIComponent(this.value);
      });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>

<?php
require_once __DIR__ . '/../includes/check_employee.php';
require_once __DIR__ . '/../includes/functions_attendance.php';

session_start();
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) { header('Location: /login.php'); exit; }

$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['check_in'])) {
        $res = attendance_check_in($user_id);
        $message = $res['error'] ?? 'Checked in';
    } elseif (isset($_POST['check_out'])) {
        $res = attendance_check_out($user_id);
        $message = $res['error'] ?? 'Checked out';
    }
}

// show today's record
$emp = employee_get_by_user_id($user_id);
$today = date('Y-m-d');
$pdo = get_pdo();
$stmt = $pdo->prepare('SELECT * FROM attendances WHERE employee_id = :eid AND date = :d LIMIT 1');
$stmt->execute(['eid'=>$emp['id'],'d'=>$today]);
$rec = $stmt->fetch();

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check In / Out</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>
    <div class="container py-4">
      <h2>Today's Attendance (<?=htmlspecialchars($today)?>)</h2>
      <?php if ($message): ?><div class="alert alert-info"><?=htmlspecialchars($message)?></div><?php endif; ?>
      <p>Check In: <?=htmlspecialchars($rec['check_in'] ?? '—')?></p>
      <p>Check Out: <?=htmlspecialchars($rec['check_out'] ?? '—')?></p>

      <form method="post">
        <?php if (empty($rec) || empty($rec['check_in'])): ?>
          <button name="check_in" class="btn btn-success">Check In</button>
        <?php elseif (empty($rec['check_out'])): ?>
          <button name="check_out" class="btn btn-warning">Check Out</button>
        <?php else: ?>
          <div class="alert alert-secondary">You have already checked out today.</div>
        <?php endif; ?>
      </form>
    </div>
    <?php include __DIR__ . '/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>

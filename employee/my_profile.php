<?php
require_once __DIR__ . '/../includes/check_employee.php';
require_once __DIR__ . '/../includes/functions_employee.php';

// Get employee record for logged-in user
session_start();
$user_id = $_SESSION['user_id'] ?? null;
$emp = employee_get_by_user_id($user_id);
if (!$emp) { header('Location: /'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone'] ?? '');
    $emergency = trim($_POST['emergency_contact'] ?? '');
    $userUpdates = ['phone' => $phone];
    $employeeUpdates = ['emergency_contact' => $emergency];
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) < 6) $errors[] = 'Password min 6 chars.';
        else $userUpdates['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }
    if (empty($errors)) {
        if (employee_update($emp['id'], $userUpdates, $employeeUpdates)) {
            $updated = true; $emp = employee_get_by_id($emp['id']);
        } else { $errors[] = 'Update failed.'; }
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>
    <div class="container py-4">
      <h2>My Profile</h2>
      <?php if (!empty($errors)): ?><div class="alert alert-danger"><?=implode('<br>', array_map('htmlspecialchars',$errors))?></div><?php endif; ?>
      <?php if (!empty($updated)): ?><div class="alert alert-success">Profile updated.</div><?php endif; ?>

      <form method="post">
        <div class="mb-3"><label class="form-label">Full Name</label><input class="form-control" value="<?=htmlspecialchars($emp['full_name'])?>" disabled></div>
        <div class="mb-3"><label class="form-label">Email</label><input class="form-control" value="<?=htmlspecialchars($emp['email'])?>" disabled></div>
        <div class="mb-3"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?=htmlspecialchars($emp['phone'])?>"></div>
        <div class="mb-3"><label class="form-label">Emergency Contact</label><input name="emergency_contact" class="form-control" value="<?=htmlspecialchars($emp['emergency_contact'])?>"></div>
        <div class="mb-3"><label class="form-label">New Password (leave blank)</label><input type="password" name="password" class="form-control"></div>
        <div class="d-grid"><button class="btn btn-primary">Update</button></div>
      </form>
    </div>
    <?php include __DIR__ . '/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>

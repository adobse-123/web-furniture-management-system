<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_employee.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: employees.php'); exit; }
$emp = employee_get_by_id($id);
if (!$emp) { header('Location: employees.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $employee_id = trim($_POST['employee_id'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $salary = floatval($_POST['salary'] ?? 0);
    $hire_date = $_POST['hire_date'] ?? null;
    $emergency_contact = trim($_POST['emergency_contact'] ?? '');

    if ($full_name === '') $errors[] = 'Full name required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';

    if (empty($errors)) {
        $userUpdates = ['full_name'=>$full_name,'email'=>$email,'phone'=>$phone];
        if (!empty($password)) $userUpdates['password'] = password_hash($password, PASSWORD_DEFAULT);
        $employeeUpdates = ['employee_id'=>$employee_id,'department'=>$department,'position'=>$position,'salary'=>$salary,'hire_date'=>$hire_date,'emergency_contact'=>$emergency_contact];
        if (employee_update($id, $userUpdates, $employeeUpdates)) {
            header('Location: employee_view.php?id=' . $id . '&updated=1'); exit;
        } else {
            $errors[] = 'Update failed.';
        }
    }
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>
    <div class="container py-4">
      <h2>Edit Employee</h2>
      <?php if (!empty($errors)): ?><div class="alert alert-danger"><ul><?php foreach($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach;?></ul></div><?php endif; ?>

      <form method="post">
        <div class="mb-3"><label class="form-label">Full Name</label><input name="full_name" class="form-control" value="<?=htmlspecialchars($_POST['full_name'] ?? $emp['full_name'])?>"></div>
        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?=htmlspecialchars($_POST['email'] ?? $emp['email'])?>"></div>
        <div class="mb-3"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?=htmlspecialchars($_POST['phone'] ?? $emp['phone'])?>"></div>
        <div class="mb-3"><label class="form-label">Reset Password (leave blank to keep)</label><input type="password" name="password" class="form-control"></div>

        <h5 class="mt-4">Employee Details</h5>
        <div class="mb-3"><label class="form-label">Employee ID</label><input name="employee_id" class="form-control" value="<?=htmlspecialchars($_POST['employee_id'] ?? $emp['employee_id'])?>"></div>
        <div class="mb-3"><label class="form-label">Department</label><input name="department" class="form-control" value="<?=htmlspecialchars($_POST['department'] ?? $emp['department'])?>"></div>
        <div class="mb-3"><label class="form-label">Position</label><input name="position" class="form-control" value="<?=htmlspecialchars($_POST['position'] ?? $emp['position'])?>"></div>
        <div class="mb-3"><label class="form-label">Salary</label><input name="salary" type="number" step="0.01" class="form-control" value="<?=htmlspecialchars($_POST['salary'] ?? $emp['salary'])?>"></div>
        <div class="mb-3"><label class="form-label">Hire Date</label><input name="hire_date" type="date" class="form-control" value="<?=htmlspecialchars($_POST['hire_date'] ?? $emp['hire_date'])?>"></div>
        <div class="mb-3"><label class="form-label">Emergency Contact</label><input name="emergency_contact" class="form-control" value="<?=htmlspecialchars($_POST['emergency_contact'] ?? $emp['emergency_contact'])?>"></div>

        <div class="d-grid"><button class="btn btn-primary">Update Employee</button></div>
      </form>
    </div>
    <?php include __DIR__ . '/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>

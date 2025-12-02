<?php
require_once __DIR__ . '/auth_functions.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $role = in_array($_POST['role'] ?? 'customer', ['customer','employee']) ? $_POST['role'] : 'customer';

    // Basic server-side validation
    if ($full_name === '') $errors[] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    // Check unique email
    if (empty($errors)) {
        $existing = find_user_by_email($email);
        if ($existing) $errors[] = 'Email is already registered.';
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $data = [
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone,
            'password' => $hashed,
            'role' => $role
        ];
        if (register_user($data)) {
            // auto-login
            $user = find_user_by_email($email);
            login_and_redirect($user);
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register - Jimma Furniture System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <h2>Register</h2>
          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
              <?php foreach ($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form method="post" novalidate>
            <div class="mb-3">
              <label class="form-label">Full Name</label>
              <input class="form-control" name="full_name" required value="<?=htmlspecialchars($_POST['full_name'] ?? '')?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input class="form-control" type="email" name="email" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Phone Number</label>
              <input class="form-control" name="phone" value="<?=htmlspecialchars($_POST['phone'] ?? '')?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input class="form-control" type="password" name="password" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Confirm Password</label>
              <input class="form-control" type="password" name="confirm_password" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Role</label>
              <select name="role" class="form-select">
                <option value="customer">Customer</option>
                <option value="employee">Employee</option>
              </select>
            </div>
            <div class="d-grid">
              <button class="btn btn-primary">Register</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>

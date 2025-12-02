<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_customer.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: customers.php'); exit; }
$customer = customer_get_by_id($id);
if (!$customer) { header('Location: customers.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $company_name = trim($_POST['company_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $tax_number = trim($_POST['tax_number'] ?? '');

    if ($full_name === '') $errors[] = 'Full name required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';

    if (empty($errors)) {
        $userUpdates = ['full_name' => $full_name, 'email' => $email, 'phone' => $phone];
        if (!empty($password)) { $userUpdates['password'] = password_hash($password, PASSWORD_DEFAULT); }
        $customerUpdates = ['company_name' => $company_name, 'address' => $address, 'tax_number' => $tax_number];
        if (customer_update($id, $userUpdates, $customerUpdates)) {
            header('Location: customer_view.php?id=' . $id . '&updated=1'); exit;
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
    <title>Edit Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>
    <div class="container py-4">
      <h2>Edit Customer</h2>
      <?php if (!empty($errors)): ?><div class="alert alert-danger"><ul><?php foreach($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach;?></ul></div><?php endif; ?>

      <form method="post">
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input name="full_name" class="form-control" value="<?=htmlspecialchars($_POST['full_name'] ?? $customer['full_name'])?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?=htmlspecialchars($_POST['email'] ?? $customer['email'])?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Phone</label>
          <input name="phone" class="form-control" value="<?=htmlspecialchars($_POST['phone'] ?? $customer['phone'])?>">
        </div>
        <div class="mb-3">
          <label class="form-label">New Password (leave blank to keep)</label>
          <input type="password" name="password" class="form-control">
        </div>

        <h5 class="mt-4">Customer Details</h5>
        <div class="mb-3">
          <label class="form-label">Company</label>
          <input name="company_name" class="form-control" value="<?=htmlspecialchars($_POST['company_name'] ?? $customer['company_name'])?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Address</label>
          <textarea name="address" class="form-control"><?=htmlspecialchars($_POST['address'] ?? $customer['address'])?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Tax Number</label>
          <input name="tax_number" class="form-control" value="<?=htmlspecialchars($_POST['tax_number'] ?? $customer['tax_number'])?>">
        </div>

        <div class="d-grid"><button class="btn btn-primary">Update Customer</button></div>
      </form>
    </div>
    <?php include __DIR__ . '/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>

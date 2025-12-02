<?php
session_start();
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_stock.php';

$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_supplier'])) {
        $data = [
            'name'=>$_POST['name'],
            'contact_person'=>$_POST['contact_person'] ?? '',
            'phone'=>$_POST['phone'] ?? '',
            'email'=>$_POST['email'] ?? '',
            'address'=>$_POST['address'] ?? '',
            'notes'=>$_POST['notes'] ?? ''
        ];
        if (!empty($_POST['id'])) {
            update_supplier($_POST['id'], $data);
        } else {
            create_supplier($data);
        }
        header('Location: suppliers.php'); exit;
    }
    if (isset($_POST['delete_supplier'])) {
        delete_supplier($_POST['id']);
        header('Location: suppliers.php'); exit;
    }
}

$suppliers = get_suppliers();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Suppliers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h3>Suppliers</h3>
        <a href="?action=create" class="btn btn-primary">Add Supplier</a>
    </div>
    <?php if ($action === 'create' || ($action === 'edit' && $id)): 
        $supplier = $action === 'edit' ? get_supplier_by_id($id) : null;
    ?>
        <form method="post" class="mb-4">
            <input type="hidden" name="id" value="<?= $supplier['id'] ?? '' ?>">
            <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" value="<?=htmlspecialchars($supplier['name'] ?? '')?>" required></div>
            <div class="mb-3"><label class="form-label">Contact Person</label><input name="contact_person" class="form-control" value="<?=htmlspecialchars($supplier['contact_person'] ?? '')?>"></div>
            <div class="mb-3"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?=htmlspecialchars($supplier['phone'] ?? '')?>"></div>
            <div class="mb-3"><label class="form-label">Email</label><input name="email" class="form-control" value="<?=htmlspecialchars($supplier['email'] ?? '')?>"></div>
            <div class="mb-3"><label class="form-label">Address</label><textarea name="address" class="form-control"><?=htmlspecialchars($supplier['address'] ?? '')?></textarea></div>
            <div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control"><?=htmlspecialchars($supplier['notes'] ?? '')?></textarea></div>
            <button name="save_supplier" class="btn btn-success">Save</button>
            <a href="suppliers.php" class="btn btn-secondary">Cancel</a>
        </form>
    <?php else: ?>
        <table id="supTable" class="table table-striped">
            <thead><tr><th>ID</th><th>Name</th><th>Contact</th><th>Phone</th><th>Email</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach($suppliers as $s): ?>
                <tr>
                    <td><?=$s['id']?></td>
                    <td><?=htmlspecialchars($s['name'])?></td>
                    <td><?=htmlspecialchars($s['contact_person'])?></td>
                    <td><?=htmlspecialchars($s['phone'])?></td>
                    <td><?=htmlspecialchars($s['email'])?></td>
                    <td>
                        <a href="?action=edit&id=<?=$s['id']?>" class="btn btn-sm btn-warning">Edit</a>
                        <form method="post" style="display:inline-block" onsubmit="return confirm('Delete supplier?')">
                            <input type="hidden" name="id" value="<?=$s['id']?>">
                            <button name="delete_supplier" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>$(document).ready(function(){ $('#supTable').DataTable(); });</script>
</body>
</html>


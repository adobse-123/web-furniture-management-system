<?php
require_once __DIR__ . '/../includes/check_admin.php';
require_once __DIR__ . '/../includes/functions_employee.php';
$emps = employee_get_all();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employees - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>
    <div class="container-fluid px-4 py-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Employees</h2>
        <a class="btn btn-primary" href="employee_create.php">Add New Employee</a>
      </div>

      <div class="card">
        <div class="card-body">
          <table id="empsTable" class="table table-striped">
            <thead>
              <tr><th>Emp ID</th><th>Full Name</th><th>Department</th><th>Position</th><th>Salary</th><th>Hire Date</th><th>Contact</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach($emps as $e): ?>
              <tr>
                <td><?=htmlspecialchars($e['employee_id'])?></td>
                <td><?=htmlspecialchars($e['full_name'])?></td>
                <td><?=htmlspecialchars($e['department'])?></td>
                <td><?=htmlspecialchars($e['position'])?></td>
                <td><?=number_format($e['salary'],2)?></td>
                <td><?=htmlspecialchars($e['hire_date'])?></td>
                <td><?=htmlspecialchars($e['phone'])?></td>
                <td>
                  <a class="btn btn-sm btn-outline-primary" href="employee_view.php?id=<?=$e['emp_id']?>">View</a>
                  <a class="btn btn-sm btn-outline-secondary" href="employee_edit.php?id=<?=$e['emp_id']?>">Edit</a>
                  <form method="post" action="employee_delete.php" style="display:inline" onsubmit="return confirm('Delete this employee?');">
                    <input type="hidden" name="emp_id" value="<?=$e['emp_id']?>">
                    <button class="btn btn-sm btn-danger">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php include __DIR__ . '/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>$(document).ready(()=>$('#empsTable').DataTable());</script>
  </body>
</html>

<?php
require_once __DIR__ . '/auth_functions.php';
require_once __DIR__ . '/functions_employee.php';

function attendance_get_for_employee_month($employee_id, $year, $month) {
    $pdo = get_pdo();
    $start = sprintf('%04d-%02d-01', $year, $month);
    $end = date('Y-m-t', strtotime($start));
    $stmt = $pdo->prepare('SELECT * FROM attendances WHERE employee_id = :eid AND date BETWEEN :start AND :end ORDER BY date');
    $stmt->execute(['eid' => $employee_id, 'start' => $start, 'end' => $end]);
    return $stmt->fetchAll();
}

function attendance_check_in($user_id) {
    $pdo = get_pdo();
    $emp = employee_get_by_user_id($user_id);
    if (!$emp) return ['error' => 'Employee record not found'];
    $eid = $emp['id'];
    $today = date('Y-m-d');
    $now = date('H:i:s');

    // if today's record exists
    $stmt = $pdo->prepare('SELECT * FROM attendances WHERE employee_id = :eid AND date = :d LIMIT 1');
    $stmt->execute(['eid'=>$eid,'d'=>$today]);
    $row = $stmt->fetch();
    if ($row) {
        if (!empty($row['check_in'])) return ['error'=>'Already checked in'];
        $stmt2 = $pdo->prepare('UPDATE attendances SET check_in = :ci, status = :st WHERE id = :id');
        $stmt2->execute(['ci'=>$now,'st'=>'Present','id'=>$row['id']]);
        return ['ok'=>true,'action'=>'checked_in'];
    }

    $stmt = $pdo->prepare('INSERT INTO attendances (employee_id, date, check_in, status) VALUES (:eid, :d, :ci, :st)');
    $stmt->execute(['eid'=>$eid,'d'=>$today,'ci'=>$now,'st'=>'Present']);
    return ['ok'=>true,'action'=>'checked_in'];
}

function attendance_check_out($user_id) {
    $pdo = get_pdo();
    $emp = employee_get_by_user_id($user_id);
    if (!$emp) return ['error' => 'Employee record not found'];
    $eid = $emp['id'];
    $today = date('Y-m-d');
    $now = date('H:i:s');

    $stmt = $pdo->prepare('SELECT * FROM attendances WHERE employee_id = :eid AND date = :d LIMIT 1');
    $stmt->execute(['eid'=>$eid,'d'=>$today]);
    $row = $stmt->fetch();
    if (!$row) return ['error'=>'No check-in record found'];
    if (!empty($row['check_out'])) return ['error'=>'Already checked out'];

    $stmt2 = $pdo->prepare('UPDATE attendances SET check_out = :co WHERE id = :id');
    $stmt2->execute(['co'=>$now,'id'=>$row['id']]);
    return ['ok'=>true,'action'=>'checked_out'];
}

function attendance_month_summary($employee_id, $year, $month) {
    $records = attendance_get_for_employee_month($employee_id, $year, $month);
    $summary = ['Present'=>0,'Absent'=>0,'Late'=>0,'Leave'=>0];
    foreach ($records as $r) {
        $st = $r['status'] ?? 'Absent';
        if (!isset($summary[$st])) $summary[$st]=0;
        $summary[$st]++;
    }
    return $summary;
}

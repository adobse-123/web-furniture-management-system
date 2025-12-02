<?php
require_once __DIR__ . '/../config/database.php';

function get_all_materials()
{
    $pdo = get_pdo();
    $stmt = $pdo->query('SELECT * FROM raw_materials ORDER BY name');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_material_by_id($id)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM raw_materials WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function create_material($data)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('INSERT INTO raw_materials (name, description, unit, current_quantity, alert_threshold, unit_price, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
    return $stmt->execute([
        $data['name'],
        $data['description'] ?? null,
        $data['unit'] ?? null,
        $data['current_quantity'] ?? 0,
        $data['alert_threshold'] ?? 0,
        $data['unit_price'] ?? 0.0,
    ]);
}

function update_material($id, $data)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('UPDATE raw_materials SET name = ?, description = ?, unit = ?, current_quantity = ?, alert_threshold = ?, unit_price = ? WHERE id = ?');
    return $stmt->execute([
        $data['name'],
        $data['description'] ?? null,
        $data['unit'] ?? null,
        $data['current_quantity'] ?? 0,
        $data['alert_threshold'] ?? 0,
        $data['unit_price'] ?? 0.0,
        $id,
    ]);
}

function delete_material($id)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('DELETE FROM raw_materials WHERE id = ?');
    return $stmt->execute([$id]);
}

function get_low_stock_count()
{
    $pdo = get_pdo();
    $stmt = $pdo->query('SELECT COUNT(*) FROM raw_materials WHERE current_quantity <= alert_threshold');
    return (int)$stmt->fetchColumn();
}

function get_total_items_count()
{
    $pdo = get_pdo();
    $stmt = $pdo->query('SELECT COUNT(*) FROM raw_materials');
    return (int)$stmt->fetchColumn();
}

function get_total_stock_value()
{
    $pdo = get_pdo();
    $stmt = $pdo->query('SELECT SUM(current_quantity * unit_price) FROM raw_materials');
    return (float)$stmt->fetchColumn();
}

function record_stock_transaction($data)
{
    // $data keys: material_id, type (IN/OUT/ADJUST), quantity, price_per_unit, supplier_id|null, order_id|null, notes|null, performed_by|null
    $pdo = get_pdo();
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare('INSERT INTO stock_transactions (raw_material_id, type, quantity, price_per_unit, supplier_id, order_id, notes, performed_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $data['material_id'],
            $data['type'],
            $data['quantity'],
            $data['price_per_unit'] ?? null,
            $data['supplier_id'] ?? null,
            $data['order_id'] ?? null,
            $data['notes'] ?? null,
            $data['performed_by'] ?? null,
        ]);

        if ($data['type'] === 'IN') {
            $upd = $pdo->prepare('UPDATE raw_materials SET current_quantity = current_quantity + ? WHERE id = ?');
            $upd->execute([$data['quantity'], $data['material_id']]);
        } elseif ($data['type'] === 'OUT') {
            $upd = $pdo->prepare('UPDATE raw_materials SET current_quantity = current_quantity - ? WHERE id = ?');
            $upd->execute([$data['quantity'], $data['material_id']]);
        } elseif ($data['type'] === 'ADJUST') {
            if (isset($data['new_quantity'])) {
                $upd = $pdo->prepare('UPDATE raw_materials SET current_quantity = ? WHERE id = ?');
                $upd->execute([$data['new_quantity'], $data['material_id']]);
            } else {
                $upd = $pdo->prepare('UPDATE raw_materials SET current_quantity = current_quantity + ? WHERE id = ?');
                $upd->execute([$data['quantity'], $data['material_id']]);
            }
        }

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        return false;
    }
}

function get_stock_transactions($filters = [])
{
    $pdo = get_pdo();
    $sql = 'SELECT st.*, rm.name as material_name, s.name as supplier_name FROM stock_transactions st LEFT JOIN raw_materials rm ON st.raw_material_id = rm.id LEFT JOIN suppliers s ON st.supplier_id = s.id WHERE 1=1';
    $params = [];
    if (!empty($filters['material_id'])) { $sql .= ' AND st.raw_material_id = ?'; $params[] = $filters['material_id']; }
    if (!empty($filters['from'])) { $sql .= ' AND st.created_at >= ?'; $params[] = $filters['from']; }
    if (!empty($filters['to'])) { $sql .= ' AND st.created_at <= ?'; $params[] = $filters['to']; }
    $sql .= ' ORDER BY st.created_at DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Suppliers
function get_suppliers()
{
    $pdo = get_pdo();
    $stmt = $pdo->query('SELECT * FROM suppliers ORDER BY name');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_supplier_by_id($id)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM suppliers WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function create_supplier($data)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('INSERT INTO suppliers (name, contact_person, phone, email, address, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
    return $stmt->execute([$data['name'], $data['contact_person'] ?? null, $data['phone'] ?? null, $data['email'] ?? null, $data['address'] ?? null, $data['notes'] ?? null]);
}

function update_supplier($id, $data)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('UPDATE suppliers SET name = ?, contact_person = ?, phone = ?, email = ?, address = ?, notes = ? WHERE id = ?');
    return $stmt->execute([$data['name'], $data['contact_person'] ?? null, $data['phone'] ?? null, $data['email'] ?? null, $data['address'] ?? null, $data['notes'] ?? null, $id]);
}

function delete_supplier($id)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('DELETE FROM suppliers WHERE id = ?');
    return $stmt->execute([$id]);
}

?>

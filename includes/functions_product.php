<?php
require_once __DIR__ . '/../config/database.php';

function get_all_products()
{
    $pdo = get_pdo();
    $stmt = $pdo->query('SELECT * FROM products ORDER BY name');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_product_by_id($id)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function create_product($data)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('INSERT INTO products (product_code, name, description, category, standard_price, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    return $stmt->execute([$data['product_code'], $data['name'], $data['description'] ?? null, $data['category'] ?? null, $data['standard_price'] ?? 0]);
}

function update_product($id, $data)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('UPDATE products SET product_code = ?, name = ?, description = ?, category = ?, standard_price = ? WHERE id = ?');
    return $stmt->execute([$data['product_code'], $data['name'], $data['description'] ?? null, $data['category'] ?? null, $data['standard_price'] ?? 0, $id]);
}

function delete_product($id)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    return $stmt->execute([$id]);
}

/* BOM functions */
function get_bom_for_product($product_id)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT b.*, rm.name as material_name, rm.unit_price FROM bill_of_materials b LEFT JOIN raw_materials rm ON b.raw_material_id = rm.id WHERE b.product_id = ? ORDER BY rm.name');
    $stmt->execute([$product_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function add_bom_item($product_id, $material_id, $quantity)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('INSERT INTO bill_of_materials (product_id, raw_material_id, quantity_required) VALUES (?, ?, ?)');
    return $stmt->execute([$product_id, $material_id, $quantity]);
}

function remove_bom_item($bom_id)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('DELETE FROM bill_of_materials WHERE id = ?');
    return $stmt->execute([$bom_id]);
}

function calculate_bom_cost($product_id)
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT SUM(b.quantity_required * COALESCE(rm.unit_price,0)) as cost FROM bill_of_materials b LEFT JOIN raw_materials rm ON b.raw_material_id = rm.id WHERE b.product_id = ?');
    $stmt->execute([$product_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return (float)($row['cost'] ?? 0);
}

?>

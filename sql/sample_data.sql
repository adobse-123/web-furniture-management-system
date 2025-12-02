-- Sample data for testing the furniture system

-- Customers
INSERT INTO customers (name, email, phone) VALUES
('Alice Furniture', 'alice@example.com', '1234567890'),
('Bob Interiors', 'bob@example.com', '0987654321');

-- Employees (simple)
INSERT INTO employees (name, email, role) VALUES
('Eve Worker', 'eve@example.com', 'employee'),
('Adam Supervisor', 'adam@example.com', 'employee');

-- Suppliers
INSERT INTO suppliers (name, contact) VALUES ('Default Supplier', 'supplier@example.com');

-- Raw materials
INSERT INTO raw_materials (name, supplier_id, unit_price, current_quantity, reorder_level) VALUES
('Teak Wood', 1, 25.00, 100, 10),
('Plywood', 1, 10.00, 200, 20);

-- Products
INSERT INTO products (name, description, unit_price) VALUES
('Dining Table', '4-seater dining table', 250.00),
('Chair', 'Wooden chair', 45.00);

-- BOM for Dining Table (product_id 1)
INSERT INTO bill_of_materials (product_id, raw_material_id, quantity_required) VALUES
(1, 1, 10),
(1, 2, 5),
(2, 2, 2);

-- Example order
INSERT INTO orders (customer_id, delivery_date, notes, status, total_amount, advance_payment, created_by, created_at) VALUES
(1, DATE_ADD(NOW(), INTERVAL 7 DAY), 'Sample order', 'Pending', 290.00, 50.00, 1, NOW());

INSERT INTO order_items (order_id, product_id, quantity, unit_price, total_price) VALUES
(LAST_INSERT_ID(), 1, 1, 250.00, 250.00),
(LAST_INSERT_ID(), 2, 1, 40.00, 40.00);

-- Payments
CREATE TABLE IF NOT EXISTS payments (id INT AUTO_INCREMENT PRIMARY KEY, order_id INT, amount DECIMAL(10,2), method VARCHAR(50), performed_by INT, created_at DATETIME);
INSERT INTO payments (order_id, amount, method, performed_by, created_at) VALUES (1, 50.00, 'Advance', 1, NOW());

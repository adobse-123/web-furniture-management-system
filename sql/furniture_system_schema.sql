-- Furniture Management System schema
-- Database: furniture_db
-- Run this file in MySQL to create all core tables and relationships.

SET FOREIGN_KEY_CHECKS=0;
CREATE DATABASE IF NOT EXISTS `furniture_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `furniture_db`;

-- users table (core authentication table)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `phone` VARCHAR(20),
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','employee','customer') DEFAULT 'customer',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- customers
CREATE TABLE IF NOT EXISTS `customers` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT UNIQUE,
  `company_name` VARCHAR(100),
  `address` TEXT,
  `tax_number` VARCHAR(50),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_customers_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- employees
CREATE TABLE IF NOT EXISTS `employees` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT UNIQUE,
  `employee_id` VARCHAR(20) UNIQUE,
  `department` VARCHAR(50),
  `position` VARCHAR(50),
  `salary` DECIMAL(10,2),
  `hire_date` DATE,
  `emergency_contact` VARCHAR(100),
  CONSTRAINT `fk_employees_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- raw materials
CREATE TABLE IF NOT EXISTS `raw_materials` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `material_name` VARCHAR(100) UNIQUE,
  `description` TEXT,
  `unit` VARCHAR(20),
  `current_quantity` DECIMAL(10,2) DEFAULT 0,
  `alert_threshold` DECIMAL(10,2) DEFAULT 0,
  `unit_price` DECIMAL(10,2) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- suppliers
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `supplier_name` VARCHAR(100),
  `contact_person` VARCHAR(100),
  `phone` VARCHAR(20),
  `email` VARCHAR(100),
  `address` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- products
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `product_code` VARCHAR(30) UNIQUE,
  `product_name` VARCHAR(100),
  `description` TEXT,
  `category` VARCHAR(50),
  `standard_price` DECIMAL(10,2) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `order_number` VARCHAR(50) UNIQUE,
  `customer_id` INT,
  `order_date` DATE DEFAULT (CURRENT_DATE),
  `delivery_date` DATE NULL,
  `status` ENUM('Pending','In Progress','Quality Check','Completed','Delivered','Cancelled') DEFAULT 'Pending',
  `total_amount` DECIMAL(10,2) DEFAULT 0,
  `advance_payment` DECIMAL(10,2) DEFAULT 0,
  `assigned_employee_id` INT NULL,
  `notes` TEXT,
  CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_orders_employee` FOREIGN KEY (`assigned_employee_id`) REFERENCES `employees`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- order_items
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `order_id` INT,
  `product_id` INT,
  `quantity` INT DEFAULT 1,
  `unit_price` DECIMAL(10,2) DEFAULT 0,
  `subtotal` DECIMAL(10,2) DEFAULT 0,
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- bill_of_materials
CREATE TABLE IF NOT EXISTS `bill_of_materials` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `product_id` INT,
  `material_id` INT,
  `quantity_required` DECIMAL(10,2) DEFAULT 0,
  CONSTRAINT `fk_bom_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bom_material` FOREIGN KEY (`material_id`) REFERENCES `raw_materials`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- stock_transactions
CREATE TABLE IF NOT EXISTS `stock_transactions` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `material_id` INT,
  `transaction_type` ENUM('IN','OUT','ADJUST'),
  `quantity` DECIMAL(10,2) DEFAULT 0,
  `related_order_id` INT NULL,
  `related_supplier_id` INT NULL,
  `notes` TEXT,
  `performed_by` INT,
  `transaction_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_stock_mat` FOREIGN KEY (`material_id`) REFERENCES `raw_materials`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_stock_order` FOREIGN KEY (`related_order_id`) REFERENCES `orders`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_stock_supplier` FOREIGN KEY (`related_supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_stock_user` FOREIGN KEY (`performed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- attendances
CREATE TABLE IF NOT EXISTS `attendances` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `employee_id` INT,
  `date` DATE,
  `check_in` TIME,
  `check_out` TIME,
  `status` ENUM('Present','Absent','Late','Leave'),
  CONSTRAINT `fk_attendance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;

-- End of schema

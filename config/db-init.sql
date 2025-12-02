-- SQL initialization for furniture_db
CREATE DATABASE IF NOT EXISTS furniture_db;
USE furniture_db;

CREATE TABLE IF NOT EXISTS users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  phone VARCHAR(20),
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','employee','customer') DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS password_resets (
  email VARCHAR(100) NOT NULL,
  token VARCHAR(255) NOT NULL,
  expiry DATETIME NOT NULL
);

-- Example admin insert (update password hash after creating password)
-- INSERT INTO users (full_name, email, phone, password, role) VALUES ('Admin','admin@example.com','','<hashed_password>','admin');

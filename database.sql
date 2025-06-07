CREATE DATABASE IF NOT EXISTS service_order;
USE service_order;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'client') NOT NULL DEFAULT 'client'
);

CREATE TABLE clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cpf VARCHAR(11) NOT NULL UNIQUE,
    address TEXT NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    description VARCHAR(100) NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    warranty_period INT NOT NULL
);

CREATE TABLE status (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE service_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    opening_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    client_id INT NOT NULL,
    product_id INT NOT NULL,
    status_id INT NOT NULL,
    description TEXT,
    created_by INT NOT NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (status_id) REFERENCES status(id) ON DELETE RESTRICT
);

CREATE TABLE service_order_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_order_id INT NOT NULL,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_order_id) REFERENCES service_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@example.com', '$2y$10$M.oVUyjb9rBwaRtnXJ7kJuFoUzARwRTBl6Z/FxqeMQw34TpsWkR3e', 'admin');

INSERT INTO status (name) VALUES 
('Aberto'),
('Em progresso'),
('Completo'),
('Cancelado');

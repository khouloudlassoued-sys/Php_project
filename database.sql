-- Création de la base de données
CREATE DATABASE IF NOT EXISTS hotel_reservation;
USE hotel_reservation;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'client') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des chambres
CREATE TABLE IF NOT EXISTS rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_number VARCHAR(10) NOT NULL UNIQUE,
    type VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    capacity INT NOT NULL,
    description TEXT,
    status ENUM('disponible', 'occupée', 'maintenance') DEFAULT 'disponible'
);

-- Table des réservations
CREATE TABLE IF NOT EXISTS reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    status ENUM('en attente', 'confirmée', 'annulée') DEFAULT 'en attente',
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- Insertion de données de test
INSERT INTO users (username, password, email, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@hotel.com', 'admin'),
('client', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client@hotel.com', 'client');

INSERT INTO rooms (room_number, type, price, capacity, description) VALUES 
('101', 'Simple', 100.00, 1, 'Chambre simple avec vue sur la ville'),
('102', 'Double', 150.00, 2, 'Chambre double avec lit king size'),
('201', 'Suite', 250.00, 2, 'Suite luxueuse avec salon privé'); 
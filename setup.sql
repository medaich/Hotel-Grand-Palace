-- Hotel Grand Palace - Database Setup
-- Run: mysql -u root -p < setup.sql

CREATE DATABASE IF NOT EXISTS hotel_db;
USE hotel_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,       -- stored as MD5
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'staff',     -- 'admin', 'manager', 'staff'
    full_name VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active TINYINT(1) DEFAULT 1,
    reset_token VARCHAR(100),
    notes TEXT
);

-- Rooms table
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) NOT NULL UNIQUE,
    room_type VARCHAR(50),               -- 'single','double','suite','penthouse'
    floor INT,
    capacity INT DEFAULT 2,
    price_per_night DECIMAL(10,2),
    status VARCHAR(20) DEFAULT 'available', -- 'available','occupied','maintenance'
    description TEXT,
    amenities TEXT,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Guests table
CREATE TABLE IF NOT EXISTS guests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(100),
    phone VARCHAR(20),
    id_number VARCHAR(50),               -- passport / national ID
    nationality VARCHAR(50),
    address TEXT,
    date_of_birth DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT
);

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_ref VARCHAR(20),
    guest_id INT,
    room_id INT,
    check_in DATE,
    check_out DATE,
    adults INT DEFAULT 1,
    children INT DEFAULT 0,
    total_price DECIMAL(10,2),
    status VARCHAR(20) DEFAULT 'pending', -- 'pending','confirmed','checked_in','checked_out','cancelled'
    payment_status VARCHAR(20) DEFAULT 'unpaid',
    payment_method VARCHAR(30),
    special_requests TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (guest_id) REFERENCES guests(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- Services / charges table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    service_name VARCHAR(100),
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10,2),
    total DECIMAL(10,2),
    added_by INT,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

-- Maintenance requests
CREATE TABLE IF NOT EXISTS maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT,
    reported_by INT,
    issue TEXT,
    priority VARCHAR(20) DEFAULT 'normal',
    status VARCHAR(20) DEFAULT 'open',
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL
);

-- Messages / internal notes
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,
    subject VARCHAR(200),
    body TEXT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Activity log
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- Seed Data
-- =============================================
-- admin / admin123  |  manager / manager123  |  staff / staff123
INSERT INTO users (username, password, email, role, full_name, phone) VALUES
('admin',   '0192023a7bbd73250516f069df18b500',   'admin@grandpalace.com',   'admin',   'System Administrator', '+1-555-0001'),
('manager', '0795151defba7a4b5dfa89170de46277', 'manager@grandpalace.com', 'manager', 'John Manager',         '+1-555-0002'),
('staff',   'de9bf5643eabf80f4a56fda3bbb84483',   'staff@grandpalace.com',   'staff',   'Jane Staff',           '+1-555-0003'),
('carlos',  '5f4dcc3b5aa765d61d8327deb882cf99',   'carlos@grandpalace.com',  'staff',   'Carlos Rodriguez',     '+1-555-0004');

INSERT INTO rooms (room_number, room_type, floor, capacity, price_per_night, status, description, amenities) VALUES
('101', 'single',    1, 1,  89.00, 'available',    'Cozy single room with garden view',      'WiFi, TV, AC, Mini-bar'),
('102', 'single',    1, 1,  89.00, 'available',    'Cozy single room with garden view',      'WiFi, TV, AC'),
('201', 'double',    2, 2, 149.00, 'available',    'Spacious double room with city view',    'WiFi, TV, AC, Mini-bar, Safe'),
('202', 'double',    2, 2, 149.00, 'occupied',     'Spacious double room with city view',    'WiFi, TV, AC, Mini-bar, Safe'),
('301', 'suite',     3, 4, 299.00, 'available',    'Luxury suite with panoramic view',       'WiFi, TV, AC, Jacuzzi, Mini-bar, Safe, Lounge'),
('401', 'penthouse', 4, 6, 599.00, 'available',    'Premium penthouse with rooftop access',  'WiFi, TV, AC, Jacuzzi, Kitchen, Bar, Terrace'),
('105', 'single',    1, 1,  89.00, 'maintenance',  'Under renovation',                       'WiFi, TV'),
('203', 'double',    2, 2, 149.00, 'available',    'Double room with pool view',             'WiFi, TV, AC, Mini-bar');

INSERT INTO guests (first_name, last_name, email, phone, id_number, nationality, address) VALUES
('Alice',   'Johnson',  'alice@email.com',   '+1-555-1001', 'P123456', 'American', '123 Main St, New York, USA'),
('Bob',     'Smith',    'bob@email.com',     '+1-555-1002', 'P654321', 'British',  '45 Oak Ave, London, UK'),
('Maria',   'Garcia',   'maria@email.com',   '+34-555-001', 'P789012', 'Spanish',  'Calle Mayor 10, Madrid, Spain'),
('Ahmed',   'Al-Rashid','ahmed@email.com',   '+971-555-01', 'P345678', 'Emirati',  'Sheikh Zayed Rd, Dubai, UAE');

INSERT INTO bookings (booking_ref, guest_id, room_id, check_in, check_out, adults, total_price, status, payment_status, special_requests, created_by) VALUES
('BK-2024-001', 1, 3, '2024-12-01', '2024-12-05', 2, 596.00, 'checked_out', 'paid',      'Late checkout please',        1),
('BK-2024-002', 2, 4, '2024-12-10', '2024-12-15', 2, 745.00, 'confirmed',   'paid',      'Champagne on arrival',        1),
('BK-2024-003', 3, 5, '2024-12-20', '2024-12-25', 3, 1495.00,'pending',     'unpaid',    'Need baby cot',               2),
('BK-2024-004', 4, 6, '2024-12-28', '2025-01-02', 4, 2995.00,'confirmed',   'partial',   'Airport pickup required',     1);

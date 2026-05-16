-- ============================================================
-- Hotel Grand Palace - Database Seeder
-- Wipes all rows and re-seeds every table with demo data.
-- Usage: mysql -u root hotel_db < seeder.sql
--        (or paste into phpMyAdmin / MySQL Workbench)
-- ============================================================

USE hotel_db;

-- ── 1. Disable FK checks so we can truncate in any order ─────
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE activity_log;
TRUNCATE TABLE messages;
TRUNCATE TABLE services;
TRUNCATE TABLE maintenance;
TRUNCATE TABLE bookings;
TRUNCATE TABLE guests;
TRUNCATE TABLE rooms;
TRUNCATE TABLE users;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- USERS
-- admin/admin123 | manager/manager123 | staff/staff123 | carlos/password
-- ============================================================
INSERT INTO users (username, password, email, role, full_name, phone, is_active, notes) VALUES
('admin',   '0192023a7bbd73250516f069df18b500', 'admin@grandpalace.com',   'admin',   'System Administrator', '+1-555-0001', 1, NULL),
('manager', '0795151defba7a4b5dfa89170de46277', 'manager@grandpalace.com', 'manager', 'John Manager',         '+1-555-0002', 1, NULL),
('staff',   'de9bf5643eabf80f4a56fda3bbb84483', 'staff@grandpalace.com',   'staff',   'Jane Staff',           '+1-555-0003', 1, NULL),
('carlos',  '5f4dcc3b5aa765d61d8327deb882cf99', 'carlos@grandpalace.com',  'staff',   'Carlos Rodriguez',     '+1-555-0004', 1, NULL),
('sara',    'e10adc3949ba59abbe56e057f20f883e', 'sara@grandpalace.com',    'staff',   'Sara Ali',             '+1-555-0005', 1, 'Night shift supervisor'),
('mike',    '25d55ad283aa400af464c76d713c07ad', 'mike@grandpalace.com',    'manager', 'Mike Thompson',        '+1-555-0006', 1, NULL);
-- sara/123456  |  mike/12345678

-- ============================================================
-- ROOMS
-- ============================================================
INSERT INTO rooms (room_number, room_type, floor, capacity, price_per_night, status, description, amenities) VALUES
('101', 'single',    1, 1,  89.00, 'available',   'Cozy single room with garden view',              'WiFi, TV, AC, Mini-bar'),
('102', 'single',    1, 1,  89.00, 'available',   'Quiet single room overlooking the courtyard',    'WiFi, TV, AC'),
('103', 'single',    1, 1,  89.00, 'occupied',    'Single room with work desk',                     'WiFi, TV, AC, Desk'),
('105', 'single',    1, 1,  89.00, 'maintenance', 'Under renovation – electrical upgrade',          'WiFi, TV'),
('201', 'double',    2, 2, 149.00, 'available',   'Spacious double room with city view',            'WiFi, TV, AC, Mini-bar, Safe'),
('202', 'double',    2, 2, 149.00, 'occupied',    'Double room with king-size bed',                 'WiFi, TV, AC, Mini-bar, Safe'),
('203', 'double',    2, 2, 149.00, 'available',   'Double room with pool view',                     'WiFi, TV, AC, Mini-bar'),
('204', 'double',    2, 2, 149.00, 'available',   'Double room – corner unit, extra light',         'WiFi, TV, AC, Mini-bar, Safe'),
('205', 'double',    2, 2, 149.00, 'maintenance', 'Plumbing repair in progress',                   'WiFi, TV, AC'),
('301', 'suite',     3, 4, 299.00, 'available',   'Luxury suite with panoramic city view',          'WiFi, TV, AC, Jacuzzi, Mini-bar, Safe, Lounge'),
('302', 'suite',     3, 4, 299.00, 'occupied',    'Executive suite with private balcony',           'WiFi, TV, AC, Jacuzzi, Mini-bar, Safe, Lounge, Balcony'),
('303', 'suite',     3, 4, 299.00, 'available',   'Family suite with two queen beds',               'WiFi, TV, AC, Mini-bar, Safe, Sofa Bed'),
('401', 'penthouse', 4, 6, 599.00, 'available',   'Premium penthouse with rooftop terrace access',  'WiFi, TV, AC, Jacuzzi, Kitchen, Bar, Terrace'),
('402', 'penthouse', 4, 6, 599.00, 'occupied',    'Grand penthouse with private pool',              'WiFi, TV, AC, Jacuzzi, Kitchen, Private Pool, Bar, Terrace');

-- ============================================================
-- GUESTS
-- ============================================================
INSERT INTO guests (first_name, last_name, email, phone, id_number, nationality, address, date_of_birth, notes, created_by) VALUES
('Alice',    'Johnson',   'alice@email.com',    '+1-555-1001',  'P123456',  'American',   '123 Main St, New York, USA',              '1985-03-14', NULL,                           1),
('Bob',      'Smith',     'bob@email.com',      '+1-555-1002',  'P654321',  'British',    '45 Oak Ave, London, UK',                  '1979-07-22', 'VIP guest – prefers top floor', 1),
('Maria',    'Garcia',    'maria@email.com',    '+34-555-001',  'P789012',  'Spanish',    'Calle Mayor 10, Madrid, Spain',            '1990-11-05', NULL,                           2),
('Ahmed',    'Al-Rashid', 'ahmed@email.com',    '+971-555-001', 'P345678',  'Emirati',    'Sheikh Zayed Rd, Dubai, UAE',             '1975-01-30', 'Requires halal meals',          1),
('Yuki',     'Tanaka',    'yuki@email.com',     '+81-555-001',  'P112233',  'Japanese',   '1-1 Shinjuku, Tokyo, Japan',              '1992-09-18', NULL,                           3),
('Lena',     'Mueller',   'lena@email.com',     '+49-555-001',  'P998877',  'German',     'Hauptstrasse 5, Berlin, Germany',         '1988-06-03', NULL,                           3),
('Carlos',   'Fernandez', 'cfernandez@email.com','+52-555-001', 'P556677',  'Mexican',    'Av. Reforma 200, Mexico City, Mexico',    '1983-12-12', 'Allergic to nuts',              2),
('Sophie',   'Dubois',    'sophie@email.com',   '+33-555-001',  'P334455',  'French',     '12 Rue de Rivoli, Paris, France',         '1995-04-27', NULL,                           1),
('Omar',     'Hassan',    'omar@email.com',     '+20-555-001',  'P778899',  'Egyptian',   '15 Tahrir Sq, Cairo, Egypt',              '1980-08-15', NULL,                           2),
('Priya',    'Patel',     'priya@email.com',    '+91-555-001',  'P221144',  'Indian',     '42 MG Road, Bangalore, India',            '1993-02-08', 'Early check-in requested',      1);

-- ============================================================
-- BOOKINGS
-- guest_id refs: 1=Alice 2=Bob 3=Maria 4=Ahmed 5=Yuki 6=Lena 7=Carlos 8=Sophie 9=Omar 10=Priya
-- room_id refs:  1=101 2=102 3=103 4=105 5=201 6=202 7=203 8=204 9=205 10=301 11=302 12=303 13=401 14=402
-- ============================================================
INSERT INTO bookings (booking_ref, guest_id, room_id, check_in, check_out, adults, children, total_price, status, payment_status, payment_method, special_requests, created_by) VALUES
('BK-2024-001',  1,  5, '2024-10-01', '2024-10-05', 2, 0,  596.00,  'checked_out', 'paid',    'credit_card',   'Late checkout please',           1),
('BK-2024-002',  2,  6, '2024-10-10', '2024-10-15', 2, 0,  745.00,  'checked_out', 'paid',    'cash',          'Champagne on arrival',            1),
('BK-2024-003',  3, 10, '2024-11-01', '2024-11-06', 2, 1, 1495.00,  'checked_out', 'paid',    'bank_transfer',  'Need baby cot',                  2),
('BK-2024-004',  4, 13, '2024-11-15', '2024-11-20', 4, 0, 2995.00,  'checked_out', 'paid',    'credit_card',   'Airport pickup required',         1),
('BK-2024-005',  5,  1, '2024-12-01', '2024-12-04', 1, 0,  267.00,  'checked_out', 'paid',    'online',        'Non-smoking room please',         3),
('BK-2024-006',  6,  7, '2024-12-05', '2024-12-09', 2, 0,  596.00,  'checked_out', 'paid',    'cash',          NULL,                              2),
('BK-2024-007',  7, 12, '2024-12-10', '2024-12-13', 3, 1,  897.00,  'checked_out', 'paid',    'credit_card',   'Extra towels daily',              1),
('BK-2024-008',  8,  8, '2024-12-15', '2024-12-18', 2, 0,  447.00,  'checked_out', 'paid',    'online',        NULL,                              3),
('BK-2025-001',  9,  6, '2025-01-05', '2025-01-10', 2, 0,  745.00,  'confirmed',   'paid',    'credit_card',   'Quiet room away from elevator',   2),
('BK-2025-002', 10,  1, '2025-01-12', '2025-01-15', 1, 0,  267.00,  'confirmed',   'partial', 'bank_transfer',  'Early check-in if possible',     1),
('BK-2025-003',  1, 14, '2025-02-01', '2025-02-07', 4, 2, 3594.00,  'pending',     'unpaid',  'online',        'Anniversary decoration in room',  1),
('BK-2025-004',  3, 11, '2025-02-14', '2025-02-16', 2, 0,  598.00,  'confirmed',   'paid',    'credit_card',   'Valentines surprise setup',       2),
('BK-2025-005',  5,  5, '2025-03-01', '2025-03-03', 2, 0,  298.00,  'pending',     'unpaid',  'cash',          NULL,                              3),
('BK-2025-006',  2, 13, '2025-03-10', '2025-03-15', 3, 1, 2995.00,  'cancelled',   'unpaid',  'credit_card',   'Flight cancelled – rescheduling', 1);

-- ============================================================
-- SERVICES (charges on bookings)
-- booking_id refs above
-- ============================================================
INSERT INTO services (booking_id, service_name, quantity, unit_price, total, added_by) VALUES
(1,  'Room Service – Dinner',   1,  55.00,  55.00, 3),
(1,  'Minibar Restock',         2,  20.00,  40.00, 3),
(2,  'Spa Treatment',           2, 120.00, 240.00, 3),
(2,  'Champagne Bottle',        1,  85.00,  85.00, 2),
(3,  'Baby Cot Rental',         5,  10.00,  50.00, 2),
(3,  'Room Service – Breakfast',5,  25.00, 125.00, 3),
(4,  'Airport Transfer',        1,  75.00,  75.00, 1),
(4,  'Private Dining',          1, 250.00, 250.00, 2),
(4,  'Laundry Service',         3,  15.00,  45.00, 3),
(5,  'Tour Package',            1, 150.00, 150.00, 3),
(6,  'Room Service – Lunch',    2,  35.00,  70.00, 3),
(7,  'Extra Towels',            3,   5.00,  15.00, 3),
(7,  'Minibar Restock',         1,  20.00,  20.00, 3),
(8,  'Late Checkout Fee',       1,  40.00,  40.00, 1),
(9,  'Airport Transfer',        1,  75.00,  75.00, 2),
(10, 'Early Check-in Fee',      1,  30.00,  30.00, 1);

-- ============================================================
-- MAINTENANCE REQUESTS
-- room_id refs rooms table (1=101, 2=102, 3=103, 4=105 ...)
-- reported_by refs users table
-- ============================================================
INSERT INTO maintenance (room_id, reported_by, issue, priority, status, reported_at, resolved_at) VALUES
(4,  3, 'Electrical wiring needs full replacement',               'critical', 'open',     '2024-11-20 09:00:00', NULL),
(9,  2, 'Burst pipe under bathroom sink',                         'critical', 'open',     '2025-01-02 14:30:00', NULL),
(1,  3, 'Air conditioning unit making loud noise',               'high',     'resolved', '2024-10-15 10:00:00', '2024-10-16 15:00:00'),
(5,  2, 'Broken safe lock – guest cannot access',                'high',     'resolved', '2024-11-05 11:20:00', '2024-11-05 16:00:00'),
(6,  3, 'TV remote not working, replace batteries and remote',   'normal',   'resolved', '2024-12-01 08:00:00', '2024-12-01 10:30:00'),
(7,  3, 'Shower drain slow – needs cleaning',                    'normal',   'open',     '2025-01-10 09:45:00', NULL),
(10, 2, 'Jacuzzi jets not firing correctly',                     'high',     'open',     '2025-01-14 13:00:00', NULL),
(11, 3, 'Balcony door handle loose',                             'normal',   'resolved', '2025-01-08 11:00:00', '2025-01-09 09:00:00'),
(13, 1, 'Kitchen fridge temperature too warm',                   'high',     'open',     '2025-01-20 07:30:00', NULL),
(2,  3, 'Light bulb replacement in bathroom',                    'low',      'resolved', '2024-12-20 14:00:00', '2024-12-20 15:00:00');

-- ============================================================
-- MESSAGES (internal staff messages)
-- sender_id refs users table
-- ============================================================
INSERT INTO messages (sender_id, subject, body, is_read) VALUES
(1, 'Welcome to Hotel Grand Palace Management System',
   'Please familiarise yourself with the system. Report any issues to the admin.',
   1),
(2, 'Staffing Rota – January 2025',
   'Please check the attached rota for January. Night shifts have been updated. Contact manager if you have conflicts.',
   1),
(1, 'Security Reminder – Log Out After Each Shift',
   'All staff must log out of the system after their shift ends. Shared terminals must not remain logged in.',
   0),
(3, 'Room 105 – Renovation Update',
   'Contractors estimate Room 105 electrical work will be complete by end of next week. Room 205 plumbing should follow.',
   0),
(2, 'VIP Guest Arrival – Ahmed Al-Rashid',
   'Mr Al-Rashid is arriving tomorrow. Ensure penthouse 401 is fully stocked and halal meal options are arranged with the kitchen.',
   1),
(1, 'Monthly Revenue Report – November 2024',
   'November revenue came in at $12,450. Occupancy rate was 73%. Full report available in the Reports section.',
   1),
(3, 'Maintenance Escalation – Room 301 Jacuzzi',
   'The jacuzzi in suite 301 is still not working. Parts have been ordered. ETA 3-5 business days. Please inform any affected guests.',
   0),
(2, 'Staff Training – Fire Safety Drill',
   'Mandatory fire safety drill scheduled for next Friday at 10:00. All staff must attend. Meet at the main entrance.',
   0),
(1, 'System Maintenance Window',
   'The management system will undergo routine maintenance on Sunday 02:00–04:00. Please save your work before then.',
   0),
(3, 'Guest Feedback – Alice Johnson',
   'Mrs Johnson left a 5-star review! She specifically praised the cleanliness and the front-desk staff. Well done team!',
   1);

-- ============================================================
-- ACTIVITY LOG
-- ============================================================
INSERT INTO activity_log (user_id, action, ip_address, user_agent) VALUES
(1, 'User logged in',                              '192.168.1.10', 'Mozilla/5.0 (Windows NT 10.0)'),
(1, 'Added room 401 – penthouse',                  '192.168.1.10', 'Mozilla/5.0 (Windows NT 10.0)'),
(2, 'User logged in',                              '192.168.1.11', 'Mozilla/5.0 (Macintosh)'),
(2, 'Created booking BK-2024-001 for Alice Johnson','192.168.1.11','Mozilla/5.0 (Macintosh)'),
(3, 'User logged in',                              '192.168.1.12', 'Mozilla/5.0 (Linux)'),
(3, 'Submitted maintenance request for room 101',  '192.168.1.12', 'Mozilla/5.0 (Linux)'),
(1, 'Deleted user account: guest_test',            '192.168.1.10', 'Mozilla/5.0 (Windows NT 10.0)'),
(2, 'Updated booking BK-2024-002 status to confirmed', '192.168.1.11', 'Mozilla/5.0 (Macintosh)'),
(3, 'Added service charge: Spa Treatment on BK-2024-002', '192.168.1.12', 'Mozilla/5.0 (Linux)'),
(1, 'Password reset for user: carlos',             '192.168.1.10', 'Mozilla/5.0 (Windows NT 10.0)'),
(4, 'User logged in',                              '10.0.0.5',     'Mozilla/5.0 (iPhone; CPU iPhone OS 16)'),
(4, 'Registered guest: Yuki Tanaka',               '10.0.0.5',     'Mozilla/5.0 (iPhone; CPU iPhone OS 16)'),
(5, 'User logged in',                              '10.0.0.6',     'Mozilla/5.0 (Android 13)'),
(5, 'Resolved maintenance request #6',             '10.0.0.6',     'Mozilla/5.0 (Android 13)'),
(1, 'Viewed phpinfo()',                             '192.168.1.10', 'Mozilla/5.0 (Windows NT 10.0)'),
(2, 'Exported report: Revenue November 2024',      '192.168.1.11', 'Mozilla/5.0 (Macintosh)');

-- ============================================================
-- Done. All tables re-seeded.
-- ============================================================

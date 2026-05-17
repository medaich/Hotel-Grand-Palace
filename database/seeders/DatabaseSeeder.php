<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Room;
use App\Models\Guest;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Maintenance;
use App\Models\Message;
use App\Models\ActivityLog;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        ActivityLog::truncate();
        Message::truncate();
        Service::truncate();
        Maintenance::truncate();
        Booking::truncate();
        Guest::truncate();
        Room::truncate();
        User::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // ── USERS ───────────────────────────────────────────────────
        User::insert([
            ['username' => 'admin',   'password' => Hash::make('admin123'),  'email' => 'admin@grandpalace.com',   'role' => 'admin',   'full_name' => 'System Administrator', 'phone' => '+1-555-0001', 'is_active' => 1],
            ['username' => 'manager', 'password' => Hash::make('manager123'),'email' => 'manager@grandpalace.com', 'role' => 'manager', 'full_name' => 'John Manager',         'phone' => '+1-555-0002', 'is_active' => 1],
            ['username' => 'staff',   'password' => Hash::make('staff123'),  'email' => 'staff@grandpalace.com',   'role' => 'staff',   'full_name' => 'Jane Staff',           'phone' => '+1-555-0003', 'is_active' => 1],
            ['username' => 'carlos',  'password' => Hash::make('password'),  'email' => 'carlos@grandpalace.com',  'role' => 'staff',   'full_name' => 'Carlos Rodriguez',     'phone' => '+1-555-0004', 'is_active' => 1],
            ['username' => 'sara',    'password' => Hash::make('123456'),    'email' => 'sara@grandpalace.com',    'role' => 'staff',   'full_name' => 'Sara Ali',             'phone' => '+1-555-0005', 'is_active' => 1],
            ['username' => 'mike',    'password' => Hash::make('12345678'),  'email' => 'mike@grandpalace.com',    'role' => 'manager', 'full_name' => 'Mike Thompson',        'phone' => '+1-555-0006', 'is_active' => 1],
        ]);

        // ── ROOMS ───────────────────────────────────────────────────
        Room::insert([
            ['room_number' => '101', 'room_type' => 'single',    'floor' => 1, 'capacity' => 1, 'price_per_night' => 89.00,  'status' => 'available',   'description' => 'Cozy single room with garden view',              'amenities' => 'WiFi, TV, AC, Mini-bar'],
            ['room_number' => '102', 'room_type' => 'single',    'floor' => 1, 'capacity' => 1, 'price_per_night' => 89.00,  'status' => 'available',   'description' => 'Quiet single room overlooking the courtyard',    'amenities' => 'WiFi, TV, AC'],
            ['room_number' => '103', 'room_type' => 'single',    'floor' => 1, 'capacity' => 1, 'price_per_night' => 89.00,  'status' => 'occupied',    'description' => 'Single room with work desk',                     'amenities' => 'WiFi, TV, AC, Desk'],
            ['room_number' => '105', 'room_type' => 'single',    'floor' => 1, 'capacity' => 1, 'price_per_night' => 89.00,  'status' => 'maintenance', 'description' => 'Under renovation – electrical upgrade',          'amenities' => 'WiFi, TV'],
            ['room_number' => '201', 'room_type' => 'double',    'floor' => 2, 'capacity' => 2, 'price_per_night' => 149.00, 'status' => 'available',   'description' => 'Spacious double room with city view',            'amenities' => 'WiFi, TV, AC, Mini-bar, Safe'],
            ['room_number' => '202', 'room_type' => 'double',    'floor' => 2, 'capacity' => 2, 'price_per_night' => 149.00, 'status' => 'occupied',    'description' => 'Double room with king-size bed',                 'amenities' => 'WiFi, TV, AC, Mini-bar, Safe'],
            ['room_number' => '203', 'room_type' => 'double',    'floor' => 2, 'capacity' => 2, 'price_per_night' => 149.00, 'status' => 'available',   'description' => 'Double room with pool view',                     'amenities' => 'WiFi, TV, AC, Mini-bar'],
            ['room_number' => '204', 'room_type' => 'double',    'floor' => 2, 'capacity' => 2, 'price_per_night' => 149.00, 'status' => 'available',   'description' => 'Double room – corner unit, extra light',         'amenities' => 'WiFi, TV, AC, Mini-bar, Safe'],
            ['room_number' => '205', 'room_type' => 'double',    'floor' => 2, 'capacity' => 2, 'price_per_night' => 149.00, 'status' => 'maintenance', 'description' => 'Plumbing repair in progress',                   'amenities' => 'WiFi, TV, AC'],
            ['room_number' => '301', 'room_type' => 'suite',     'floor' => 3, 'capacity' => 4, 'price_per_night' => 299.00, 'status' => 'available',   'description' => 'Luxury suite with panoramic city view',          'amenities' => 'WiFi, TV, AC, Jacuzzi, Mini-bar, Safe, Lounge'],
            ['room_number' => '302', 'room_type' => 'suite',     'floor' => 3, 'capacity' => 4, 'price_per_night' => 299.00, 'status' => 'occupied',    'description' => 'Executive suite with private balcony',           'amenities' => 'WiFi, TV, AC, Jacuzzi, Mini-bar, Safe, Lounge, Balcony'],
            ['room_number' => '303', 'room_type' => 'suite',     'floor' => 3, 'capacity' => 4, 'price_per_night' => 299.00, 'status' => 'available',   'description' => 'Family suite with two queen beds',               'amenities' => 'WiFi, TV, AC, Mini-bar, Safe, Sofa Bed'],
            ['room_number' => '401', 'room_type' => 'penthouse', 'floor' => 4, 'capacity' => 6, 'price_per_night' => 599.00, 'status' => 'available',   'description' => 'Premium penthouse with rooftop terrace access',  'amenities' => 'WiFi, TV, AC, Jacuzzi, Kitchen, Bar, Terrace'],
            ['room_number' => '402', 'room_type' => 'penthouse', 'floor' => 4, 'capacity' => 6, 'price_per_night' => 599.00, 'status' => 'occupied',    'description' => 'Grand penthouse with private pool',              'amenities' => 'WiFi, TV, AC, Jacuzzi, Kitchen, Private Pool, Bar, Terrace'],
        ]);

        // ── GUESTS ──────────────────────────────────────────────────
        Guest::insert([
            ['first_name' => 'Alice',  'last_name' => 'Johnson',   'email' => 'alice@email.com',    'phone' => '+1-555-1001', 'id_number' => 'P123456', 'nationality' => 'American', 'address' => '123 Main St, New York, USA',           'date_of_birth' => '1985-03-14', 'notes' => NULL,                           'created_by' => 1],
            ['first_name' => 'Bob',    'last_name' => 'Smith',     'email' => 'bob@email.com',      'phone' => '+1-555-1002', 'id_number' => 'P654321', 'nationality' => 'British',  'address' => '45 Oak Ave, London, UK',               'date_of_birth' => '1979-07-22', 'notes' => 'VIP guest – prefers top floor', 'created_by' => 1],
            ['first_name' => 'Maria',  'last_name' => 'Garcia',    'email' => 'maria@email.com',    'phone' => '+34-555-001', 'id_number' => 'P789012', 'nationality' => 'Spanish',  'address' => 'Calle Mayor 10, Madrid, Spain',        'date_of_birth' => '1990-11-05', 'notes' => NULL,                           'created_by' => 2],
            ['first_name' => 'Ahmed',  'last_name' => 'Al-Rashid', 'email' => 'ahmed@email.com',    'phone' => '+971-555-001','id_number' => 'P345678', 'nationality' => 'Emirati',  'address' => 'Sheikh Zayed Rd, Dubai, UAE',          'date_of_birth' => '1975-01-30', 'notes' => 'Requires halal meals',          'created_by' => 1],
            ['first_name' => 'Yuki',   'last_name' => 'Tanaka',    'email' => 'yuki@email.com',     'phone' => '+81-555-001', 'id_number' => 'P112233', 'nationality' => 'Japanese', 'address' => '1-1 Shinjuku, Tokyo, Japan',           'date_of_birth' => '1992-09-18', 'notes' => NULL,                           'created_by' => 3],
            ['first_name' => 'Lena',   'last_name' => 'Mueller',   'email' => 'lena@email.com',     'phone' => '+49-555-001', 'id_number' => 'P998877', 'nationality' => 'German',   'address' => 'Hauptstrasse 5, Berlin, Germany',      'date_of_birth' => '1988-06-03', 'notes' => NULL,                           'created_by' => 3],
            ['first_name' => 'Carlos', 'last_name' => 'Fernandez', 'email' => 'cfernandez@email.com','phone'=> '+52-555-001', 'id_number' => 'P556677', 'nationality' => 'Mexican',  'address' => 'Av. Reforma 200, Mexico City, Mexico', 'date_of_birth' => '1983-12-12', 'notes' => 'Allergic to nuts',              'created_by' => 2],
            ['first_name' => 'Sophie', 'last_name' => 'Dubois',    'email' => 'sophie@email.com',   'phone' => '+33-555-001', 'id_number' => 'P334455', 'nationality' => 'French',   'address' => '12 Rue de Rivoli, Paris, France',      'date_of_birth' => '1995-04-27', 'notes' => NULL,                           'created_by' => 1],
            ['first_name' => 'Omar',   'last_name' => 'Hassan',    'email' => 'omar@email.com',     'phone' => '+20-555-001', 'id_number' => 'P778899', 'nationality' => 'Egyptian', 'address' => '15 Tahrir Sq, Cairo, Egypt',           'date_of_birth' => '1980-08-15', 'notes' => NULL,                           'created_by' => 2],
            ['first_name' => 'Priya',  'last_name' => 'Patel',     'email' => 'priya@email.com',    'phone' => '+91-555-001', 'id_number' => 'P221144', 'nationality' => 'Indian',   'address' => '42 MG Road, Bangalore, India',         'date_of_birth' => '1993-02-08', 'notes' => 'Early check-in requested',      'created_by' => 1],
        ]);

        // ── BOOKINGS ────────────────────────────────────────────────
        Booking::insert([
            ['booking_ref' => 'BK-2024-001', 'guest_id' => 1,  'room_id' => 5,  'check_in' => '2024-10-01', 'check_out' => '2024-10-05', 'adults' => 2, 'children' => 0, 'total_price' => 596.00,  'status' => 'checked_out', 'payment_status' => 'paid',    'payment_method' => 'credit_card',   'special_requests' => 'Late checkout please',           'created_by' => 1],
            ['booking_ref' => 'BK-2024-002', 'guest_id' => 2,  'room_id' => 6,  'check_in' => '2024-10-10', 'check_out' => '2024-10-15', 'adults' => 2, 'children' => 0, 'total_price' => 745.00,  'status' => 'checked_out', 'payment_status' => 'paid',    'payment_method' => 'cash',          'special_requests' => 'Champagne on arrival',            'created_by' => 1],
            ['booking_ref' => 'BK-2024-003', 'guest_id' => 3,  'room_id' => 10, 'check_in' => '2024-11-01', 'check_out' => '2024-11-06', 'adults' => 2, 'children' => 1, 'total_price' => 1495.00, 'status' => 'checked_out', 'payment_status' => 'paid',    'payment_method' => 'bank_transfer', 'special_requests' => 'Need baby cot',                  'created_by' => 2],
            ['booking_ref' => 'BK-2024-004', 'guest_id' => 4,  'room_id' => 13, 'check_in' => '2024-11-15', 'check_out' => '2024-11-20', 'adults' => 4, 'children' => 0, 'total_price' => 2995.00, 'status' => 'checked_out', 'payment_status' => 'paid',    'payment_method' => 'credit_card',   'special_requests' => 'Airport pickup required',         'created_by' => 1],
            ['booking_ref' => 'BK-2024-005', 'guest_id' => 5,  'room_id' => 1,  'check_in' => '2024-12-01', 'check_out' => '2024-12-04', 'adults' => 1, 'children' => 0, 'total_price' => 267.00,  'status' => 'checked_out', 'payment_status' => 'paid',    'payment_method' => 'online',        'special_requests' => 'Non-smoking room please',         'created_by' => 3],
            ['booking_ref' => 'BK-2024-006', 'guest_id' => 6,  'room_id' => 7,  'check_in' => '2024-12-05', 'check_out' => '2024-12-09', 'adults' => 2, 'children' => 0, 'total_price' => 596.00,  'status' => 'checked_out', 'payment_status' => 'paid',    'payment_method' => 'cash',          'special_requests' => NULL,                              'created_by' => 2],
            ['booking_ref' => 'BK-2024-007', 'guest_id' => 7,  'room_id' => 12, 'check_in' => '2024-12-10', 'check_out' => '2024-12-13', 'adults' => 3, 'children' => 1, 'total_price' => 897.00,  'status' => 'checked_out', 'payment_status' => 'paid',    'payment_method' => 'credit_card',   'special_requests' => 'Extra towels daily',              'created_by' => 1],
            ['booking_ref' => 'BK-2024-008', 'guest_id' => 8,  'room_id' => 8,  'check_in' => '2024-12-15', 'check_out' => '2024-12-18', 'adults' => 2, 'children' => 0, 'total_price' => 447.00,  'status' => 'checked_out', 'payment_status' => 'paid',    'payment_method' => 'online',        'special_requests' => NULL,                              'created_by' => 3],
            ['booking_ref' => 'BK-2025-001', 'guest_id' => 9,  'room_id' => 6,  'check_in' => '2025-01-05', 'check_out' => '2025-01-10', 'adults' => 2, 'children' => 0, 'total_price' => 745.00,  'status' => 'confirmed',   'payment_status' => 'paid',    'payment_method' => 'credit_card',   'special_requests' => 'Quiet room away from elevator',   'created_by' => 2],
            ['booking_ref' => 'BK-2025-002', 'guest_id' => 10, 'room_id' => 1,  'check_in' => '2025-01-12', 'check_out' => '2025-01-15', 'adults' => 1, 'children' => 0, 'total_price' => 267.00,  'status' => 'confirmed',   'payment_status' => 'partial', 'payment_method' => 'bank_transfer', 'special_requests' => 'Early check-in if possible',      'created_by' => 1],
            ['booking_ref' => 'BK-2025-003', 'guest_id' => 1,  'room_id' => 14, 'check_in' => '2025-02-01', 'check_out' => '2025-02-07', 'adults' => 4, 'children' => 2, 'total_price' => 3594.00, 'status' => 'pending',     'payment_status' => 'unpaid',  'payment_method' => 'online',        'special_requests' => 'Anniversary decoration in room',  'created_by' => 1],
            ['booking_ref' => 'BK-2025-004', 'guest_id' => 3,  'room_id' => 11, 'check_in' => '2025-02-14', 'check_out' => '2025-02-16', 'adults' => 2, 'children' => 0, 'total_price' => 598.00,  'status' => 'confirmed',   'payment_status' => 'paid',    'payment_method' => 'credit_card',   'special_requests' => 'Valentines surprise setup',       'created_by' => 2],
            ['booking_ref' => 'BK-2025-005', 'guest_id' => 5,  'room_id' => 5,  'check_in' => '2025-03-01', 'check_out' => '2025-03-03', 'adults' => 2, 'children' => 0, 'total_price' => 298.00,  'status' => 'pending',     'payment_status' => 'unpaid',  'payment_method' => 'cash',          'special_requests' => NULL,                              'created_by' => 3],
            ['booking_ref' => 'BK-2025-006', 'guest_id' => 2,  'room_id' => 13, 'check_in' => '2025-03-10', 'check_out' => '2025-03-15', 'adults' => 3, 'children' => 1, 'total_price' => 2995.00, 'status' => 'cancelled',   'payment_status' => 'unpaid',  'payment_method' => 'credit_card',   'special_requests' => 'Flight cancelled – rescheduling', 'created_by' => 1],
        ]);

        // ── SERVICES ────────────────────────────────────────────────
        Service::insert([
            ['booking_id' => 1,  'service_name' => 'Room Service – Dinner',   'quantity' => 1, 'unit_price' => 55.00,  'total' => 55.00,  'added_by' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['booking_id' => 1,  'service_name' => 'Minibar Restock',         'quantity' => 2, 'unit_price' => 20.00,  'total' => 40.00,  'added_by' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['booking_id' => 2,  'service_name' => 'Spa Treatment',           'quantity' => 2, 'unit_price' => 120.00, 'total' => 240.00, 'added_by' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['booking_id' => 2,  'service_name' => 'Champagne Bottle',        'quantity' => 1, 'unit_price' => 85.00,  'total' => 85.00,  'added_by' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['booking_id' => 3,  'service_name' => 'Baby Cot Rental',         'quantity' => 5, 'unit_price' => 10.00,  'total' => 50.00,  'added_by' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['booking_id' => 3,  'service_name' => 'Room Service – Breakfast','quantity' => 5, 'unit_price' => 25.00,  'total' => 125.00, 'added_by' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['booking_id' => 4,  'service_name' => 'Airport Transfer',        'quantity' => 1, 'unit_price' => 75.00,  'total' => 75.00,  'added_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['booking_id' => 4,  'service_name' => 'Private Dining',          'quantity' => 1, 'unit_price' => 250.00, 'total' => 250.00, 'added_by' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['booking_id' => 4,  'service_name' => 'Laundry Service',         'quantity' => 3, 'unit_price' => 15.00,  'total' => 45.00,  'added_by' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['booking_id' => 5,  'service_name' => 'Tour Package',            'quantity' => 1, 'unit_price' => 150.00, 'total' => 150.00, 'added_by' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['booking_id' => 6,  'service_name' => 'Room Service – Lunch',    'quantity' => 2, 'unit_price' => 35.00,  'total' => 70.00,  'added_by' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['booking_id' => 7,  'service_name' => 'Extra Towels',            'quantity' => 3, 'unit_price' => 5.00,   'total' => 15.00,  'added_by' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['booking_id' => 7,  'service_name' => 'Minibar Restock',         'quantity' => 1, 'unit_price' => 20.00,  'total' => 20.00,  'added_by' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['booking_id' => 8,  'service_name' => 'Late Checkout Fee',       'quantity' => 1, 'unit_price' => 40.00,  'total' => 40.00,  'added_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['booking_id' => 9,  'service_name' => 'Airport Transfer',        'quantity' => 1, 'unit_price' => 75.00,  'total' => 75.00,  'added_by' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['booking_id' => 10, 'service_name' => 'Early Check-in Fee',      'quantity' => 1, 'unit_price' => 30.00,  'total' => 30.00,  'added_by' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── MAINTENANCE ─────────────────────────────────────────────
        Maintenance::insert([
            ['room_id' => 4,  'reported_by' => 3, 'issue' => 'Electrical wiring needs full replacement',               'priority' => 'critical', 'status' => 'open',     'created_at' => '2024-11-20 09:00:00', 'resolved_at' => NULL],
            ['room_id' => 9,  'reported_by' => 2, 'issue' => 'Burst pipe under bathroom sink',                         'priority' => 'critical', 'status' => 'open',     'created_at' => '2025-01-02 14:30:00', 'resolved_at' => NULL],
            ['room_id' => 1,  'reported_by' => 3, 'issue' => 'Air conditioning unit making loud noise',                'priority' => 'high',     'status' => 'resolved', 'created_at' => '2024-10-15 10:00:00', 'resolved_at' => '2024-10-16 15:00:00'],
            ['room_id' => 5,  'reported_by' => 2, 'issue' => 'Broken safe lock – guest cannot access',                 'priority' => 'high',     'status' => 'resolved', 'created_at' => '2024-11-05 11:20:00', 'resolved_at' => '2024-11-05 16:00:00'],
            ['room_id' => 6,  'reported_by' => 3, 'issue' => 'TV remote not working, replace batteries and remote',    'priority' => 'normal',   'status' => 'resolved', 'created_at' => '2024-12-01 08:00:00', 'resolved_at' => '2024-12-01 10:30:00'],
            ['room_id' => 7,  'reported_by' => 3, 'issue' => 'Shower drain slow – needs cleaning',                     'priority' => 'normal',   'status' => 'open',     'created_at' => '2025-01-10 09:45:00', 'resolved_at' => NULL],
            ['room_id' => 10, 'reported_by' => 2, 'issue' => 'Jacuzzi jets not firing correctly',                      'priority' => 'high',     'status' => 'open',     'created_at' => '2025-01-14 13:00:00', 'resolved_at' => NULL],
            ['room_id' => 11, 'reported_by' => 3, 'issue' => 'Balcony door handle loose',                              'priority' => 'normal',   'status' => 'resolved', 'created_at' => '2025-01-08 11:00:00', 'resolved_at' => '2025-01-09 09:00:00'],
            ['room_id' => 13, 'reported_by' => 1, 'issue' => 'Kitchen fridge temperature too warm',                    'priority' => 'high',     'status' => 'open',     'created_at' => '2025-01-20 07:30:00', 'resolved_at' => NULL],
            ['room_id' => 2,  'reported_by' => 3, 'issue' => 'Light bulb replacement in bathroom',                     'priority' => 'low',      'status' => 'resolved', 'created_at' => '2024-12-20 14:00:00', 'resolved_at' => '2024-12-20 15:00:00'],
        ]);

        // ── MESSAGES ────────────────────────────────────────────────
        Message::insert([
            ['sender_id' => 1, 'subject' => 'Welcome to Hotel Grand Palace Management System', 'body' => 'Please familiarise yourself with the system. Report any issues to the admin.', 'is_read' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['sender_id' => 2, 'subject' => 'Staffing Rota – January 2025', 'body' => 'Please check the attached rota for January. Night shifts have been updated. Contact manager if you have conflicts.', 'is_read' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['sender_id' => 1, 'subject' => 'Security Reminder – Log Out After Each Shift', 'body' => 'All staff must log out of the system after their shift ends. Shared terminals must not remain logged in.', 'is_read' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['sender_id' => 3, 'subject' => 'Room 105 – Renovation Update', 'body' => 'Contractors estimate Room 105 electrical work will be complete by end of next week. Room 205 plumbing should follow.', 'is_read' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['sender_id' => 2, 'subject' => 'VIP Guest Arrival – Ahmed Al-Rashid', 'body' => 'Mr Al-Rashid is arriving tomorrow. Ensure penthouse 401 is fully stocked and halal meal options are arranged with the kitchen.', 'is_read' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['sender_id' => 1, 'subject' => 'Monthly Revenue Report – November 2024', 'body' => 'November revenue came in at $12,450. Occupancy rate was 73%. Full report available in the Reports section.', 'is_read' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['sender_id' => 3, 'subject' => 'Maintenance Escalation – Room 301 Jacuzzi', 'body' => 'The jacuzzi in suite 301 is still not working. Parts have been ordered. ETA 3-5 business days. Please inform any affected guests.', 'is_read' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['sender_id' => 2, 'subject' => 'Staff Training – Fire Safety Drill', 'body' => 'Mandatory fire safety drill scheduled for next Friday at 10:00. All staff must attend. Meet at the main entrance.', 'is_read' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['sender_id' => 1, 'subject' => 'System Maintenance Window', 'body' => 'The management system will undergo routine maintenance on Sunday 02:00–04:00. Please save your work before then.', 'is_read' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['sender_id' => 3, 'subject' => 'Guest Feedback – Alice Johnson', 'body' => 'Mrs Johnson left a 5-star review! She specifically praised the cleanliness and the front-desk staff. Well done team!', 'is_read' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── ACTIVITY LOGS ───────────────────────────────────────────
        ActivityLog::insert([
            ['user_id' => 1, 'action' => 'User logged in',                              'ip_address' => '192.168.1.10', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0)', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 1, 'action' => 'Added room 401 – penthouse',                  'ip_address' => '192.168.1.10', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0)', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 2, 'action' => 'User logged in',                              'ip_address' => '192.168.1.11', 'user_agent' => 'Mozilla/5.0 (Macintosh)', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 2, 'action' => 'Created booking BK-2024-001 for Alice Johnson','ip_address'=> '192.168.1.11', 'user_agent' => 'Mozilla/5.0 (Macintosh)', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 3, 'action' => 'User logged in',                              'ip_address' => '192.168.1.12', 'user_agent' => 'Mozilla/5.0 (Linux)', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 3, 'action' => 'Submitted maintenance request for room 101',  'ip_address' => '192.168.1.12', 'user_agent' => 'Mozilla/5.0 (Linux)', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 1, 'action' => 'Deleted user account: guest_test',            'ip_address' => '192.168.1.10', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0)', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 2, 'action' => 'Updated booking BK-2024-002 status to confirmed', 'ip_address' => '192.168.1.11', 'user_agent' => 'Mozilla/5.0 (Macintosh)', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 3, 'action' => 'Added service charge: Spa Treatment on BK-2024-002', 'ip_address' => '192.168.1.12', 'user_agent' => 'Mozilla/5.0 (Linux)', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 1, 'action' => 'Password reset for user: carlos',             'ip_address' => '192.168.1.10', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0)', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 4, 'action' => 'User logged in',                              'ip_address' => '10.0.0.5',     'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16)', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 4, 'action' => 'Registered guest: Yuki Tanaka',               'ip_address' => '10.0.0.5',     'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16)', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 5, 'action' => 'User logged in',                              'ip_address' => '10.0.0.6',     'user_agent' => 'Mozilla/5.0 (Android 13)', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 5, 'action' => 'Resolved maintenance request #6',             'ip_address' => '10.0.0.6',     'user_agent' => 'Mozilla/5.0 (Android 13)', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 1, 'action' => 'Viewed phpinfo()',                            'ip_address' => '192.168.1.10', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0)', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 2, 'action' => 'Exported report: Revenue November 2024',      'ip_address' => '192.168.1.11', 'user_agent' => 'Mozilla/5.0 (Macintosh)', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

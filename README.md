# Hotel Grand Palace — Management System

A deliberately vulnerable PHP hotel management web application built for **security auditing and penetration testing practice**.

> ⚠️ **Do not deploy this on a public server.** It contains intentional security vulnerabilities for educational purposes.

---

## Requirements

- PHP 8.0+
- MySQL 5.7+ / MariaDB
- A local web server (or PHP's built-in server)

---

## Setup

**1. Create the database**

```bash
mysql -u root hotel_db < setup.sql
```

**2. Configure environment**

```bash
cp .env.example .env
```

Then edit `.env` and set your values (at minimum `DB_PASS`):

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=your_password
DB_NAME=hotel_db
```

**3. Seed demo data** *(optional — wipes and re-populates all tables)*


```powershell
Get-Content seeder.sql | mysql -u root hotel_db
```

**4. Start the server**

```bash
php -S localhost:8000
```

Then open **http://localhost:8000** in your browser.

---

## Demo Credentials

| Username  | Password    | Role    |
|-----------|-------------|---------|
| `admin`   | `admin123`  | Admin   |
| `manager` | `manager123`| Manager |
| `staff`   | `staff123`  | Staff   |
| `carlos`  | `password`  | Staff   |

---

## Pages

| URL | Description |
|-----|-------------|
| `index.php` | Login |
| `dashboard.php` | Overview & stats |
| `rooms.php` | Room management |
| `bookings.php` | Booking management |
| `guests.php` | Guest profiles |
| `services.php` | Service charges |
| `maintenance.php` | Maintenance requests |
| `messages.php` | Internal messages |
| `reports.php` | Revenue & occupancy reports |
| `admin.php` | Admin panel (admin only) |
| `profile.php` | User profile & password |
| `reset_password.php` | Password reset flow |

---

## Project Structure

```
Hotel_management/
├── config.php              # DB credentials, constants, session start
├── index.php               # Login page
├── dashboard.php           # Main dashboard
├── *.php                   # Feature pages
├── includes/
│   ├── auth.php            # Session/role guards
│   ├── header.php          # Shared HTML header + sidebar
│   └── footer.php          # Shared HTML footer + JS
├── uploads/                # Room image uploads
├── logs/
│   └── app.log             # Application activity log
├── setup.sql               # Database schema
├── seeder.sql              # Demo data seeder
└── vulnerabilities.md      # Security audit reference
```

---
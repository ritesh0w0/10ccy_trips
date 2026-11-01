<div align="center">

  <img src="assets/logo.png" alt="10ccy Trips Logo" width="130" />

  # 🚌 10ccy Trips
  ### Online Bus Reservation & Fleet Management System

  [![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
  [![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
  [![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
  [![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](LICENSE)

  <p align="center">
    A full-stack, responsive bus ticketing and intercity fleet management web platform tailored for seamless travel across India. Featuring real-time interactive seat layouts, instant e-ticket generation, dynamic route scheduling, and a comprehensive administrator control panel.
  </p>

  [Key Features](#-key-features) • [Visual Showcase](#-visual-showcase) • [Database Architecture](#-database-architecture) • [Getting Started](#-getting-started) • [Default Credentials](#-default-credentials)

</div>

---

## 📖 Overview

**10ccy Trips** is a full-stack PHP web application engineered to simplify intercity transit booking and fleet administration. Designed with a clean Bootstrap 5 interface and normalized MySQL relational database, it provides passengers with a hassle-free booking journey—from route exploration and live seat selection to instant printable e-tickets—while providing bus operators and administrators with granular control over fleets, schedules, pricing, and reservations.

---

## ✨ Key Features

### 👤 Passenger Portal
- **Smart Fleet Search**: Filter upcoming departures by source city, destination, and travel date with instant availability counters.
- **Interactive Seat Matrix**: Real-time visual seat map showing driver cabin, available seats, booked seats, and multi-seat selection (up to 6 passengers per booking).
- **Instant E-Ticket Generation**: Verified digital travel voucher displaying booking reference `#TCK`, passenger names, boarding/arrival points, QR code placeholder, and printable format.
- **Trip History & Management**: View past, current, and upcoming journeys with live status badges (`CONFIRMED`, `COMPLETED`, `CANCELLED`).
- **User Authentication**: Secure session-based registration, login, and profile tracking.

### 🛡️ Fleet & Admin Management Portal
- **Fleet Management**: Register, update, and manage buses (Volvo Multi-Axle, Scania, BharatBenz AC Sleeper, Executive Seater) with seat capacities and registration plates.
- **Route Planning**: Define intercity corridors with source, destination, distance in kilometers, and estimated journey durations.
- **Schedule Dispatcher**: Assign buses to routes, schedule departure/arrival timestamps, and configure dynamic ticket fares in INR (₹).
- **Reservation Control**: Real-time visibility into all passenger bookings across schedules with cancellation handling.

---

## 📸 Visual Showcase

### 1. Landing & Authentication
| Welcome Landing Page | Secure User Login |
| :---: | :---: |
| <img src="docs/screenshots/01_landing_page.png" alt="Landing Page" width="550" /> | <img src="docs/screenshots/02_user_login.png" alt="User Login" width="550" /> |

### 2. User Dashboard & Journey Search
| Passenger Dashboard | Search & Bus Fleet Listings |
| :---: | :---: |
| <img src="docs/screenshots/03_user_dashboard.png" alt="User Dashboard" width="550" /> | <img src="docs/screenshots/04_bus_search_booking.png" alt="Bus Search" width="550" /> |

### 3. Seat Selection & Confirmation
| Interactive Seat Map & Passenger Details | Digital Travel E-Ticket |
| :---: | :---: |
| <img src="docs/screenshots/05_seat_selection.png" alt="Seat Selection" width="550" /> | <img src="docs/screenshots/06_eticket_confirmation.png" alt="E-Ticket" width="550" /> |

### 4. History & Administrative Control
| Booking History & Status | Fleet Admin Control Center |
| :---: | :---: |
| <img src="docs/screenshots/07_booking_history.png" alt="Booking History" width="550" /> | <img src="docs/screenshots/08_admin_dashboard.png" alt="Admin Dashboard" width="550" /> |

---

## 🛠️ Tech Stack

- **Backend**: PHP 8.x (Native Procedural & Object-Oriented with MySQLi)
- **Database**: MySQL 8.0 / MariaDB with Relational Foreign Key Integrity
- **Frontend**: HTML5, CSS3, JavaScript (ES6+), Bootstrap 5.3.3
- **Icons & Typography**: Bootstrap Icons, Font Awesome 6, Segoe UI / Inter
- **Security**: Password hashing (`bcrypt` via `password_hash()`), parameterized SQL statements, session role verification (`user` vs `admin`)

---

## 🗄️ Database Architecture

The system utilizes a normalized relational schema with cascading deletions and foreign key constraints:

```mermaid
erDiagram
    users ||--o{ bookings : "places"
    users ||--o{ feedback : "submits"
    buses ||--o{ schedules : "assigned to"
    routes ||--o{ schedules : "operates on"
    schedules ||--o{ bookings : "has"

    users {
        int id PK
        varchar full_name
        varchar email UK
        varchar username UK
        varchar password
        enum user_type
        timestamp created_at
    }

    buses {
        int id PK
        varchar bus_number
        varchar name
        varchar type
        int seats
        timestamp created_at
    }

    routes {
        int id PK
        varchar source
        varchar destination
        int distance_km
        int duration_minutes
        timestamp created_at
    }

    schedules {
        int id PK
        int bus_id FK
        int route_id FK
        datetime departure_time
        datetime arrival_time
        decimal price
        timestamp created_at
    }

    bookings {
        int id PK
        int user_id FK
        int schedule_id FK
        varchar seat_no
        varchar passenger_name
        enum status
        datetime booked_on
    }

    feedback {
        int id PK
        int user_id FK
        text message
        datetime submitted_on
    }
```

---

## 🚀 Getting Started

Follow these steps to set up the project locally on your machine.

### Prerequisites
- **Web Server**: Apache / Nginx or PHP Built-in Server
- **PHP**: PHP 8.0 or newer (with `mysqli` and `session` extensions enabled)
- **Database**: MySQL 5.7+ / 8.0+ or MariaDB (via XAMPP, WAMP, Laragon, or standalone MySQL)

### Step 1: Clone the Repository
```bash
git clone https://github.com/ritesh0w0/10ccy_trips.git
cd 10ccy_trips
```

### Step 2: Database Setup & Seeding
1. Open your MySQL client (phpMyAdmin, MySQL Workbench, or CLI).
2. Import the provided Indian Fleet seed file:
```bash
mysql -u root -p < seed_indian_data.sql
```
*(This automatically creates the `10ccy_trips` database and populates it with realistic Indian buses, routes such as Bengaluru–Chennai and Mumbai–Pune, active schedules, and demo accounts).*

### Step 3: Configure Database Connection
Verify the connection credentials in [`includes/db.php`](includes/db.php):
```php
<?php
$host = 'localhost';
$db   = '10ccy_trips';
$user = 'root';
$pass = ''; // Enter your local MySQL password if applicable

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

### Step 4: Run the Application
You can run it using your local server stack (e.g. XAMPP `htdocs`) or via the built-in PHP development server:
```bash
php -S localhost:8000
```
Open your browser and navigate to:
```
http://localhost:8000/index.php
```

---

## 🔑 Default Credentials

The seeded database comes pre-configured with the following test credentials:

| Role | Username / Identifier | Password | Access Level |
| :--- | :--- | :--- | :--- |
| **System Administrator** | `admin` | `admin123` | Full Fleet, Route, Schedule & Booking Admin Control |
| **Demo Passenger** | `ritesh0w0` (or `ritesh@10ccytrips.com`) | `admin123` | Search, Book Seats, View E-Tickets, Cancel Trips |
| **Passenger 2** | `rahul_s` (or `rahul.sharma@example.com`) | `admin123` | Standard Passenger Booking & History Access |
| **Passenger 3** | `priya_p` (or `priya.patel@example.com`) | `admin123` | Standard Passenger Booking & History Access |

---

## 📂 Project Directory Structure

```text
10ccy_trips/
│
├── assets/                     # Frontend static assets
│   ├── logo.png                # Brand logo
│   └── style.css               # Global application stylesheet
│
├── docs/                       # Project documentation
│   └── screenshots/            # Visual documentation & preview captures
│       ├── 01_landing_page.png
│       ├── 02_user_login.png
│       ├── 03_user_dashboard.png
│       ├── 04_bus_search_booking.png
│       ├── 05_seat_selection.png
│       ├── 06_eticket_confirmation.png
│       ├── 07_booking_history.png
│       └── 08_admin_dashboard.png
│
├── includes/                   # Reusable server-side modules
│   ├── db.php                  # MySQL database connection handler
│   ├── header.php              # Global layout header & navigation
│   └── footer.php              # Global footer template
│
├── admin_dashboard.php         # Admin control portal
├── admin_login.php             # Admin credential verification
├── book.php                    # Bus schedule search & fleet explorer
├── book_seats.php              # Seat reservation processor
├── booking_handler.php         # Booking transactional handler
├── booking_history.php         # Passenger trip history
├── cancel.php                  # Reservation cancellation handler
├── dashboard.php               # Passenger user dashboard
├── database.sql                # Base database schema
├── generate_hash.php           # Helper utility for bcrypt password hashing
├── index.php                   # Public landing page
├── load_seats.php              # Asynchronous seat layout builder
├── login-choice.php            # Portal selection (User vs Admin)
├── login.php                   # Passenger authentication
├── logout.php                  # Session termination
├── manage_bookings.php         # Admin booking management
├── manage_buses.php            # Admin fleet CRUD
├── manage_routes.php           # Admin intercity route CRUD
├── manage_schedules.php        # Admin timetable & fare manager
├── register.php                # Passenger self-registration
├── seed_indian_data.sql        # Database schema + Indian fleet seed dataset
├── ticket.php                  # Printable electronic travel voucher
├── .gitignore                  # Git tracking exclusion list (protects .env)
├── LICENSE                     # MIT License
└── README.md                   # Project documentation & visual guide
```

---

## 🔒 Security Best Practices

- **Credential Isolation**: `.env` and sensitive local configuration files are excluded via `.gitignore` to prevent secret exposure on remote repositories.
- **Prepared Statements**: Core database transactions employ parameterized queries to safeguard against SQL injection.
- **Session Segregation**: Separate session identifiers and role validations ensure user sessions cannot access administrative endpoints.

---

## 📜 License

This project is licensed under the [MIT License](LICENSE) - created by **[Ritesh Kulkarni](https://github.com/ritesh0w0)**.

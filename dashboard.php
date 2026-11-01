<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Initialize all variables with default values
$user_id = 0;
$full_name = 'User';
$email = '';
$first_name = 'User';
$active_bookings = 0;
$upcoming = null;

// Get user data with proper validation
if (isset($_SESSION['user'])) {
    $user_id = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
    $full_name = isset($_SESSION['user']['full_name']) ? htmlspecialchars($_SESSION['user']['full_name']) : 'User';
    $email = isset($_SESSION['user']['email']) ? htmlspecialchars($_SESSION['user']['email']) : '';
    
    // Get first name for welcome message
    $name_parts = explode(' ', $full_name);
    $first_name = ucfirst($name_parts[0]);
}

include 'includes/db.php';

try {
    // Count active bookings
    $active_stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND status = 'booked'");
    if ($active_stmt) {
        $active_stmt->bind_param("i", $user_id);
        $active_stmt->execute();
        $result = $active_stmt->get_result();
        $row = $result->fetch_row();
        $active_bookings = $row[0] ?? 0;
        $active_stmt->close();
    }

    // Get upcoming trip
    $upcoming_stmt = $conn->prepare("
        SELECT b.id, s.departure_time, bus.bus_number, route.source, route.destination 
        FROM bookings b
        JOIN schedules s ON b.schedule_id = s.id
        JOIN buses bus ON s.bus_id = bus.id
        JOIN routes route ON s.route_id = route.id
        WHERE b.user_id = ? AND b.status = 'booked' AND s.departure_time > NOW()
        ORDER BY s.departure_time ASC
        LIMIT 1
    ");
    if ($upcoming_stmt) {
        $upcoming_stmt->bind_param("i", $user_id);
        $upcoming_stmt->execute();
        $result = $upcoming_stmt->get_result();
        $upcoming = $result->fetch_assoc();
        $upcoming_stmt->close();
    }
} catch (Exception $e) {
    // Log error and continue with default values
    error_log("Dashboard error: " . $e->getMessage());
    $active_bookings = 0;
    $upcoming = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | 10ccy Trips</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --secondary: #4b5563;
            --light: #f9fafb;
            --dark: #111827;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        body {
            background-color: #f3f4f6;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .navbar {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 0.75rem 0;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: transform 0.15s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .stat-card {
            padding: 1.5rem;
        }

        .stat-card .icon {
            width: 3rem;
            height: 3rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .stat-card .value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-card .label {
            color: var(--secondary);
            font-size: 0.875rem;
        }

        .upcoming-trip {
            border-left: 4px solid var(--primary);
        }

        .trip-route {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .trip-meta {
            color: var(--secondary);
            font-size: 0.875rem;
        }

        .btn-action {
            padding: 1rem;
            font-weight: 500;
            border-radius: 0.5rem;
        }

        .welcome-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .user-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .history-btn {
            width: 100%;
            padding: 1rem;
            font-weight: 500;
            border-radius: 0.5rem;
            background-color: white;
            border: 1px solid var(--primary);
            color: var(--primary);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .history-btn:hover {
            background-color: var(--primary);
            color: white;
        }

        .history-btn i {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg mb-4">
        <div class="dashboard-container w-100">
            <div class="d-flex justify-content-between w-100 align-items-center">
                <a class="navbar-brand fw-bold text-primary" href="#">
                    <i class="bi bi-bus-front me-2"></i>10ccy Trips
                </a>
                <div class="d-flex align-items-center">
                    <div class="user-avatar me-2">
                        <?= strtoupper(substr($first_name, 0, 1)) ?>
                    </div>
                    <a href="logout.php" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="dashboard-container">
        <!-- Welcome Header -->
        <div class="welcome-header">
            <h1 class="h4 mb-2">Welcome back, <?= $first_name ?></h1>
            <p class="mb-0 opacity-75">Ready for your next journey?</p>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4 g-3">
            <div class="col-md-6">
                <div class="card stat-card h-100">
                    <div class="icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-ticket-perforated fs-4"></i>
                    </div>
                    <div class="value text-dark"><?= $active_bookings ?></div>
                    <div class="label">Active Bookings</div>
                </div>
            </div>
            <div class="col-md-6">
                <a href="book.php" class="text-decoration-none">
                    <div class="card stat-card h-100">
                        <div class="icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-plus-lg fs-4"></i>
                        </div>
                        <div class="value text-dark">New Trip</div>
                        <div class="label">Book your next adventure</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Upcoming Trip -->
        <?php if(!empty($upcoming) && is_array($upcoming)): ?>
        <div class="card mb-4 upcoming-trip">
            <div class="card-body">
                <h5 class="card-title mb-3">Upcoming Trip</h5>
                <div class="d-md-flex justify-content-between align-items-center">
                    <div>
                        <div class="trip-route">
                            <?= htmlspecialchars($upcoming['source'] ?? '') ?> 
                            <i class="bi bi-arrow-right mx-2 text-primary"></i>
                            <?= htmlspecialchars($upcoming['destination'] ?? '') ?>
                        </div>
                        <div class="trip-meta">
                            <span class="me-3">
                                <i class="bi bi-bus-front me-1"></i>
                                Bus #<?= htmlspecialchars($upcoming['bus_number'] ?? '') ?>
                            </span>
                            <span>
                                <i class="bi bi-clock me-1"></i>
                                <?= isset($upcoming['departure_time']) ? date('D, M j, Y H:i', strtotime($upcoming['departure_time'])) : '' ?>
                            </span>
                        </div>
                    </div>
                    <a href="booking_history.php" class="btn btn-primary mt-3 mt-md-0">
                        <i class="bi bi-eye me-1"></i> View Details
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Actions - Only My Bookings Button -->
        <h5 class="h5 mb-3">Quick Actions</h5>
        <div class="row">
            <div class="col-md-12">
                <a href="booking_history.php" class="history-btn">
                    <i class="bi bi-list-check me-2"></i> My Bookings
                </a>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
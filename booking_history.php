<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
include 'includes/db.php';

$user_id = $_SESSION['user']['id'];

// Get all bookings sorted by booking time (newest first)
$sql = "SELECT 
            b.id AS booking_id,
            b.status,
            b.seat_no,
            b.passenger_name,
            b.booked_on,
            s.departure_time,
            s.arrival_time,
            s.price,
            bus.bus_number,
            bus.name AS bus_name,
            route.source,
            route.destination
        FROM bookings b
        JOIN schedules s ON b.schedule_id = s.id
        JOIN buses bus ON s.bus_id = bus.id
        JOIN routes route ON s.route_id = route.id
        WHERE b.user_id = ?
        ORDER BY b.booked_on DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings | 10ccy Trips</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .booking-card {
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            overflow: hidden;
            transition: all 0.2s;
        }
        .booking-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .booking-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }
        .booking-body {
            padding: 15px;
        }
        .seat-badge {
            background-color: #198754;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .booking-meta {
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <!-- Replace the existing header div with this -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="dashboard.php" class="btn btn-outline-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
        <h2 class="d-inline-block">My Bookings</h2>
    </div>
    <a href="book.php" class="btn btn-primary">Book New Trip</a>
</div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-info mb-4"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <?php if (empty($bookings)): ?>
        <div class="alert alert-info">You have no bookings yet.</div>
    <?php else: ?>
        <?php foreach ($bookings as $booking): ?>
            <div class="booking-card">
                <div class="booking-header">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-1"><?= htmlspecialchars($booking['bus_number']) ?> - <?= htmlspecialchars($booking['bus_name']) ?></h5>
                            <p class="mb-0">
                                <?= htmlspecialchars($booking['source']) ?> → <?= htmlspecialchars($booking['destination']) ?>
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="badge status-badge bg-<?= $booking['status'] === 'booked' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($booking['status']) ?>
                            </span>
                            <div class="booking-meta mt-1">
                                <?= date('M j, Y H:i', strtotime($booking['departure_time'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="booking-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <span class="badge seat-badge">Seat <?= htmlspecialchars($booking['seat_no']) ?></span>
                        </div>
                        <div class="col-md-4">
                            <strong><?= htmlspecialchars($booking['passenger_name']) ?></strong>
                        </div>
                        <div class="col-md-2">
                            ₹<?= number_format($booking['price'], 2) ?>
                        </div>
                        <div class="col-md-4 text-end">
                            <small class="text-muted me-2">
                                Booked: <?= date('M j, Y H:i', strtotime($booking['booked_on'])) ?>
                            </small>
                            <a href="ticket.php?booking_id=<?= $booking['booking_id'] ?>" class="btn btn-sm btn-outline-primary me-1">View</a>
                            <?php if ($booking['status'] === 'booked'): ?>
                                <a href="cancel.php?cancel_id=<?= $booking['booking_id'] ?>" 
                                   onclick="return confirm('Are you sure you want to cancel seat <?= $booking['seat_no'] ?> for <?= htmlspecialchars($booking['passenger_name']) ?>?');"
                                   class="btn btn-sm btn-outline-danger">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
<?php
session_start();
include 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    $_SESSION['booking_error'] = "Please login to book seats";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$schedule_id = $_POST['schedule_id'] ?? null;
$seats = !empty($_POST['seats']) ? json_decode($_POST['seats'], true) : [];
$names = $_POST['passenger_names'] ?? [];

// Validate input
if (!$schedule_id || empty($seats) || count($seats) !== count($names)) {
    $_SESSION['booking_error'] = "Invalid booking data. Please select seats and enter passenger names.";
    header("Location: book.php");
    exit();
}

// Get bus details
$bus_query = $conn->prepare("
    SELECT b.seats, b.bus_number, b.name AS bus_name,
           r.source, r.destination, s.departure_time
    FROM schedules s
    JOIN buses b ON s.bus_id = b.id
    JOIN routes r ON s.route_id = r.id
    WHERE s.id = ?
");
$bus_query->bind_param("i", $schedule_id);
$bus_query->execute();
$bus_data = $bus_query->get_result()->fetch_assoc();
$bus_query->close();

if (!$bus_data) {
    $_SESSION['booking_error'] = "Bus schedule not found";
    header("Location: book.php");
    exit();
}

// Start transaction
$conn->begin_transaction();
$booking_ids = [];

try {
    foreach ($seats as $index => $seat_no) {
        $name = $conn->real_escape_string($names[$index]);
        
        // Check seat availability
        $check = $conn->prepare("SELECT id FROM bookings WHERE schedule_id = ? AND seat_no = ? AND status = 'booked'");
        $check->bind_param("ii", $schedule_id, $seat_no);
        $check->execute();
        
        if ($check->get_result()->num_rows > 0) {
            throw new Exception("Seat $seat_no is already booked");
        }
        
        // Insert booking
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, schedule_id, seat_no, passenger_name, status, booked_on) VALUES (?, ?, ?, ?, 'booked', NOW())");
        $stmt->bind_param("iiis", $user_id, $schedule_id, $seat_no, $name);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to book seat $seat_no");
        }
        
        $booking_ids[] = $conn->insert_id;
        $stmt->close();
    }
    
    $conn->commit();
    
    // Display success message instead of redirecting
    $success_message = "Seat booking confirmed! You can view your tickets in the 'My Bookings' section.";
    
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['booking_error'] = "Booking failed: " . $e->getMessage();
    header("Location: book.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation | 10ccy Trips</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h4>Booking Confirmed</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <h5>✅ <?= htmlspecialchars($success_message) ?></h5>
                    <p>Details of your booking:</p>
                    <ul>
                        <li>Bus: <?= htmlspecialchars($bus_data['bus_number']) ?> - <?= htmlspecialchars($bus_data['bus_name']) ?></li>
                        <li>Route: <?= htmlspecialchars($bus_data['source']) ?> to <?= htmlspecialchars($bus_data['destination']) ?></li>
                        <li>Departure: <?= htmlspecialchars($bus_data['departure_time']) ?></li>
                        <li>Seats Booked: <?= count($seats) ?></li>
                    </ul>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="book.php" class="btn btn-outline-primary">Book Another Seat</a>
                    <a href="booking_history.php" class="btn btn-primary">View My Bookings</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
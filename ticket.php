<?php 
session_start();
include('includes/db.php');

if (!isset($_GET['booking_id']) || !isset($_SESSION['user'])) {
    header("Location: book.php");
    exit();
}

$booking_id = intval($_GET['booking_id']);
$user_id = $_SESSION['user']['id'];

// Get the specific booking details
$booking_query = $conn->prepare("
    SELECT b.id, b.seat_no, b.passenger_name, b.status, b.booked_on,
           s.price, s.departure_time, s.arrival_time,
           bus.bus_number, bus.name AS bus_name,
           route.source, route.destination
    FROM bookings b
    JOIN schedules s ON b.schedule_id = s.id
    JOIN buses bus ON s.bus_id = bus.id
    JOIN routes route ON s.route_id = route.id
    WHERE b.id = ? AND b.user_id = ?
");
$booking_query->bind_param("ii", $booking_id, $user_id);
$booking_query->execute();
$booking_result = $booking_query->get_result();

if ($booking_result->num_rows == 0) {
    header("Location: book.php");
    exit();
}

$ticket = $booking_result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ticket | 10ccy Trips</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .ticket-container {
            max-width: 500px;
            margin: 30px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .ticket-header {
            background: linear-gradient(to right, #0d6efd, #0b5ed7);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .ticket-body {
            padding: 25px;
        }
        .ticket-details {
            margin-bottom: 20px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: bold;
            width: 120px;
            color: #6c757d;
        }
        .seat-badge {
            background-color: #198754;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 1rem;
        }
        .divider {
            border-top: 1px dashed #dee2e6;
            margin: 20px 0;
        }
        .fare-highlight {
            font-size: 1.2rem;
            color: #0d6efd;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="ticket-container">
    <div class="ticket-header">
        <h3>10ccy Trips</h3>
        <h5 class="mb-0">E-Ticket</h5>
    </div>
    
    <div class="ticket-body">
        <div class="ticket-details">
            <h4 class="text-center mb-4"><?= htmlspecialchars($ticket['bus_number']) ?> - <?= htmlspecialchars($ticket['bus_name']) ?></h4>
            
            <div class="detail-row">
                <div class="detail-label">Route:</div>
                <div><?= htmlspecialchars($ticket['source']) ?> → <?= htmlspecialchars($ticket['destination']) ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Departure:</div>
                <div><?= date('D, M j, Y H:i', strtotime($ticket['departure_time'])) ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Arrival:</div>
                <div><?= date('D, M j, Y H:i', strtotime($ticket['arrival_time'])) ?></div>
            </div>
            
            <div class="divider"></div>
            
            <div class="detail-row">
                <div class="detail-label">Passenger:</div>
                <div><strong><?= htmlspecialchars($ticket['passenger_name']) ?></strong></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Seat Number:</div>
                <div><span class="seat-badge"><?= htmlspecialchars($ticket['seat_no']) ?></span></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Booking ID:</div>
                <div>#<?= $ticket['id'] ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Booked On:</div>
                <div><?= date('M j, Y H:i', strtotime($ticket['booked_on'])) ?></div>
            </div>
            
            <div class="divider"></div>
            
            <div class="detail-row">
                <div class="detail-label">Fare:</div>
                <div class="fare-highlight">₹<?= number_format($ticket['price'], 2) ?></div>
            </div>
        </div>
        
        <div class="alert alert-info text-center mt-4">
            <strong>Please show this ticket when boarding</strong>
            <p class="mb-0 small">Valid ID required for passenger verification</p>
        </div>
    </div>
    
    <div class="p-3 text-center bg-light border-top">
        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
</div>
</body>
</html>
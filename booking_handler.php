<!-- FILE 1: booking_handler.php (called after Book Now form is submitted) -->
<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['schedule_id'])) {
    $user_id = $_SESSION['user_id'];
    $schedule_id = $_POST['schedule_id'];

    // Insert into bookings
    $conn->query("INSERT INTO bookings (user_id, schedule_id) VALUES ($user_id, $schedule_id)");
    $booking_id = $conn->insert_id;

    header("Location: ticket.php?booking_id=$booking_id");
    exit();
} else {
    echo "Invalid request.";
}
?>

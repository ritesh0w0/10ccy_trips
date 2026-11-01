<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$msg = '';

if (isset($_GET['cancel_id']) && is_numeric($_GET['cancel_id'])) {
    $booking_id = $_GET['cancel_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Verify the booking belongs to the user and is active
        $verify = $conn->prepare("
            SELECT id, seat_no, passenger_name 
            FROM bookings 
            WHERE id = ? AND user_id = ? AND status = 'booked'
        ");
        $verify->bind_param("ii", $booking_id, $user_id);
        $verify->execute();
        $verify_result = $verify->get_result();
        
        if ($verify_result->num_rows === 1) {
            $booking = $verify_result->fetch_assoc();
            
            // Cancel only this specific booking
            $cancel = $conn->prepare("
                UPDATE bookings 
                SET status = 'cancelled' 
                WHERE id = ? AND user_id = ?
            ");
            $cancel->bind_param("ii", $booking_id, $user_id);
            $cancel->execute();
            
            if ($cancel->affected_rows === 1) {
                $msg = "✅ Seat " . $booking['seat_no'] . " for " . $booking['passenger_name'] . " cancelled successfully.";
                $conn->commit();
            } else {
                throw new Exception("No booking was cancelled");
            }
        } else {
            throw new Exception("Booking not found or already cancelled");
        }
    } catch (Exception $e) {
        $conn->rollback();
        $msg = "❌ Error: " . $e->getMessage();
    }
    
    // Redirect back to booking history with message
    header("Location: booking_history.php?msg=" . urlencode($msg));
    exit();
}

// If no cancellation requested, redirect to booking history
header("Location: booking_history.php");
exit();
?>
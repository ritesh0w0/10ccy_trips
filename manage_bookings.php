<?php
session_start();
include 'includes/db.php';

// Admin authentication check
if (!isset($_SESSION['login_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

$msg = '';
$edit_mode = false;
$edit_data = [];

// Fetch users for dropdown
$users = $conn->query("SELECT id, full_name, email FROM users WHERE user_type = 'user' ORDER BY full_name ASC");

// Load booking for edit
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $stmt = $conn->prepare("
        SELECT b.*, u.full_name as user_name, u.email as user_email,
               s.departure_time, bus.bus_number
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN schedules s ON b.schedule_id = s.id
        JOIN buses bus ON s.bus_id = bus.id
        WHERE b.id = ? AND b.status = 'booked'
    ");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    
    if (!$edit_data) {
        header("Location: manage_bookings.php");
        exit;
    }
}

// Update booking
if (isset($_POST['update'])) {
    $booking_id = (int)$_POST['id'];
    $user_id = (int)$_POST['user_id'];
    $seat_no = trim($_POST['seat_no']);
    $passenger_name = trim($_POST['passenger_name']);
    $status = $_POST['status'];
    
    // Check if seat is available (excluding current booking)
    $check = $conn->prepare("SELECT id FROM bookings WHERE schedule_id = ? AND seat_no = ? AND status = 'booked' AND id != ?");
    $check->bind_param("isi", $edit_data['schedule_id'], $seat_no, $booking_id);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $msg = "Error: Seat $seat_no is already booked for this schedule.";
    } else {
        $stmt = $conn->prepare("UPDATE bookings SET 
            user_id = ?, 
            seat_no = ?, 
            passenger_name = ?, 
            status = ? 
            WHERE id = ?");
        $stmt->bind_param("isssi", $user_id, $seat_no, $passenger_name, $status, $booking_id);
        
        if ($stmt->execute()) {
            $msg = "Booking updated successfully!";
            header("Location: manage_bookings.php?msg=".urlencode($msg));
            exit;
        } else {
            $msg = "Error updating booking: " . $conn->error;
        }
    }
}

// Cancel booking
if (isset($_GET['cancel'])) {
    $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
    $stmt->bind_param("i", $_GET['cancel']);
    
    if ($stmt->execute()) {
        $msg = "Booking cancelled successfully!";
        header("Location: manage_bookings.php?msg=".urlencode($msg));
        exit;
    } else {
        $msg = "Error cancelling booking: " . $conn->error;
    }
}

// Fetch all bookings
$bookings = $conn->query("
    SELECT b.*, u.full_name as user_name, u.email as user_email,
           s.departure_time, bus.bus_number, r.source, r.destination
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN schedules s ON b.schedule_id = s.id
    JOIN buses bus ON s.bus_id = bus.id
    JOIN routes r ON s.route_id = r.id
    ORDER BY b.booked_on DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .booking-status {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-booked {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .form-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .edit-form {
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2 class="mb-4">📝 Manage Bookings</h2>
    
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-info"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <?php if ($edit_mode): ?>
    <!-- Edit Booking Form -->
    <div class="form-section edit-form">
        <h4>✏️ Edit Booking #<?= $edit_data['id'] ?></h4>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            
            <div class="mb-3">
                <label class="form-label">Bus & Schedule</label>
                <input type="text" class="form-control" 
                    value="<?= htmlspecialchars($edit_data['bus_number'] . ' | ' . date('d M Y, H:i', strtotime($edit_data['departure_time']))) ?>" readonly>
            </div>
            
            <div class="mb-3">
                <label class="form-label">User</label>
                <select name="user_id" class="form-control" required>
                    <?php 
                    $users->data_seek(0); // Reset pointer
                    while ($user = $users->fetch_assoc()): ?>
                        <option value="<?= $user['id'] ?>" 
                            <?= ($edit_data['user_id'] == $user['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['full_name'] . ' (' . $user['email'] . ')') ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Passenger Name</label>
                    <input type="text" name="passenger_name" class="form-control" required
                        value="<?= htmlspecialchars($edit_data['passenger_name']) ?>">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Seat Number</label>
                    <input type="text" name="seat_no" class="form-control" required 
                        value="<?= htmlspecialchars($edit_data['seat_no']) ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="booked" <?= ($edit_data['status'] == 'booked') ? 'selected' : '' ?>>Booked</option>
                    <option value="cancelled" <?= ($edit_data['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="submit" name="update" class="btn btn-primary">💾 Save Changes</button>
                <a href="manage_bookings.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- List of All Bookings -->
    <div class="form-section">
        <h4>All Bookings</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Bus</th>
                        <th>Departure</th>
                        <th>Passenger</th>
                        <th>Seat</th>
                        <th>Status</th>
                        <th>Booked On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = $bookings->fetch_assoc()): ?>
                        <tr>
                            <td><?= $booking['id'] ?></td>
                            <td>
                                <?= htmlspecialchars($booking['user_name']) ?>
                                <br><small><?= htmlspecialchars($booking['user_email']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($booking['bus_number']) ?></td>
                            <td><?= date('d M Y, H:i', strtotime($booking['departure_time'])) ?></td>
                            <td><?= htmlspecialchars($booking['passenger_name']) ?></td>
                            <td><?= htmlspecialchars($booking['seat_no']) ?></td>
                            <td>
                                <span class="booking-status status-<?= $booking['status'] ?>">
                                    <?= ucfirst($booking['status']) ?>
                                </span>
                            </td>
                            <td><?= date('d M Y, H:i', strtotime($booking['booked_on'])) ?></td>
                            <td>
                                <?php if ($booking['status'] == 'booked'): ?>
                                    <a href="manage_bookings.php?edit=<?= $booking['id'] ?>" class="btn btn-sm btn-primary">✏️ Edit</a>
                                    <a href="manage_bookings.php?cancel=<?= $booking['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this booking?')">❌ Cancel</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <a href="admin_dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>
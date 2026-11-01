<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['login_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

$edit_mode = false;
$edit_data = [];

// Fetch buses and routes for dropdowns
$buses = $conn->query("SELECT id, bus_number, name FROM buses ORDER BY bus_number ASC");
$routes = $conn->query("SELECT id, source, destination FROM routes ORDER BY source ASC");

// Add Schedule
if (isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO schedules (bus_id, route_id, departure_time, arrival_time, price) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissd", $_POST['bus_id'], $_POST['route_id'], $_POST['departure_time'], $_POST['arrival_time'], $_POST['price']);
    $stmt->execute();
    header("Location: manage_schedules.php");
    exit;
}

// Delete Schedule
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM schedules WHERE id = " . $_GET['delete']);
    header("Location: manage_schedules.php");
    exit;
}

// Load for Edit
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $stmt = $conn->prepare("SELECT * FROM schedules WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
}

// Update Schedule
if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE schedules SET bus_id = ?, route_id = ?, departure_time = ?, arrival_time = ?, price = ? WHERE id = ?");
    $stmt->bind_param("iissdi", $_POST['bus_id'], $_POST['route_id'], $_POST['departure_time'], $_POST['arrival_time'], $_POST['price'], $_POST['id']);
    $stmt->execute();
    header("Location: manage_schedules.php");
    exit;
}

// Fetch all schedules for display
$schedules = $conn->query("SELECT s.*, b.bus_number, b.name as bus_name, r.source, r.destination FROM schedules s JOIN buses b ON s.bus_id = b.id JOIN routes r ON s.route_id = r.id ORDER BY s.departure_time ASC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Schedules</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>📅 Manage Schedules</h2>

    <form method="POST" class="bg-white p-4 shadow mb-4 rounded">
        <h5><?= $edit_mode ? "✏️ Edit Schedule" : "➕ Add New Schedule" ?></h5>
        <?php if ($edit_mode): ?>
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
        <?php endif; ?>

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label>Bus</label>
                <select name="bus_id" class="form-control" required>
                    <option value="">Select Bus</option>
                    <?php while ($bus = $buses->fetch_assoc()): ?>
                        <option value="<?= $bus['id'] ?>" <?= ($edit_mode && $edit_data['bus_id'] == $bus['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($bus['bus_number'] . ' | ' . $bus['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label>Route</label>
                <select name="route_id" class="form-control" required>
                    <option value="">Select Route</option>
                    <?php while ($route = $routes->fetch_assoc()): ?>
                        <option value="<?= $route['id'] ?>" <?= ($edit_mode && $edit_data['route_id'] == $route['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($route['source'] . ' → ' . $route['destination']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label>Departure Time</label>
                <input type="datetime-local" name="departure_time" class="form-control" required
                    value="<?= $edit_mode ? date('Y-m-d\TH:i', strtotime($edit_data['departure_time'])) : '' ?>">
            </div>

            <div class="col-md-3">
                <label>Arrival Time</label>
                <input type="datetime-local" name="arrival_time" class="form-control" required
                    value="<?= $edit_mode ? date('Y-m-d\TH:i', strtotime($edit_data['arrival_time'])) : '' ?>">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label>Price (₹)</label>
                <input type="number" step="0.01" name="price" class="form-control" required
                    value="<?= $edit_mode ? $edit_data['price'] : '' ?>">
            </div>

            <div class="col-md-3 align-self-end">
                <button name="<?= $edit_mode ? 'update' : 'add' ?>" class="btn btn-<?= $edit_mode ? 'primary' : 'success' ?> w-100">
                    <?= $edit_mode ? '💾 Update' : '➕ Add Schedule' ?>
                </button>
            </div>
        </div>
    </form>

    <div class="bg-white p-4 shadow rounded">
        <h5>All Schedules</h5>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Bus</th>
                <th>Route</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Price (₹)</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $schedules->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['bus_number'] . ' | ' . $row['bus_name']) ?></td>
                    <td><?= htmlspecialchars($row['source'] . ' → ' . $row['destination']) ?></td>
                    <td><?= date('d M Y, H:i', strtotime($row['departure_time'])) ?></td>
                    <td><?= date('d M Y, H:i', strtotime($row['arrival_time'])) ?></td>
                    <td>₹<?= number_format($row['price'], 2) ?></td>
                    <td>
                        <a href="manage_schedules.php?edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary">✏️ Edit</a>
                        <a href="manage_schedules.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this schedule?')">🗑️ Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <a href="admin_dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>

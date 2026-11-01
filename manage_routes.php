<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['login_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

$edit_mode = false;
$edit_data = [];

// Add Route
if (isset($_POST['add'])) {
    $duration = ($_POST['hours'] * 60) + $_POST['minutes'];
    $stmt = $conn->prepare("INSERT INTO routes (source, destination, distance_km, duration_minutes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $_POST['source'], $_POST['destination'], $_POST['distance'], $duration);
    $stmt->execute();
    header("Location: manage_routes.php");
    exit;
}

// Delete Route
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM routes WHERE id = " . $_GET['delete']);
    header("Location: manage_routes.php");
    exit;
}

// Load for Edit
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $stmt = $conn->prepare("SELECT * FROM routes WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
}

// Update Route
if (isset($_POST['update'])) {
    $duration = ($_POST['hours'] * 60) + $_POST['minutes'];
    $stmt = $conn->prepare("UPDATE routes SET source = ?, destination = ?, distance_km = ?, duration_minutes = ? WHERE id = ?");
    $stmt->bind_param("ssiii", $_POST['source'], $_POST['destination'], $_POST['distance'], $duration, $_POST['id']);
    $stmt->execute();
    header("Location: manage_routes.php");
    exit;
}

$routes = $conn->query("SELECT * FROM routes ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Routes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>🗺️ Manage Routes</h2>

    <form method="POST" class="bg-white p-4 shadow mb-4 rounded">
        <h5><?= $edit_mode ? "✏️ Edit Route" : "➕ Add New Route" ?></h5>
        <?php if ($edit_mode): ?>
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
        <?php endif; ?>
        <div class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" name="source" class="form-control" placeholder="Source" value="<?= $edit_data['source'] ?? '' ?>" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="destination" class="form-control" placeholder="Destination" value="<?= $edit_data['destination'] ?? '' ?>" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="distance" class="form-control" placeholder="Distance (KM)" value="<?= $edit_data['distance_km'] ?? '' ?>" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="hours" class="form-control" placeholder="Hours" value="<?= isset($edit_data['duration_minutes']) ? floor($edit_data['duration_minutes'] / 60) : '' ?>" required>
            </div>
            <div class="col-md-1">
                <input type="number" name="minutes" class="form-control" placeholder="Min" value="<?= isset($edit_data['duration_minutes']) ? ($edit_data['duration_minutes'] % 60) : '' ?>" required>
            </div>
            <div class="col-md-1">
                <button name="<?= $edit_mode ? 'update' : 'add' ?>" class="btn btn-<?= $edit_mode ? 'primary' : 'success' ?> w-100"><?= $edit_mode ? '💾' : '➕' ?></button>
            </div>
        </div>
    </form>

    <div class="bg-white p-4 shadow rounded">
        <h5>All Routes</h5>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Source</th>
                <th>Destination</th>
                <th>Distance (KM)</th>
                <th>Duration</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $routes->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['source']) ?></td>
                    <td><?= htmlspecialchars($row['destination']) ?></td>
                    <td><?= $row['distance_km'] ?></td>
                    <td><?= floor($row['duration_minutes'] / 60) . "h " . ($row['duration_minutes'] % 60) . "m" ?></td>
                    <td>
                        <a href="manage_routes.php?edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary">✏️ Edit</a>
                        <a href="manage_routes.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this route?')">🗑️ Delete</a>
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

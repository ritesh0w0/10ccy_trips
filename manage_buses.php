<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['login_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

$msg = '';
$edit_mode = false;
$edit_data = [];

// ADD bus
if (isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO buses (bus_number, name, type, seats) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $_POST['bus_number'], $_POST['name'], $_POST['type'], $_POST['seats']);
    $stmt->execute();
    header("Location: manage_buses.php");
    exit;
}

// DELETE bus
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM buses WHERE id = " . $_GET['delete']);
    header("Location: manage_buses.php");
    exit;
}

// LOAD bus for edit
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $stmt = $conn->prepare("SELECT * FROM buses WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_data = $result->fetch_assoc();
}

// UPDATE bus
if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE buses SET bus_number = ?, name = ?, type = ?, seats = ? WHERE id = ?");
    $stmt->bind_param("sssii", $_POST['bus_number'], $_POST['name'], $_POST['type'], $_POST['seats'], $_POST['id']);
    $stmt->execute();
    header("Location: manage_buses.php");
    exit;
}

// Get all buses
$buses = $conn->query("SELECT * FROM buses ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Buses | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">🚌 Manage Buses</h2>

    <!-- Add/Edit Bus Form -->
    <form method="POST" class="mb-4 p-4 bg-white shadow rounded">
        <h5><?= $edit_mode ? "✏️ Edit Bus" : "➕ Add New Bus" ?></h5>
        <?php if ($edit_mode): ?>
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
        <?php endif; ?>
        <div class="row mb-2">
            <div class="col-md-3">
                <input type="text" name="bus_number" class="form-control" placeholder="Bus Number" value="<?= $edit_data['bus_number'] ?? '' ?>" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="Bus Name" value="<?= $edit_data['name'] ?? '' ?>" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="type" class="form-control" placeholder="Type (AC/Non-AC)" value="<?= $edit_data['type'] ?? '' ?>" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="seats" class="form-control" placeholder="Seats" value="<?= $edit_data['seats'] ?? '' ?>" required>
            </div>
            <div class="col-md-1">
                <button name="<?= $edit_mode ? 'update' : 'add' ?>" class="btn btn-<?= $edit_mode ? 'primary' : 'success' ?> w-100">
                    <?= $edit_mode ? '💾' : '➕' ?>
                </button>
            </div>
        </div>
    </form>

    <!-- List of Buses -->
    <div class="bg-white p-4 shadow rounded">
        <h5>All Buses</h5>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Bus Number</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Seats</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $buses->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['bus_number']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['type']) ?></td>
                        <td><?= $row['seats'] ?></td>
                        <td>
                            <a href="manage_buses.php?edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary">✏️ Edit</a>
                            <a href="manage_buses.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this bus?')">🗑️ Delete</a>
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

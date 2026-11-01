<?php
session_start();
include 'includes/db.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND user_type = 'admin'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['login_id'] = $row['id'];
            $_SESSION['user_type'] = $row['user_type'];
            $_SESSION['username'] = $row['username'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $msg = "Incorrect password.";
        }
    } else {
        $msg = "Admin not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login | 10ccy Trips</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --secondary: #4b5563;
            --light: #f9fafb;
            --dark: #111827;
        }

        body {
            background-color: #f3f4f6;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .auth-card {
            width: 100%;
            max-width: 450px;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .auth-header {
            background: linear-gradient(135deg, #111827, #1f2937);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .auth-body {
            background: white;
            padding: 2rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.1);
        }

        .btn-auth {
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-dark {
            background-color: #111827;
            border-color: #111827;
        }

        .btn-dark:hover {
            background-color: #1f2937;
            transform: translateY(-1px);
        }

        .auth-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <i class="bi bi-shield-lock auth-icon"></i>
            <h2>Admin Portal</h2>
            <p>Access the administration dashboard</p>
        </div>
        <div class="auth-body">
            <?php if ($msg): ?>
                <div class="alert alert-danger"><?= $msg ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button class="btn btn-dark btn-auth w-100">Login as Admin</button>
            </form>
        </div>
    </div>
</body>
</html>
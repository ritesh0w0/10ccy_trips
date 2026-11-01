<?php
session_start();
include 'includes/db.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_or_username = $_POST['email_or_username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email_or_username' OR username = '$email_or_username'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            header('Location: dashboard.php');
            exit();
        } else {
            $msg = "Incorrect password.";
        }
    } else {
        $msg = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | 10ccy Trips</title>
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
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
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

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--secondary);
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-footer a:hover {
            text-decoration: underline;
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
            <i class="bi bi-person-circle auth-icon"></i>
            <h2>Welcome Back</h2>
            <p>Login to your 10ccy Trips account</p>
        </div>
        <div class="auth-body">
            <?php if ($msg): ?>
                <div class="alert alert-danger"><?= $msg ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <input type="text" name="email_or_username" class="form-control" placeholder="Email or Username" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-auth w-100">Login</button>
            </form>
            <div class="auth-footer">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>
    </div>
</body>
</html>
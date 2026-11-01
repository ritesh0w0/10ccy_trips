<?php
include 'includes/db.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (full_name, email, username, password) 
            VALUES ('$full_name', '$email', '$username', '$password')";

    if ($conn->query($sql) === TRUE) {
        $msg = "Registration successful. <a href='login.php'>Login Now</a>";
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register | 10ccy Trips</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --secondary: #4b5563;
            --light: #f9fafb;
            --dark: #111827;
            --success: #10b981;
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
            background: linear-gradient(135deg, var(--success), #34d399);
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
            border-color: var(--success);
            box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.1);
        }

        .btn-auth {
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-success {
            background-color: var(--success);
            border-color: var(--success);
        }

        .btn-success:hover {
            background-color: #059669;
            transform: translateY(-1px);
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--secondary);
        }

        .auth-footer a {
            color: var(--success);
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
            <i class="bi bi-person-plus auth-icon"></i>
            <h2>Create Account</h2>
            <p>Join 10ccy Trips today</p>
        </div>
        <div class="auth-body">
            <?php if ($msg): ?>
                <div class="alert alert-success"><?= $msg ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-success btn-auth w-100">Register</button>
            </form>
            <div class="auth-footer">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
</body>
</html>
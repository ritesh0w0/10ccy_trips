<!-- login-choice.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Choose Login | 10ccy Trips</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .choice-box {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .btn-choice {
            margin: 10px;
            padding: 12px 30px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="choice-box">
        <h2 class="mb-4">👋 Welcome! Choose how you want to log in</h2>
        <a href="login.php" class="btn btn-primary btn-choice">Login as User</a>
        <a href="admin_login.php" class="btn btn-dark btn-choice">Login as Admin</a>
        <p class="mt-3">New user? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>

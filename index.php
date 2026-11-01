<!DOCTYPE html>
<html>
<head>
    <title>Welcome | 10ccy Trips</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: linear-gradient(to right, #ffffff, #8ECAE6); /* Light white to blue */
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .welcome-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .logo {
            width: 150px;
            margin-bottom: 20px;
        }

        h1 {
            color: #062c3a; /* Teal Black for premium header */
            font-weight: bold;
        }

        p {
            font-size: 1.2rem;
            color: #2F3E46; /* Slate gray for readable body text */
        }

        .btn-start {
            background-color: #FFB703; /* Primary button color */
            color: #062c3a;
            font-weight: bold;
            padding: 12px 30px;
            border: none;
            border-radius: 30px;
            font-size: 1.1rem;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .btn-start:hover {
            background-color: #FDAF17; /* Button hover color */
            color: white;
        }
    </style>
</head>
<body>
    <div class="welcome-card">
        <img src="assets/logo.png" alt="10ccy Trips Logo" class="logo"> <!-- Replace with your logo path -->
        <h1>Welcome to 10ccy Trips</h1>
        <p>Let’s make your journey amazing with <strong>10ccy Trips</strong>!</p>
        <a href="login-choice.php" class="btn btn-start">Click to Continue 🚀</a>
    </div>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['login_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - 10ccy Trips</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .admin-card {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .admin-card:hover {
            transform: translateY(-5px);
        }
        
        .admin-header {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--dark) 100%);
            color: white;
            padding: 25px;
            text-align: center;
            border-bottom: 4px solid var(--primary);
        }
        
        .admin-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 1.8rem;
        }
        
        .admin-body {
            padding: 30px;
            background-color: white;
        }
        
        .admin-btn {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 15px 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .admin-btn i {
            margin-right: 12px;
            font-size: 1.2rem;
        }
        
        .admin-btn:hover {
            transform: translateX(5px);
            color: white;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        
        .btn-buses {
            background-color: #3498db;
            border-left: 5px solid #2980b9;
        }
        
        .btn-routes {
            background-color: #2ecc71;
            border-left: 5px solid #27ae60;
        }
        
        .btn-schedules {
            background-color: #9b59b6;
            border-left: 5px solid #8e44ad;
        }
        
        .btn-bookings {
            background-color: #e67e22;
            border-left: 5px solid #d35400;
        }
        
        .btn-logout {
            background-color: var(--danger);
            border-left: 5px solid #c0392b;
        }
        
        .welcome-text {
            text-align: center;
            margin-bottom: 25px;
            color: var(--dark);
            font-size: 1.1rem;
        }
        
        @media (max-width: 768px) {
            .admin-body {
                padding: 20px;
            }
            
            .admin-btn {
                padding: 12px 15px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-card">
            <div class="admin-header">
                <h2><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h2>
            </div>
            <div class="admin-body">
                <p class="welcome-text">Welcome back, Administrator! What would you like to manage today?</p>
                
                <a href="manage_buses.php" class="admin-btn btn-buses">
                    <i class="fas fa-bus"></i> Manage Buses
                </a>
                
                <a href="manage_routes.php" class="admin-btn btn-routes">
                    <i class="fas fa-route"></i> Manage Routes
                </a>
                
                <a href="manage_schedules.php" class="admin-btn btn-schedules">
                    <i class="fas fa-calendar-alt"></i> Manage Schedules
                </a>
                
                <a href="manage_bookings.php" class="admin-btn btn-bookings">
                    <i class="fas fa-ticket-alt"></i> Manage Bookings
                </a>
                
                <a href="logout.php" class="admin-btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
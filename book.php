<?php
session_start();
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit(); }
include 'includes/db.php';
$user_id = $_SESSION['user']['id'];

// Initialize filter variables
$source = $_GET['source'] ?? '';
$destination = $_GET['destination'] ?? '';
$travel_date = $_GET['travel_date'] ?? '';

// Build the SQL query with filters
$sql = "
  SELECT s.id AS schedule_id, s.price, s.departure_time, s.arrival_time,
         b.id AS bus_id, b.bus_number, b.name AS bus_name, b.seats,
         r.source, r.destination
  FROM schedules s
  JOIN buses b ON s.bus_id = b.id
  JOIN routes r ON s.route_id = r.id
  WHERE s.departure_time > NOW()  -- Only show future schedules
";

// Add filters if they exist
if (!empty($source)) {
    $sql .= " AND r.source LIKE '%" . $conn->real_escape_string($source) . "%'";
}
if (!empty($destination)) {
    $sql .= " AND r.destination LIKE '%" . $conn->real_escape_string($destination) . "%'";
}
if (!empty($travel_date)) {
    $sql .= " AND DATE(s.departure_time) = '" . $conn->real_escape_string($travel_date) . "'";
}

$sql .= " ORDER BY s.departure_time ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Book a Bus | 10ccy Trips</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .search-box {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      margin-bottom: 30px;
    }
    .bus-card {
      transition: all 0.3s ease;
      border-left: 4px solid #0d6efd;
      margin-bottom: 20px;
    }
    .bus-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .route-arrow {
      color: #0d6efd;
      font-weight: bold;
      font-size: 1.1rem;
    }
    .price-tag {
      font-size: 1.3rem;
      color: #198754;
      font-weight: bold;
    }
    .time-badge {
      background: #f8f9fa;
      padding: 5px 10px;
      border-radius: 5px;
      font-weight: 500;
    }
    .seats-available {
      color: #6c757d;
      font-size: 0.9rem;
    }
    .no-results {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 40px;
      text-align: center;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container py-4">
    <!-- Add this right after the opening <div class="container py-4"> -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="dashboard.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
    </a>
    <?php if (isset($_SESSION['booking_error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show">
        <?= $_SESSION['booking_error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['booking_error']); ?>
    <?php endif; ?>
</div>
    <?php if (isset($_SESSION['booking_error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show">
        <?= $_SESSION['booking_error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['booking_error']); ?>
    <?php endif; ?>
    
    <div class="search-box">
      <h2 class="mb-4"><i class="fas fa-search me-2"></i>Find Your Bus</h2>
      <form method="GET" action="book.php">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label for="source" class="form-label">From</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
              <input type="text" class="form-control" id="source" name="source" 
                     placeholder="City or station" value="<?= htmlspecialchars($source) ?>">
            </div>
          </div>
          <div class="col-md-4">
            <label for="destination" class="form-label">To</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
              <input type="text" class="form-control" id="destination" name="destination" 
                     placeholder="City or station" value="<?= htmlspecialchars($destination) ?>">
            </div>
          </div>
          <div class="col-md-3">
            <label for="travel_date" class="form-label">Travel Date</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
              <input type="date" class="form-control" id="travel_date" name="travel_date" 
                     min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($travel_date) ?>">
            </div>
          </div>
          <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </form>
    </div>

    <h2 class="mb-4"><i class="fas fa-bus me-2"></i>Available Buses</h2>
    
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card p-4 bus-card">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h4 class="mb-2"><?= htmlspecialchars($row['bus_number']) ?> - <?= htmlspecialchars($row['bus_name']) ?></h4>
              <p class="mb-3">
                <span class="route-arrow"><?= htmlspecialchars($row['source']) ?> <i class="fas fa-arrow-right mx-2"></i> <?= htmlspecialchars($row['destination']) ?></span>
              </p>
              <div class="d-flex flex-wrap gap-3 mb-2">
                <span class="time-badge">
                  <i class="far fa-clock me-1"></i> <?= date('h:i A', strtotime($row['departure_time'])) ?>
                </span>
                <span class="time-badge">
                  <i class="far fa-clock me-1"></i> <?= date('h:i A', strtotime($row['arrival_time'])) ?>
                </span>
                <span class="time-badge">
                  <i class="far fa-calendar-alt me-1"></i> <?= date('D, M j', strtotime($row['departure_time'])) ?>
                </span>
              </div>
              <p class="mb-0 seats-available">
                <i class="fas fa-chair me-1"></i> <?= $row['seats'] ?> seats available
              </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
              <div class="price-tag mb-3">₹<?= number_format($row['price'], 2) ?></div>
              <button class="btn btn-success open-book-modal"
                      data-schedule="<?= $row['schedule_id'] ?>">
                <i class="fas fa-ticket-alt me-1"></i> Book Now
              </button>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="no-results">
        <i class="fas fa-bus-slash fa-3x mb-3 text-muted"></i>
        <h4 class="mb-2">No buses found</h4>
        <p class="text-muted mb-4">
          <?php if (!empty($source) || !empty($destination) || !empty($travel_date)): ?>
            No upcoming buses match your search criteria.
          <?php else: ?>
            Currently no upcoming buses available.
          <?php endif; ?>
        </p>
        <?php if (!empty($source) || !empty($destination) || !empty($travel_date)): ?>
          <a href="book.php" class="btn btn-outline-primary">
            <i class="fas fa-times me-1"></i> Clear filters
          </a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Seat Selection Modal -->
  <div class="modal fade" id="seatModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <form id="seatForm" method="POST" action="book_seats.php">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title"><i class="fas fa-chair me-2"></i>Select Seats</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div id="modalContent">
              <div class="d-flex justify-content-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-check me-1"></i> Confirm Booking
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.querySelectorAll('.open-book-modal').forEach(btn => {
      btn.addEventListener('click', () => {
        const schedule_id = btn.dataset.schedule;
        const modalContent = document.getElementById('modalContent');
        
        // Show loading spinner
        modalContent.innerHTML = `
          <div class="d-flex justify-content-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        `;
        
        const seatModal = new bootstrap.Modal('#seatModal');
        seatModal.show();
        
        fetch(`load_seats.php?schedule_id=${schedule_id}`)
          .then(res => res.text())
          .then(html => {
            modalContent.innerHTML = html;
            
            // Execute any scripts in the loaded content
            const scripts = modalContent.getElementsByTagName('script');
            for (let script of scripts) {
              const newScript = document.createElement('script');
              newScript.text = script.text;
              document.body.appendChild(newScript).parentNode.removeChild(newScript);
            }
          });
      });
    });

    // Set minimum date to today and default to today if empty
    const dateInput = document.getElementById('travel_date');
    dateInput.min = new Date().toISOString().split('T')[0];
    if (!dateInput.value) {
      dateInput.value = new Date().toISOString().split('T')[0];
    }
  </script>
</body>
</html>
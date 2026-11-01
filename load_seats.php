<?php
include 'includes/db.php';
$schedule_id = $_GET['schedule_id'] ?? 0;

if (!$schedule_id) {
    echo "<div class='alert alert-danger'>Invalid Schedule ID</div>";
    exit;
}

// Get bus details with seat count
$bus_query = $conn->prepare("
    SELECT b.id AS bus_id, b.seats, b.bus_number, b.name AS bus_name,
           r.source, r.destination, s.departure_time, s.price
    FROM schedules s
    JOIN buses b ON s.bus_id = b.id
    JOIN routes r ON s.route_id = r.id
    WHERE s.id = ?
");
$bus_query->bind_param("i", $schedule_id);
$bus_query->execute();
$bus_data = $bus_query->get_result()->fetch_assoc();
$bus_query->close();

if (!$bus_data) {
    echo "<div class='alert alert-danger'>Bus data not found</div>";
    exit;
}

$total_seats = $bus_data['seats'];

// Fetch booked seats
$booked = [];
$booked_query = $conn->prepare("SELECT seat_no FROM bookings WHERE schedule_id = ? AND status = 'booked'");
$booked_query->bind_param("i", $schedule_id);
$booked_query->execute();
$booked_result = $booked_query->get_result();
while ($row = $booked_result->fetch_assoc()) {
    $booked[] = $row['seat_no'];
}
$booked_query->close();

$available_seats = $total_seats - count($booked);
?>

<style>
/* CSS for seat selection - Add this at the top of the file */
.seat-grid { 
    display: grid; 
    grid-template-columns: repeat(4, 1fr); 
    gap: 10px; 
    margin-bottom: 20px;
}
.seat {
    width: 100%; 
    aspect-ratio: 1/1;
    background: #e9ecef; 
    border-radius: 8px; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    cursor: pointer; /* This makes the cursor change to pointer */
    transition: all 0.2s;
    font-weight: bold;
}
.seat:hover { 
    transform: scale(1.05); 
    box-shadow: 0 0 5px rgba(0,0,0,0.2);
}
.seat.booked { 
    background: #dc3545; 
    color: white; 
    cursor: not-allowed;
}
.seat.selected { 
    background: #198754; 
    color: white;
}
.seat-legend { 
    display: flex; 
    justify-content: center; 
    gap: 20px; 
    margin: 15px 0; 
}
.legend-item { 
    display: flex; 
    align-items: center; 
    gap: 5px; 
}
</style>

<div class="alert alert-info">
    <h4><?= htmlspecialchars($bus_data['bus_number']) ?> - <?= htmlspecialchars($bus_data['bus_name']) ?></h4>
    <p><?= htmlspecialchars($bus_data['source']) ?> to <?= htmlspecialchars($bus_data['destination']) ?> | 
       <?= date('M d, Y h:i A', strtotime($bus_data['departure_time'])) ?> |
       ₹<?= number_format($bus_data['price'], 2) ?></p>
    <p class="mb-0"><strong><?= $available_seats ?> seats available</strong> out of <?= $total_seats ?></p>
</div>

<div class="seat-legend">
    <div class="legend-item"><div style="width:20px;height:20px;background:#e9ecef;border-radius:4px"></div> Available</div>
    <div class="legend-item"><div style="width:20px;height:20px;background:#198754;border-radius:4px"></div> Selected</div>
    <div class="legend-item"><div style="width:20px;height:20px;background:#dc3545;border-radius:4px"></div> Booked</div>
</div>

<div class="seat-grid" id="seatsContainer">
<?php for ($i = 1; $i <= $total_seats; $i++): ?>
    <div class="seat <?= in_array($i, $booked) ? 'booked' : '' ?>" 
         data-seat="<?= $i ?>">
        <?= $i ?>
    </div>
<?php endfor; ?>
</div>

<form method="POST" action="book_seats.php" id="bookingForm">
    <input type="hidden" name="schedule_id" value="<?= $schedule_id ?>">
    <input type="hidden" name="seats" id="selectedSeats">
    
    <div id="passengerSection" class="mt-4 d-none">
        <h5>Passenger Details</h5>
        <div id="passengerInputs" class="row g-3"></div>
        
        <div class="mt-3 d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary" onclick="clearSelection()">
                Clear Selection
            </button>
            <button type="submit" class="btn btn-primary">
                Confirm Booking for <span id="seatCount">0</span> Seat(s)
            </button>
        </div>
    </div>
</form>

<script>
// JavaScript for seat selection
window.selectedSeats = [];

// Event delegation for seat clicks
document.getElementById('seatsContainer').addEventListener('click', function(e) {
    const seatEl = e.target.closest('.seat');
    if (!seatEl || seatEl.classList.contains('booked')) return;
    
    const seatNo = parseInt(seatEl.dataset.seat);
    
    if (selectedSeats.includes(seatNo)) {
        // Deselect
        selectedSeats = selectedSeats.filter(s => s !== seatNo);
        seatEl.classList.remove('selected');
    } else {
        // Select
        selectedSeats.push(seatNo);
        seatEl.classList.add('selected');
    }
    
    updatePassengerForm();
});

function updatePassengerForm() {
    const seatInput = document.getElementById('selectedSeats');
    const passengerSection = document.getElementById('passengerSection');
    const passengerInputs = document.getElementById('passengerInputs');
    const seatCount = document.getElementById('seatCount');
    
    seatInput.value = JSON.stringify(selectedSeats);
    seatCount.textContent = selectedSeats.length;
    
    if (selectedSeats.length > 0) {
        passengerSection.classList.remove('d-none');
        passengerInputs.innerHTML = '';
        
        selectedSeats.sort((a, b) => a - b).forEach(seat => {
            passengerInputs.innerHTML += `
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Passenger for Seat ${seat}</label>
                        <input type="text" class="form-control" 
                               name="passenger_names[]" 
                               placeholder="Full Name" required>
                    </div>
                </div>
            `;
        });
    } else {
        passengerSection.classList.add('d-none');
    }
}

function clearSelection() {
    document.querySelectorAll('.seat.selected').forEach(seat => {
        seat.classList.remove('selected');
    });
    selectedSeats = [];
    updatePassengerForm();
}

// Initialize form
updatePassengerForm();
</script>
<?php
require_once '../includes/header.php';
requireRole(['customer']);

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Proses booking
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $studio_id = mysqli_real_escape_string($conn, $_POST['studio_id']);
    $booking_date = mysqli_real_escape_string($conn, $_POST['booking_date']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);
    
    // Validasi tanggal
    if (strtotime($booking_date) < strtotime(date('Y-m-d'))) {
        $error = "Tanggal booking tidak boleh di masa lalu.";
    } else {
        // Hitung durasi dan total harga
        $start = strtotime($start_time);
        $end = strtotime($end_time);
        $total_hours = ($end - $start) / 3600;
        
        if ($total_hours <= 0) {
            $error = "Waktu akhir harus setelah waktu mulai.";
        } else {
            // Ambil harga studio
            $sql = "SELECT price_per_hour FROM studios WHERE id = '$studio_id'";
            $result = mysqli_query($conn, $sql);
            
            if ($result && mysqli_num_rows($result) > 0) {
                $studio = mysqli_fetch_assoc($result);
                $total_price = $studio['price_per_hour'] * $total_hours;
                
                // Cek apakah studio available pada waktu tersebut
                $sql = "SELECT id FROM bookings 
                        WHERE studio_id = '$studio_id' 
                        AND booking_date = '$booking_date'
                        AND (
                            (start_time <= '$start_time' AND end_time > '$start_time') OR
                            (start_time < '$end_time' AND end_time >= '$end_time') OR
                            (start_time >= '$start_time' AND end_time <= '$end_time')
                        )
                        AND status != 'cancelled'";
                
                $result = mysqli_query($conn, $sql);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    $error = "Studio tidak tersedia pada waktu yang dipilih.";
                } else {
                    // Insert booking
                    $sql = "INSERT INTO bookings (user_id, studio_id, booking_date, start_time, end_time, total_hours, total_price) 
                            VALUES ('$user_id', '$studio_id', '$booking_date', '$start_time', '$end_time', '$total_hours', '$total_price')";
                    
                    if (mysqli_query($conn, $sql)) {
                        $success = "Booking berhasil dibuat. Silakan lakukan pembayaran.";
                    } else {
                        $error = "Error: " . mysqli_error($conn);
                    }
                }
            } else {
                $error = "Studio tidak ditemukan.";
            }
        }
    }
}

// Ambil data studio
$studio_id = isset($_GET['studio_id']) ? mysqli_real_escape_string($conn, $_GET['studio_id']) : 0;
$studio = null;

if ($studio_id) {
    $sql = "SELECT * FROM studios WHERE id = '$studio_id' AND status = 'available'";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $studio = mysqli_fetch_assoc($result);
    }
}

// Ambil semua studio available
$sql = "SELECT * FROM studios WHERE status = 'available' ORDER BY name";
$studios = mysqli_query($conn, $sql);
?>

<div class="booking-container">
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="booking-form">
        <form method="POST">
            <div class="form-group">
                <label for="studio_id">Pilih Studio</label>
                <select id="studio_id" name="studio_id" required onchange="updateStudioPrice()">
                    <option value="">-- Pilih Studio --</option>
                    <?php 
                    if ($studios && mysqli_num_rows($studios) > 0) {
                        while($s = mysqli_fetch_assoc($studios)): 
                    ?>
                    <option value="<?php echo $s['id']; ?>" data-price="<?php echo $s['price_per_hour']; ?>" <?php echo ($studio_id == $s['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($s['name']); ?> - <?php echo formatRupiah($s['price_per_hour']); ?>/jam
                    </option>
                    <?php 
                        endwhile;
                    } else {
                        echo '<option value="">Tidak ada studio tersedia</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="booking_date">Tanggal Booking</label>
                <input type="date" id="booking_date" name="booking_date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="start_time">Waktu Mulai</label>
                <input type="time" id="start_time" name="start_time" min="08:00" max="22:00" required onchange="calculateTotal()">
            </div>
            
            <div class="form-group">
                <label for="end_time">Waktu Selesai</label>
                <input type="time" id="end_time" name="end_time" min="08:00" max="22:00" required onchange="calculateTotal()">
            </div>
            
            <div class="form-group">
                <label>Durasi: <span id="duration">0</span> jam</label>
            </div>
            
            <div class="form-group">
                <label>Total Harga: <span id="total_price">Rp 0</span></label>
            </div>
            
            <button type="submit" class="btn btn-primary">Booking Sekarang</button>
        </form>
    </div>
</div>

<script>
function updateStudioPrice() {
    calculateTotal();
}

function calculateTotal() {
    const studioSelect = document.getElementById('studio_id');
    const startTime = document.getElementById('start_time');
    const endTime = document.getElementById('end_time');
    const durationSpan = document.getElementById('duration');
    const totalPriceSpan = document.getElementById('total_price');
    
    if (studioSelect.value && startTime.value && endTime.value) {
        const pricePerHour = parseFloat(studioSelect.options[studioSelect.selectedIndex].getAttribute('data-price'));
        
        // Calculate duration in hours
        const start = new Date('2000-01-01T' + startTime.value);
        const end = new Date('2000-01-01T' + endTime.value);
        const duration = (end - start) / (1000 * 60 * 60);
        
        if (duration > 0) {
            durationSpan.textContent = duration.toFixed(1);
            totalPriceSpan.textContent = 'Rp ' + (pricePerHour * duration).toLocaleString('id-ID');
        } else {
            durationSpan.textContent = '0';
            totalPriceSpan.textContent = 'Rp 0';
        }
    }
}

// Set minimum time to current time if today is selected
document.getElementById('booking_date').addEventListener('change', function() {
    const today = new Date().toISOString().split('T')[0];
    const selectedDate = this.value;
    
    if (selectedDate === today) {
        const now = new Date();
        const currentHour = now.getHours().toString().padStart(2, '0');
        const currentMinute = now.getMinutes().toString().padStart(2, '0');
        document.getElementById('start_time').min = currentHour + ':' + currentMinute;
    } else {
        document.getElementById('start_time').min = '08:00';
    }
});
</script>

<?php include '../includes/footer.php'; ?>
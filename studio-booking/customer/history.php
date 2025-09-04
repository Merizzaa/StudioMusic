<?php
require_once '../includes/header.php';
requireRole(['customer']);

$user_id = $_SESSION['user_id'];

// Batalkan booking
if (isset($_GET['cancel'])) {
    $booking_id = mysqli_real_escape_string($conn, $_GET['cancel']);
    
    // Hanya bisa membatalkan jika status masih pending
    $sql = "UPDATE bookings SET status='cancelled' WHERE id='$booking_id' AND user_id='$user_id' AND status='pending'";
    
    if (mysqli_query($conn, $sql)) {
        $success = "Booking berhasil dibatalkan.";
    } else {
        $error = "Tidak dapat membatalkan booking.";
    }
}

// Upload bukti pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['payment_proof'])) {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    
    // Validasi file
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    $file_type = $_FILES['payment_proof']['type'];
    
    if (!in_array($file_type, $allowed_types)) {
        $error = "Hanya file JPG, JPEG, dan PNG yang diizinkan.";
    } else {
        // Upload file
        $file_name = 'payment_' . time() . '_' . $booking_id . '.' . pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
        $target_path = '../uploads/payment_proofs/' . $file_name;
        
        if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target_path)) {
            // Update database
            $sql = "UPDATE bookings SET payment_proof='$file_name' WHERE id='$booking_id' AND user_id='$user_id'";
            
            if (mysqli_query($conn, $sql)) {
                $success = "Bukti pembayaran berhasil diupload.";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        } else {
            $error = "Gagal mengupload file.";
        }
    }
}

// Ambil data booking history
$sql = "SELECT b.*, s.name as studio_name 
        FROM bookings b 
        JOIN studios s ON b.studio_id = s.id 
        WHERE b.user_id = '$user_id'
        ORDER BY b.created_at DESC";
$bookings = mysqli_query($conn, $sql);
?>

<div class="booking-history">
    <h2>Riwayat Booking</h2>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (mysqli_num_rows($bookings) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Studio</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Durasi</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Status Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($booking = mysqli_fetch_assoc($bookings)): ?>
            <tr>
                <td><?php echo $booking['studio_name']; ?></td>
                <td><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></td>
                <td><?php echo date('H:i', strtotime($booking['start_time'])) . ' - ' . date('H:i', strtotime($booking['end_time'])); ?></td>
                <td><?php echo $booking['total_hours']; ?> jam</td>
                <td><?php echo formatRupiah($booking['total_price']); ?></td>
                <td><?php echo getStatusBadge($booking['status']); ?></td>
                <td><?php echo getStatusBadge($booking['payment_status']); ?></td>
                <td>
                    <?php if ($booking['status'] == 'pending'): ?>
                        <a href="?cancel=<?php echo $booking['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin membatalkan booking?')">Batalkan</a>
                    <?php endif; ?>
                    
                    <?php if ($booking['status'] == 'confirmed' && $booking['payment_status'] == 'pending' && !$booking['payment_proof']): ?>
                        <a href="#" class="btn btn-sm btn-primary" onclick="showPaymentModal(<?php echo $booking['id']; ?>)">Upload Bukti</a>
                    <?php elseif ($booking['payment_proof']): ?>
                        <a href="../uploads/payment_proofs/<?php echo $booking['payment_proof']; ?>" target="_blank" class="btn btn-sm btn-secondary">Lihat Bukti</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>Anda belum memiliki riwayat booking.</p>
    <?php endif; ?>
</div>

<!-- Modal Upload Bukti Pembayaran -->
<div id="paymentModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Upload Bukti Pembayaran</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" id="booking_id" name="booking_id">
            <div class="form-group">
                <label for="payment_proof">File Bukti Pembayaran (JPG/PNG)</label>
                <input type="file" id="payment_proof" name="payment_proof" accept="image/jpeg,image/png" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</div>

<script>
function showPaymentModal(bookingId) {
    document.getElementById('booking_id').value = bookingId;
    document.getElementById('paymentModal').style.display = 'block';
}

// Tutup modal
document.querySelector('#paymentModal .close').addEventListener('click', function() {
    document.getElementById('paymentModal').style.display = 'none';
});

// Tutup modal jika klik di luar
window.addEventListener('click', function(event) {
    if (event.target == document.getElementById('paymentModal')) {
        document.getElementById('paymentModal').style.display = 'none';
    }
});
</script>

<?php include '../includes/footer.php'; ?>
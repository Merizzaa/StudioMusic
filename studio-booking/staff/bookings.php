<?php
require_once '../includes/header.php';
requireRole(['staff']);

// Update status booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $sql = "UPDATE bookings SET status='$status' WHERE id='$booking_id'";
    
    if (mysqli_query($conn, $sql)) {
        $success = "Status booking berhasil diperbarui.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Ambil data bookings
$sql = "SELECT b.*, u.username, u.full_name, s.name as studio_name 
        FROM bookings b 
        JOIN users u ON b.user_id = u.id 
        JOIN studios s ON b.studio_id = s.id 
        ORDER BY b.created_at DESC";
$bookings = mysqli_query($conn, $sql);
?>

<div class="admin-content">
    <h2>Manajemen Booking</h2>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="admin-card">
        <h3>Daftar Booking</h3>
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
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
                    <td><?php echo $booking['full_name']; ?></td>
                    <td><?php echo $booking['studio_name']; ?></td>
                    <td><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></td>
                    <td><?php echo date('H:i', strtotime($booking['start_time'])) . ' - ' . date('H:i', strtotime($booking['end_time'])); ?></td>
                    <td><?php echo $booking['total_hours']; ?> jam</td>
                    <td><?php echo formatRupiah($booking['total_price']); ?></td>
                    <td><?php echo getStatusBadge($booking['status']); ?></td>
                    <td><?php echo getStatusBadge($booking['payment_status']); ?></td>
                    <td>
                        <a href="#" class="btn btn-sm btn-primary" onclick="updateStatus(<?php echo $booking['id']; ?>, '<?php echo $booking['status']; ?>')">Ubah Status</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Update Status -->
<div id="statusModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Ubah Status Booking</h3>
        <form method="POST">
            <input type="hidden" id="booking_id" name="booking_id">
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <button type="submit" name="update_status" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
// Fungsi untuk modal update status
function updateStatus(bookingId, currentStatus) {
    document.getElementById('booking_id').value = bookingId;
    document.getElementById('status').value = currentStatus;
    
    document.getElementById('statusModal').style.display = 'block';
}

// Tutup modal
document.querySelector('#statusModal .close').addEventListener('click', function() {
    document.getElementById('statusModal').style.display = 'none';
});

// Tutup modal jika klik di luar
window.addEventListener('click', function(event) {
    if (event.target == document.getElementById('statusModal')) {
        document.getElementById('statusModal').style.display = 'none';
    }
});
</script>

<?php include '../includes/footer.php'; ?>
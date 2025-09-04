<?php
require_once '../includes/header.php';
requireRole(['staff']);

// Update status pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_payment'])) {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);
    
    $sql = "UPDATE bookings SET payment_status='$payment_status' WHERE id='$booking_id'";
    
    if (mysqli_query($conn, $sql)) {
        $success = "Status pembayaran berhasil diperbarui.";
        
        // Jika pembayaran verified, update status booking menjadi confirmed
        if ($payment_status == 'verified') {
            $sql2 = "UPDATE bookings SET status='confirmed' WHERE id='$booking_id'";
            mysqli_query($conn, $sql2);
        }
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Ambil data bookings dengan pembayaran pending
$sql = "SELECT b.*, u.username, u.full_name, s.name as studio_name 
        FROM bookings b 
        JOIN users u ON b.user_id = u.id 
        JOIN studios s ON b.studio_id = s.id 
        WHERE b.payment_status = 'pending'
        ORDER BY b.created_at DESC";
$bookings = mysqli_query($conn, $sql);
?>

<div class="admin-content">
    <h2>Konfirmasi Pembayaran</h2>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="admin-card">
        <h3>Daftar Pembayaran Pending</h3>
        <?php if (mysqli_num_rows($bookings) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Studio</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Total Harga</th>
                    <th>Bukti Pembayaran</th>
                    <th>Status</th>
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
                    <td><?php echo formatRupiah($booking['total_price']); ?></td>
                    <td>
                        <?php if ($booking['payment_proof']): ?>
                            <a href="../uploads/payment_proofs/<?php echo $booking['payment_proof']; ?>" target="_blank" class="btn btn-sm btn-primary">Lihat Bukti</a>
                        <?php else: ?>
                            <span>Tidak ada bukti</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo getStatusBadge($booking['payment_status']); ?></td>
                    <td>
                        <a href="#" class="btn btn-sm btn-primary" onclick="updatePayment(<?php echo $booking['id']; ?>, '<?php echo $booking['payment_status']; ?>')">Verifikasi</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>Tidak ada pembayaran yang pending.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Update Payment -->
<div id="paymentModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Verifikasi Pembayaran</h3>
        <form method="POST">
            <input type="hidden" id="booking_id" name="booking_id">
            <div class="form-group">
                <label for="payment_status">Status Pembayaran</label>
                <select id="payment_status" name="payment_status" required>
                    <option value="verified">Verified</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <button type="submit" name="update_payment" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
// Fungsi untuk modal update payment
function updatePayment(bookingId, currentStatus) {
    document.getElementById('booking_id').value = bookingId;
    document.getElementById('payment_status').value = currentStatus;
    
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
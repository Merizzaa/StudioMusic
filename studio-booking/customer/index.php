<?php
require_once '../includes/header.php';
requireRole(['customer']);

// Ambil statistik untuk customer
$user_id = $_SESSION['user_id'];
$stats = [];

$sql = "SELECT COUNT(*) as total FROM bookings WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$stats['total_bookings'] = $result ? mysqli_fetch_assoc($result)['total'] : 0;

$sql = "SELECT COUNT(*) as total FROM bookings WHERE user_id = '$user_id' AND status = 'pending'";
$result = mysqli_query($conn, $sql);
$stats['pending_bookings'] = $result ? mysqli_fetch_assoc($result)['total'] : 0;

$sql = "SELECT COUNT(*) as total FROM bookings WHERE user_id = '$user_id' AND status = 'confirmed'";
$result = mysqli_query($conn, $sql);
$stats['confirmed_bookings'] = $result ? mysqli_fetch_assoc($result)['total'] : 0;

// Ambil booking terbaru
$sql = "SELECT b.*, s.name as studio_name 
        FROM bookings b 
        JOIN studios s ON b.studio_id = s.id 
        WHERE b.user_id = '$user_id'
        ORDER BY b.created_at DESC LIMIT 5";
$recent_bookings = mysqli_query($conn, $sql);
?>

<div class="dashboard">
    <h2>Dashboard Customer</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Booking</h3>
            <p><?php echo $stats['total_bookings']; ?></p>
        </div>
        <div class="stat-card">
            <h3>Booking Pending</h3>
            <p><?php echo $stats['pending_bookings']; ?></p>
        </div>
        <div class="stat-card">
            <h3>Booking Dikonfirmasi</h3>
            <p><?php echo $stats['confirmed_bookings']; ?></p>
        </div>
    </div>
    
    <div class="recent-bookings">
        <h3>Booking Terbaru</h3>
        <?php if ($recent_bookings && mysqli_num_rows($recent_bookings) > 0): ?>
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
                </tr>
            </thead>
            <tbody>
                <?php while($booking = mysqli_fetch_assoc($recent_bookings)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['studio_name']); ?></td>
                    <td><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></td>
                    <td><?php echo date('H:i', strtotime($booking['start_time'])) . ' - ' . date('H:i', strtotime($booking['end_time'])); ?></td>
                    <td><?php echo $booking['total_hours']; ?> jam</td>
                    <td><?php echo formatRupiah($booking['total_price']); ?></td>
                    <td><?php echo getStatusBadge($booking['status']); ?></td>
                    <td><?php echo getStatusBadge($booking['payment_status']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>Anda belum memiliki booking.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
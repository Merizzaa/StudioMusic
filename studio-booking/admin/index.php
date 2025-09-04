<?php
require_once '../includes/header.php';
requireRole(['admin']);

// Ambil statistik
$stats = [];
$sql = "SELECT COUNT(*) as total FROM bookings";
$result = mysqli_query($conn, $sql);
$stats['total_bookings'] = mysqli_fetch_assoc($result)['total'];

$sql = "SELECT COUNT(*) as total FROM studios";
$result = mysqli_query($conn, $sql);
$stats['total_studios'] = mysqli_fetch_assoc($result)['total'];

$sql = "SELECT COUNT(*) as total FROM users WHERE role = 'customer'";
$result = mysqli_query($conn, $sql);
$stats['total_customers'] = mysqli_fetch_assoc($result)['total'];

// Ambil booking terbaru
$sql = "SELECT b.*, u.full_name, s.name as studio_name 
        FROM bookings b 
        JOIN users u ON b.user_id = u.id 
        JOIN studios s ON b.studio_id = s.id 
        ORDER BY b.created_at DESC LIMIT 5";
$recent_bookings = mysqli_query($conn, $sql);
?>

<div class="dashboard">
    <h2>Dashboard Admin</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Booking</h3>
            <p><?php echo $stats['total_bookings']; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Studio</h3>
            <p><?php echo $stats['total_studios']; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Customer</h3>
            <p><?php echo $stats['total_customers']; ?></p>
        </div>
    </div>
    
    <div class="recent-bookings">
        <h3>Booking Terbaru</h3>
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Studio</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($booking = mysqli_fetch_assoc($recent_bookings)): ?>
                <tr>
                    <td><?php echo $booking['full_name']; ?></td>
                    <td><?php echo $booking['studio_name']; ?></td>
                    <td><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></td>
                    <td><?php echo date('H:i', strtotime($booking['start_time'])) . ' - ' . date('H:i', strtotime($booking['end_time'])); ?></td>
                    <td><span class="status-badge status-<?php echo $booking['status']; ?>"><?php echo $booking['status']; ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
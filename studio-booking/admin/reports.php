<?php
require_once '../includes/header.php';
requireRole(['admin']);

// Filter laporan berdasarkan tanggal
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Query untuk statistik
$sql_stats = "SELECT 
    COUNT(*) as total_bookings,
    SUM(total_price) as total_revenue,
    AVG(total_price) as avg_booking_value
    FROM bookings 
    WHERE status = 'completed' 
    AND booking_date BETWEEN '$start_date' AND '$end_date'";
$stats = mysqli_fetch_assoc(mysqli_query($conn, $sql_stats));

// Query untuk booking per bulan
$sql_monthly = "SELECT 
    DATE_FORMAT(booking_date, '%Y-%m') as month,
    COUNT(*) as booking_count,
    SUM(total_price) as monthly_revenue
    FROM bookings 
    WHERE status = 'completed'
    GROUP BY DATE_FORMAT(booking_date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 6";
$monthly_data = mysqli_query($conn, $sql_monthly);

// Query untuk studio paling populer
$sql_popular = "SELECT 
    s.name,
    COUNT(b.id) as booking_count,
    SUM(b.total_price) as total_revenue
    FROM studios s
    LEFT JOIN bookings b ON s.id = b.studio_id AND b.status = 'completed'
    GROUP BY s.id
    ORDER BY booking_count DESC";
$popular_studios = mysqli_query($conn, $sql_popular);
?>

<div class="admin-content">
    <h2>Laporan Keuangan</h2>
    
    <div class="filter-form">
        <h3>Filter Laporan</h3>
        <form method="GET">
            <div class="form-group">
                <label for="start_date">Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
            </div>
            <div class="form-group">
                <label for="end_date">Tanggal Akhir</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Booking</h3>
            <p><?php echo $stats['total_bookings'] ?: 0; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Pendapatan</h3>
            <p><?php echo formatRupiah($stats['total_revenue'] ?: 0); ?></p>
        </div>
        <div class="stat-card">
            <h3>Rata-rata per Booking</h3>
            <p><?php echo formatRupiah($stats['avg_booking_value'] ?: 0); ?></p>
        </div>
    </div>
    
    <div class="admin-card">
        <h3>Pendapatan per Bulan</h3>
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Jumlah Booking</th>
                    <th>Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                <?php while($month = mysqli_fetch_assoc($monthly_data)): ?>
                <tr>
                    <td><?php echo date('F Y', strtotime($month['month'] . '-01')); ?></td>
                    <td><?php echo $month['booking_count']; ?></td>
                    <td><?php echo formatRupiah($month['monthly_revenue']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <div class="admin-card">
        <h3>Studio Paling Populer</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Studio</th>
                    <th>Jumlah Booking</th>
                    <th>Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                <?php while($studio = mysqli_fetch_assoc($popular_studios)): ?>
                <tr>
                    <td><?php echo $studio['name']; ?></td>
                    <td><?php echo $studio['booking_count']; ?></td>
                    <td><?php echo formatRupiah($studio['total_revenue']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
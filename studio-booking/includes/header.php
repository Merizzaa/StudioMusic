<?php
// Mulai session dan include config
require_once 'config.php';

// Deteksi path dasar berdasarkan lokasi file
$current_dir = dirname($_SERVER['PHP_SELF']);
$is_admin = strpos($current_dir, 'admin') !== false;
$is_staff = strpos($current_dir, 'staff') !== false;
$is_customer = strpos($current_dir, 'customer') !== false;

// Tentukan path untuk assets berdasarkan lokasi
if ($is_admin || $is_staff || $is_customer) {
    $base_url = '../';
} else {
    $base_url = '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Booking Studio Musik</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="<?php echo $base_url; ?>index.php">MusicStudio</a></h1>
            </div>
            <nav>
                <ul>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="<?php echo $base_url; ?>index.php">Home</a></li>
                        <?php if (isAdmin()): ?>
                            <li><a href="<?php echo $base_url; ?>admin/index.php">Dashboard Admin</a></li>
                            <li><a href="<?php echo $base_url; ?>admin/studios.php">Kelola Studio</a></li>
                            <li><a href="<?php echo $base_url; ?>admin/bookings.php">Kelola Booking</a></li>
                            <li><a href="<?php echo $base_url; ?>admin/users.php">Kelola User</a></li>
                        <?php elseif (isStaff()): ?>
                            <li><a href="<?php echo $base_url; ?>staff/index.php">Dashboard Staff</a></li>
                            <li><a href="<?php echo $base_url; ?>staff/bookings.php">Kelola Booking</a></li>
                            <li><a href="<?php echo $base_url; ?>staff/payments.php">Konfirmasi Pembayaran</a></li>
                        <?php elseif (isCustomer()): ?>
                            <li><a href="<?php echo $base_url; ?>customer/index.php">Dashboard Customer</a></li>
                            <li><a href="<?php echo $base_url; ?>customer/booking.php">Booking Studio</a></li>
                            <li><a href="<?php echo $base_url; ?>customer/history.php">Riwayat Booking</a></li>
                            <li><a href="<?php echo $base_url; ?>customer/profile.php">Profil Saya</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo $base_url; ?>logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $base_url; ?>index.php">Home</a></li>
                        <li><a href="<?php echo $base_url; ?>login.php">Login</a></li>
                        <li><a href="<?php echo $base_url; ?>register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">
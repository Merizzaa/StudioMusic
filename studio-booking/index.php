<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio Musik - Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <section class="hero">
            <div class="hero-content">
                <h1>Selamat Datang di MusicStudio</h1>
                <p>Booking studio musik terbaik dengan fasilitas lengkap dan harga terjangkau</p>
                <?php if (!isLoggedIn()): ?>
                    <div class="hero-buttons">
                        <a href="login.php" class="btn btn-primary">Login</a>
                        <a href="register.php" class="btn btn-secondary">Daftar</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="studios-section">
            <h2>Studio Kami</h2>
            <div class="studios-grid">
                <?php
                $sql = "SELECT * FROM studios WHERE status = 'available' ORDER BY created_at DESC LIMIT 3";
                $result = mysqli_query($conn, $sql);
                
                while ($studio = mysqli_fetch_assoc($result)):
                ?>
                <div class="studio-card">
                    <div class="studio-image">
                        <img src="images/<?php echo $studio['image_url'] ?: 'default-studio.jpg'; ?>" alt="<?php echo $studio['name']; ?>">
                    </div>
                    <div class="studio-info">
                        <h3><?php echo $studio['name']; ?></h3>
                        <p><?php echo $studio['description']; ?></p>
                        <div class="studio-price"><?php echo formatRupiah($studio['price_per_hour']); ?> / jam</div>
                        <div class="studio-status">Status: <?php echo getStatusBadge($studio['status']); ?></div>
                        <?php if (isLoggedIn() && isCustomer()): ?>
                            <a href="customer/booking.php?studio_id=<?php echo $studio['id']; ?>" class="btn btn-primary">Booking Sekarang</a>
                        <?php elseif (!isLoggedIn()): ?>
                            <a href="login.php" class="btn btn-primary">Login untuk Booking</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
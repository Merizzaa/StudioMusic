<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    
    // Validasi
    if ($password !== $confirm_password) {
        $error = "Password tidak cocok.";
    } else {
        // Cek apakah username sudah ada
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $error = "Username sudah digunakan.";
            } else {
                // Cek apakah email sudah ada
                $sql = "SELECT id FROM users WHERE email = ?";
                
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "s", $email);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_store_result($stmt);
                    
                    if (mysqli_stmt_num_rows($stmt) > 0) {
                        $error = "Email sudah digunakan.";
                    } else {
                        // Insert user baru
                        $sql = "INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, 'customer')";
                        
                        if ($stmt = mysqli_prepare($conn, $sql)) {
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            mysqli_stmt_bind_param($stmt, "ssss", $username, $hashed_password, $email, $full_name);
                            
                            if (mysqli_stmt_execute($stmt)) {
                                header("Location: login.php?registered=1");
                                exit();
                            } else {
                                $error = "Terjadi kesalahan. Silakan coba lagi.";
                            }
                        }
                    }
                }
            }
        }
    }
    mysqli_close($conn);
}
?>

<?php include 'includes/header.php'; ?>
<div class="register-container">
    <div class="register-form">
        <h2>Daftar Akun Baru</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="full_name">Nama Lengkap</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <button type="submit">Daftar</button>
            </div>
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
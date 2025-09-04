<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Cek user di database
    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $role);
                
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        // Password benar, buat session
                        session_start();
                        
                        $_SESSION['user_id'] = $id;
                        $_SESSION['username'] = $username;
                        $_SESSION['role'] = $role;
                        
                        // Redirect berdasarkan role
                        switch ($role) {
                            case 'admin':
                                header("Location: admin/index.php");
                                break;
                            case 'staff':
                                header("Location: staff/index.php");
                                break;
                            case 'customer':
                                header("Location: customer/index.php");
                                break;
                            default:
                                header("Location: index.php");
                        }
                        exit();
                    } else {
                        $error = "Password salah.";
                    }
                }
            } else {
                $error = "Username tidak ditemukan.";
            }
        } else {
            $error = "Terjadi kesalahan. Silakan coba lagi.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>

<?php include 'includes/header.php'; ?>
<div class="login-container">
    <div class="login-form">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
            <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
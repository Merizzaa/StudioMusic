<?php
require_once '../includes/header.php';
requireRole(['customer']);

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Ambil data user
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    
    // Cek apakah username sudah digunakan oleh user lain
    $sql = "SELECT id FROM users WHERE username = '$username' AND id != '$user_id'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $error = "Username sudah digunakan.";
    } else {
        // Cek apakah email sudah digunakan oleh user lain
        $sql = "SELECT id FROM users WHERE email = '$email' AND id != '$user_id'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $error = "Email sudah digunakan.";
        } else {
            // Update data user
            $sql = "UPDATE users SET username='$username', email='$email', full_name='$full_name' WHERE id='$user_id'";
            
            if (mysqli_query($conn, $sql)) {
                $success = "Profile berhasil diperbarui.";
                // Update session
                $_SESSION['username'] = $username;
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}

// Update password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verifikasi password saat ini
    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            // Hash password baru
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password
            $sql = "UPDATE users SET password='$hashed_password' WHERE id='$user_id'";
            
            if (mysqli_query($conn, $sql)) {
                $success = "Password berhasil diperbarui.";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        } else {
            $error = "Password baru tidak cocok.";
        }
    } else {
        $error = "Password saat ini salah.";
    }
}
?>

<div class="profile-container">
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
        <div class="profile-form">
            <h3>Informasi Profil</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="full_name">Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary">Perbarui Profil</button>
            </form>
        </div>
        
        <div class="password-form">
            <h3>Ubah Password</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="current_password">Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password Baru</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" name="update_password" class="btn btn-primary">Ubah Password</button>
            </form>
        </div>
</div>

<?php include '../includes/footer.php'; ?>
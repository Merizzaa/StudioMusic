<?php
require_once '../includes/header.php';
requireRole(['admin']);

// Tambah user baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, password, email, full_name, role) VALUES ('$username', '$hashed_password', '$email', '$full_name', '$role')";
    
    if (mysqli_query($conn, $sql)) {
        $success = "User berhasil ditambahkan.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Edit user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    $sql = "UPDATE users SET username='$username', email='$email', full_name='$full_name', role='$role' WHERE id='$id'";
    
    if (mysqli_query($conn, $sql)) {
        $success = "User berhasil diperbarui.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Hapus user
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    // Tidak boleh hapus diri sendiri
    if ($id != $_SESSION['user_id']) {
        $sql = "DELETE FROM users WHERE id='$id'";
        
        if (mysqli_query($conn, $sql)) {
            $success = "User berhasil dihapus.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    } else {
        $error = "Tidak dapat menghapus akun sendiri.";
    }
}

// Ambil data users
$sql = "SELECT * FROM users ORDER BY created_at DESC";
$users = mysqli_query($conn, $sql);
?>

<div class="admin-content">
    <h2>Manajemen User</h2>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="admin-card">
        <h3>Tambah User Baru</h3>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
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
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                    <option value="customer">Customer</option>
                </select>
            </div>
            <button type="submit" name="add_user" class="btn btn-primary">Tambah User</button>
        </form>
    </div>
    
    <div class="admin-card">
        <h3>Daftar User</h3>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Nama Lengkap</th>
                    <th>Role</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['full_name']; ?></td>
                    <td><?php echo ucfirst($user['role']); ?></td>
                    <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <a href="#" class="btn btn-sm btn-primary" onclick="editUser(<?php echo $user['id']; ?>, '<?php echo $user['username']; ?>', '<?php echo $user['email']; ?>', '<?php echo $user['full_name']; ?>', '<?php echo $user['role']; ?>')">Edit</a>
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <a href="?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit User -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Edit User</h3>
        <form id="editForm" method="POST">
            <input type="hidden" id="edit_id" name="id">
            <div class="form-group">
                <label for="edit_username">Username</label>
                <input type="text" id="edit_username" name="username" required>
            </div>
            <div class="form-group">
                <label for="edit_email">Email</label>
                <input type="email" id="edit_email" name="email" required>
            </div>
            <div class="form-group">
                <label for="edit_full_name">Nama Lengkap</label>
                <input type="text" id="edit_full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="edit_role">Role</label>
                <select id="edit_role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                    <option value="customer">Customer</option>
                </select>
            </div>
            <button type="submit" name="edit_user" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
// Fungsi untuk modal edit user
function editUser(id, username, email, fullName, role) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_full_name').value = fullName;
    document.getElementById('edit_role').value = role;
    
    document.getElementById('editModal').style.display = 'block';
}

// Tutup modal
document.querySelector('.close').addEventListener('click', function() {
    document.getElementById('editModal').style.display = 'none';
});

// Tutup modal jika klik di luar
window.addEventListener('click', function(event) {
    if (event.target == document.getElementById('editModal')) {
        document.getElementById('editModal').style.display = 'none';
    }
});
</script>

<?php include '../includes/footer.php'; ?>
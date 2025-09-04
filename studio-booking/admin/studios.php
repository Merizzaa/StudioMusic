<?php
require_once '../includes/header.php';
requireRole(['admin']);

// Tambah studio baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_studio'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price_per_hour = mysqli_real_escape_string($conn, $_POST['price_per_hour']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $sql = "INSERT INTO studios (name, description, price_per_hour, status) VALUES ('$name', '$description', '$price_per_hour', '$status')";
    
    if (mysqli_query($conn, $sql)) {
        $success = "Studio berhasil ditambahkan.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Edit studio
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_studio'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price_per_hour = mysqli_real_escape_string($conn, $_POST['price_per_hour']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $sql = "UPDATE studios SET name='$name', description='$description', price_per_hour='$price_per_hour', status='$status' WHERE id='$id'";
    
    if (mysqli_query($conn, $sql)) {
        $success = "Studio berhasil diperbarui.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Hapus studio
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    $sql = "DELETE FROM studios WHERE id='$id'";
    
    if (mysqli_query($conn, $sql)) {
        $success = "Studio berhasil dihapus.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Ambil data studios
$sql = "SELECT * FROM studios ORDER BY created_at DESC";
$studios = mysqli_query($conn, $sql);
?>

<div class="admin-content">
    <h2>Manajemen Studio</h2>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="admin-card">
        <h3>Tambah Studio Baru</h3>
        <form method="POST">
            <div class="form-group">
                <label for="name">Nama Studio</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="price_per_hour">Harga per Jam (Rp)</label>
                <input type="number" id="price_per_hour" name="price_per_hour" min="0" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="available">Available</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="booked">Booked</option>
                </select>
            </div>
            <button type="submit" name="add_studio" class="btn btn-primary">Tambah Studio</button>
        </form>
    </div>
    
    <div class="admin-card">
        <h3>Daftar Studio</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Harga/Jam</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($studio = mysqli_fetch_assoc($studios)): ?>
                <tr>
                    <td><?php echo $studio['name']; ?></td>
                    <td><?php echo $studio['description']; ?></td>
                    <td><?php echo formatRupiah($studio['price_per_hour']); ?></td>
                    <td><?php echo getStatusBadge($studio['status']); ?></td>
                    <td>
                        <a href="#" class="btn btn-sm btn-primary" onclick="editStudio(<?php echo $studio['id']; ?>, '<?php echo $studio['name']; ?>', '<?php echo $studio['description']; ?>', <?php echo $studio['price_per_hour']; ?>, '<?php echo $studio['status']; ?>')">Edit</a>
                        <a href="?delete=<?php echo $studio['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus studio ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit Studio -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Edit Studio</h3>
        <form id="editForm" method="POST">
            <input type="hidden" id="edit_id" name="id">
            <div class="form-group">
                <label for="edit_name">Nama Studio</label>
                <input type="text" id="edit_name" name="name" required>
            </div>
            <div class="form-group">
                <label for="edit_description">Deskripsi</label>
                <textarea id="edit_description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="edit_price">Harga per Jam (Rp)</label>
                <input type="number" id="edit_price" name="price_per_hour" min="0" required>
            </div>
            <div class="form-group">
                <label for="edit_status">Status</label>
                <select id="edit_status" name="status" required>
                    <option value="available">Available</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="booked">Booked</option>
                </select>
            </div>
            <button type="submit" name="edit_studio" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
// Fungsi untuk modal edit studio
function editStudio(id, name, description, price, status) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_price').value = price;
    document.getElementById('edit_status').value = status;
    
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
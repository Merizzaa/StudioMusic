</main>
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Sistem Booking Studio Musik. All rights reserved.</p>
        </div>
    </footer>
    <script src="<?php 
        // Deteksi path untuk JS
        $current_dir = dirname($_SERVER['PHP_SELF']);
        $is_subdir = (strpos($current_dir, 'admin') !== false || 
                     strpos($current_dir, 'staff') !== false || 
                     strpos($current_dir, 'customer') !== false);
        echo $is_subdir ? '../' : '';
    ?>js/script.js"></script>
</body>
</html>
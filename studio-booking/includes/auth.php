<?php
// Cek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect jika belum login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../login.php");
        exit();
    }
}

// Redirect berdasarkan role
function redirectBasedOnRole($role) {
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
}

// Cek role dan redirect jika tidak sesuai
function requireRole($allowed_roles) {
    requireLogin();
    
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: ../unauthorized.php");
        exit();
    }
}

// Cek apakah user adalah admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// Cek apakah user adalah staff
function isStaff() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'staff';
}

// Cek apakah user adalah customer
function isCustomer() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'customer';
}
?>
<?php
session_start();

// Pengaturan database
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'studio_booking');

// Koneksi ke database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Cek koneksi
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Include functions
require_once 'functions.php';
require_once 'auth.php';
?>
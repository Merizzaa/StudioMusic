<?php
// Fungsi bantuan untuk sistem

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function getStatusBadge($status) {
    $statusClass = '';
    switch ($status) {
        case 'pending':
            $statusClass = 'status-pending';
            break;
        case 'confirmed':
            $statusClass = 'status-confirmed';
            break;
        case 'cancelled':
            $statusClass = 'status-cancelled';
            break;
        case 'completed':
            $statusClass = 'status-completed';
            break;
        case 'verified':
            $statusClass = 'status-confirmed';
            break;
        case 'rejected':
            $statusClass = 'status-cancelled';
            break;
        default:
            $statusClass = 'status-pending';
    }
    return '<span class="status-badge ' . $statusClass . '">' . $status . '</span>';
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}
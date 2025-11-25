<?php
// bus_management/index.php

session_start();
require 'config/database.php';

// 1. Cek apakah user sudah login
if (!isset($_SESSION['login'])) {
    // Jika belum, tendang ke halaman login
    header("Location: " . $base_url . "auth/login.php");
    exit;
}

// 2. Jika sudah login, cek role-nya dan arahkan ke folder yang sesuai
$role = $_SESSION['role'];

switch ($role) {
    case 'admin':
        header("Location: " . $base_url . "admin/dashboard.php");
        break;
        
    case 'mandor':
        // Mandor tugas utamanya input, jadi kita arahkan langsung ke input atau riwayat
        header("Location: " . $base_url . "mandor/riwayat.php");
        break;
        
    case 'direksi':
        header("Location: " . $base_url . "direksi/dashboard.php");
        break;
        
    default:
        // Jaga-jaga jika ada role aneh, balikin ke login
        session_destroy();
        header("Location: " . $base_url . "auth/login.php");
        break;
}

exit;
?>
<?php
session_start();
// Cek apakah config database sudah di-include sebelumnya, jika belum include
if(!isset($conn)){
    include '../config/database.php'; 
}

// Cek Login: Kecuali di halaman login, user harus login
// Kita pakai logika sederhana: jika ini bukan halaman login dan session kosong, tendang keluar
if (!isset($_SESSION['login']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header("Location: " . $base_url . "auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Bus</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'], // Mengganti font default jadi Poppins
                    },
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="<?= $base_url ?>assets/css/style.css">

    <style>
        /* Tambahan style untuk transisi sidebar */
        .sidebar { transition: all 0.3s; }
        
        /* Memastikan font Poppins teraplikasi ke seluruh body jika tailwind gagal load (fallback) */
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">

    <div id="loader">
        <div class="loader-content">
            <div class="spinner"></div>
            <h2 class="text-xl font-bold text-gray-700 mt-4">Sistem Manajemen Bus</h2>
            <p class="text-gray-500 text-sm">Memuat data...</p>
        </div>
    </div>

    <div class="flex h-screen overflow-hidden">
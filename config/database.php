<?php
// config/database.php

$host = "localhost";
$user = "root"; // Sesuaikan user db kamu
$pass = "";     // Sesuaikan password db kamu
$db   = "db_bus_management";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

// Base URL (Ganti sesuai nama folder kamu di htdocs)
// Pastikan ada slash di akhir
$base_url = "http://localhost/bus_management/"; 
?>
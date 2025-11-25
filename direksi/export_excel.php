<?php
// direksi/export_excel.php
include '../config/database.php';

// Validasi Login & Role
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'direksi') {
    exit("Akses Ditolak");
}

// Header untuk memicu download Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Keberangkatan_" . date('Y-m-d') . ".xls");

?>

<h3>Data Laporan Keberangkatan Bus</h3>
<p>Tanggal Export: <?= date('d-m-Y H:i') ?></p>

<table border="1">
    <thead>
        <tr style="background-color: #f0f0f0; font-weight: bold;">
            <th>No</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>No Polisi</th>
            <th>Driver</th>
            <th>Tujuan</th>
            <th>Penumpang</th>
            <th>Mandor</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $query = mysqli_query($conn, "SELECT l.*, u.nama_lengkap as nama_mandor 
                                      FROM laporan_keberangkatan l 
                                      JOIN users u ON l.user_id = u.id 
                                      ORDER BY l.tanggal_berangkat DESC");
        
        while($row = mysqli_fetch_assoc($query)):
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['tanggal_berangkat'] ?></td>
            <td><?= $row['waktu_berangkat'] ?></td>
            <td><?= $row['no_polisi'] ?></td>
            <td><?= $row['nama_driver'] ?></td>
            <td><?= $row['tujuan'] ?></td>
            <td><?= $row['jumlah_penumpang'] ?></td>
            <td><?= $row['nama_mandor'] ?></td>
            <td><?= $row['status'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
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
header("Content-Disposition: attachment; filename=Laporan_Keberangkatan_" . date('Y-m-d_H-i') . ".xls");

?>

<h3>Data Laporan Keberangkatan Bus</h3>
<p>Tanggal Export: <?= date('d F Y, H:i') ?> WIB</p>

<table border="1">
    <thead>
        <tr style="background-color: #f0f0f0; font-weight: bold; text-align: center;">
            <th width="50">No</th>
            <th width="120">Tanggal</th>
            <th width="80">Jam</th>
            <th width="100">No Polisi</th>
            <th width="150">Driver</th>
            <th width="150">Lokasi Asal</th>
            <th width="150">Lokasi Tujuan</th>
            <th width="100">Penumpang</th>
            <th width="150">Mandor</th>
            <th width="100">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        
        // QUERY UPDATE: JOIN KE TABEL LOKASI (ASAL & TUJUAN)
        $query = mysqli_query($conn, "SELECT l.*, 
                                             u.nama_lengkap as nama_mandor,
                                             k_asal.nama_lokasi as nama_asal,
                                             k_tujuan.nama_lokasi as nama_tujuan
                                      FROM laporan_keberangkatan l 
                                      JOIN users u ON l.user_id = u.id 
                                      LEFT JOIN kategori_lokasi k_asal ON l.lokasi_id = k_asal.id
                                      LEFT JOIN kategori_lokasi k_tujuan ON l.tujuan_id = k_tujuan.id
                                      ORDER BY l.tanggal_berangkat DESC");
        
        if (mysqli_num_rows($query) > 0) {
            while($row = mysqli_fetch_assoc($query)):
                // Sanitasi data jika kosong
                $asal = $row['nama_asal'] ?? '-';
                $tujuan = $row['nama_tujuan'] ?? '-';
                $status = ucfirst($row['status']); // Kapitalisasi status
        ?>
        <tr>
            <td style="text-align: center;"><?= $no++ ?></td>
            <td style="text-align: left;"><?= date('d/m/Y', strtotime($row['tanggal_berangkat'])) ?></td>
            <td style="text-align: center;"><?= substr($row['waktu_berangkat'], 0, 5) ?></td>
            <td style="text-align: center;"><?= $row['no_polisi'] ?></td>
            <td><?= $row['nama_driver'] ?></td>
            
            <td><?= $asal ?></td>
            <td><?= $tujuan ?></td>
            
            <td style="text-align: center;"><?= $row['jumlah_penumpang'] ?></td>
            <td><?= $row['nama_mandor'] ?></td>
            <td style="text-align: center;"><?= $status ?></td>
        </tr>
        <?php 
            endwhile; 
        } else {
            echo '<tr><td colspan="10" style="text-align:center;">Tidak ada data laporan.</td></tr>';
        }
        ?>
    </tbody>
</table>
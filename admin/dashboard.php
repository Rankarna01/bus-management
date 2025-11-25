<?php
// admin/dashboard.php

// Include header (sudah ada session start & koneksi db)
include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/topbar.php';

// --- LOGIKA PHP UNTUK STATISTIK ---
// 1. Total Laporan
$q_laporan = mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan_keberangkatan");
$d_laporan = mysqli_fetch_assoc($q_laporan);

// 2. Total Penumpang
$q_penumpang = mysqli_query($conn, "SELECT SUM(jumlah_penumpang) as total FROM laporan_keberangkatan");
$d_penumpang = mysqli_fetch_assoc($q_penumpang);

// 3. Laporan Pending (Belum diverifikasi)
$q_pending = mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan_keberangkatan WHERE status = 'pending'");
$d_pending = mysqli_fetch_assoc($q_pending);

// 4. Jumlah Mandor Aktif
$q_mandor = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'mandor'");
$d_mandor = mysqli_fetch_assoc($q_mandor);
?>

<!-- HEADER PAGE -->
<div class="mb-8">
    <h3 class="text-3xl font-bold text-slate-800 tracking-tight">Dashboard Overview</h3>
    <p class="text-slate-500 mt-1">Ringkasan aktivitas operasional armada hari ini.</p>
</div>

<!-- GRID KARTU STATISTIK -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    
    <!-- Kartu 1: Total Perjalanan -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center justify-between hover:shadow-md transition duration-300">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Perjalanan</p>
            <h4 class="text-3xl font-bold text-slate-800"><?= number_format($d_laporan['total']) ?></h4>
        </div>
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-xl">
            <i class="fa-solid fa-bus-simple"></i>
        </div>
    </div>

    <!-- Kartu 2: Total Penumpang -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center justify-between hover:shadow-md transition duration-300">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Penumpang</p>
            <h4 class="text-3xl font-bold text-slate-800"><?= number_format($d_penumpang['total']) ?></h4>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>

    <!-- Kartu 3: Perlu Verifikasi -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center justify-between hover:shadow-md transition duration-300 relative overflow-hidden">
        <?php if($d_pending['total'] > 0): ?>
            <div class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full animate-ping mr-2 mt-2"></div>
        <?php endif; ?>
        
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Perlu Verifikasi</p>
            <h4 class="text-3xl font-bold text-slate-800"><?= number_format($d_pending['total']) ?></h4>
        </div>
        <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-xl">
            <i class="fa-solid fa-clipboard-list"></i>
        </div>
    </div>

    <!-- Kartu 4: Mandor Aktif -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center justify-between hover:shadow-md transition duration-300">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Mandor Aktif</p>
            <h4 class="text-3xl font-bold text-slate-800"><?= number_format($d_mandor['total']) ?></h4>
        </div>
        <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center text-violet-600 text-xl">
            <i class="fa-solid fa-user-tie"></i>
        </div>
    </div>
</div>

<!-- TABEL LAPORAN TERBARU -->
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <!-- Header Tabel -->
    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
        <div>
            <h4 class="text-lg font-bold text-slate-800">Laporan Masuk Terbaru</h4>
            <p class="text-xs text-slate-500">5 data keberangkatan terakhir.</p>
        </div>
        <a href="laporan.php" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition flex items-center">
            Lihat Semua <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 uppercase text-xs font-semibold tracking-wide border-b border-slate-200">
                    <th class="py-4 px-6"><i class="fa-regular fa-calendar mr-2"></i>Tanggal</th>
                    <th class="py-4 px-6"><i class="fa-solid fa-user-gear mr-2"></i>Mandor</th>
                    <th class="py-4 px-6 text-center"><i class="fa-solid fa-bus mr-2"></i>Armada</th>
                    <th class="py-4 px-6 text-center"><i class="fa-solid fa-map-location-dot mr-2"></i>Tujuan</th>
                    <th class="py-4 px-6 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="text-slate-600 text-sm">
                <?php
                // Query ambil 5 data terbaru
                $query = "SELECT l.*, u.nama_lengkap as nama_mandor 
                          FROM laporan_keberangkatan l 
                          JOIN users u ON l.user_id = u.id 
                          ORDER BY l.created_at DESC LIMIT 5";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr class="border-b border-slate-100 hover:bg-slate-50 transition duration-150">
                            <!-- Kolom Tanggal -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="font-bold text-slate-800"><?= date('d M Y', strtotime($row['tanggal_berangkat'])) ?></div>
                                <div class="text-xs text-blue-600 font-medium mt-1">
                                    <i class="fa-regular fa-clock"></i> <?= substr($row['waktu_berangkat'], 0, 5) ?> WIB
                                </div>
                            </td>

                            <!-- Kolom Mandor -->
                            <td class="py-4 px-6">
                                <span class="font-medium text-slate-700"><?= $row['nama_mandor'] ?></span>
                            </td>

                            <!-- Kolom Armada -->
                            <td class="py-4 px-6 text-center">
                                <div class="font-bold text-slate-800"><?= $row['no_polisi'] ?></div>
                                <div class="text-xs text-slate-400 mt-0.5"><?= $row['nama_driver'] ?></div>
                            </td>

                            <!-- Kolom Tujuan -->
                            <td class="py-4 px-6 text-center">
                                <span class="px-2 py-1 bg-slate-100 rounded text-xs text-slate-600 font-medium">
                                    <?= $row['tujuan'] ?>
                                </span>
                            </td>

                            <!-- Kolom Status -->
                            <td class="py-4 px-6 text-center">
                                <?php if($row['status'] == 'terverifikasi'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-600/20">
                                        Verified
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 ring-1 ring-inset ring-amber-600/20 animate-pulse">
                                        Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile;
                } else {
                    echo "<tr><td colspan='5' class='text-center py-8 text-slate-400 italic'>Belum ada data laporan masuk hari ini.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Include footer
include '../layouts/footer.php';
?>
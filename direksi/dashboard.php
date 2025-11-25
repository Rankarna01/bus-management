<?php
// direksi/dashboard.php
include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/topbar.php';

// --- 1. DATA KARTU ATAS (Ringkasan) ---
// Total Trip (Verified only untuk Direksi)
$d_trip = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan_keberangkatan WHERE status='terverifikasi'"));
// Total Penumpang
$d_pax  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah_penumpang) as total FROM laporan_keberangkatan WHERE status='terverifikasi'"));
// Armada Aktif (Distinct No Polisi)
$d_bus  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT no_polisi) as total FROM laporan_keberangkatan WHERE status='terverifikasi'"));
// Total Mandor
$d_mandor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='mandor'"));


// --- 2. DATA CHART 1: TREN HARIAN (7 Hari Terakhir) ---
$labels_harian = [];
$data_harian   = [];
// Loop 7 hari ke belakang
for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $labels_harian[] = date('d M', strtotime($tgl)); // Label sumbu X (misal: 25 Nov)
    
    // Query hitung trip per tanggal tersebut
    $sql_chart1 = mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan_keberangkatan WHERE date(tanggal_berangkat) = '$tgl' AND status='terverifikasi'");
    $row_chart1 = mysqli_fetch_assoc($sql_chart1);
    $data_harian[] = $row_chart1['total'];
}

// --- 3. DATA CHART 2: PERFORMA BULANAN (Tahun Ini) ---
$labels_bulanan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$data_bulanan_trip = array_fill(0, 12, 0); // Siapkan array kosong 12 bulan
$tahun_ini = date('Y');

$sql_chart2 = mysqli_query($conn, "SELECT MONTH(tanggal_berangkat) as bulan, COUNT(*) as total 
                                   FROM laporan_keberangkatan 
                                   WHERE YEAR(tanggal_berangkat) = '$tahun_ini' AND status='terverifikasi' 
                                   GROUP BY bulan");

while($row = mysqli_fetch_assoc($sql_chart2)){
    // index array dimulai dari 0 (Januari=1 dikurang 1 jadi 0)
    $data_bulanan_trip[$row['bulan'] - 1] = $row['total'];
}

?>

<!-- Inject Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- HEADER PAGE -->
<div class="mb-8 flex flex-col md:flex-row justify-between items-end gap-4">
    <div>
        <h3 class="text-3xl font-bold text-slate-800 tracking-tight">Executive Dashboard</h3>
        <p class="text-slate-500 mt-1">Statistik kinerja operasional & pertumbuhan armada.</p>
    </div>
    <!-- Filter Tahun (Visual Saja) -->
    <div class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm">
        <i class="fa-regular fa-calendar mr-2"></i> Tahun <?= date('Y') ?>
    </div>
</div>

<!-- 1. KARTU STATISTIK UTAMA -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Card Trip -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between group hover:border-blue-200 transition">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Trip (Verif)</p>
            <h4 class="text-3xl font-bold text-slate-800"><?= number_format($d_trip['total']) ?></h4>
        </div>
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-xl group-hover:scale-110 transition">
            <i class="fa-solid fa-route"></i>
        </div>
    </div>

    <!-- Card Passenger -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between group hover:border-emerald-200 transition">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Penumpang</p>
            <h4 class="text-3xl font-bold text-slate-800"><?= number_format($d_pax['total']) ?></h4>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl group-hover:scale-110 transition">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>

    <!-- Card Active Bus -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between group hover:border-indigo-200 transition">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Armada Beroperasi</p>
            <h4 class="text-3xl font-bold text-slate-800"><?= number_format($d_bus['total']) ?></h4>
        </div>
        <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl group-hover:scale-110 transition">
            <i class="fa-solid fa-bus"></i>
        </div>
    </div>

    <!-- Card Staff -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between group hover:border-violet-200 transition">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Mandor</p>
            <h4 class="text-3xl font-bold text-slate-800"><?= number_format($d_mandor['total']) ?></h4>
        </div>
        <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center text-violet-600 text-xl group-hover:scale-110 transition">
            <i class="fa-solid fa-user-tie"></i>
        </div>
    </div>
</div>

<!-- 2. SECTION GRAFIK (CHARTS) -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Chart 1: Tren Harian (Lebar 2/3) -->
    <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-lg font-bold text-slate-800">Tren Perjalanan (7 Hari Terakhir)</h4>
            <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded">Realtime Data</span>
        </div>
        <div class="relative h-72">
            <canvas id="dailyChart"></canvas>
        </div>
    </div>

    <!-- Chart 2: Komposisi Bulanan (Lebar 1/3) -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="mb-6">
            <h4 class="text-lg font-bold text-slate-800">Performa Bulanan</h4>
            <p class="text-xs text-slate-400">Total trip per bulan tahun <?= date('Y') ?></p>
        </div>
        <div class="relative h-72">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>
</div>

<!-- 3. TABEL RINGKASAN TERBARU -->
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
        <div>
            <h4 class="text-lg font-bold text-slate-800">Aktivitas Terkini</h4>
            <p class="text-xs text-slate-500">Pantauan laporan masuk realtime.</p>
        </div>
        <a href="laporan.php" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition">Lihat Detail <i class="fa-solid fa-arrow-right ml-1"></i></a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 uppercase text-xs font-semibold tracking-wide border-b border-slate-200">
                    <th class="py-4 px-6">Tanggal</th>
                    <th class="py-4 px-6 text-center">Armada</th>
                    <th class="py-4 px-6 text-center">Rute</th>
                    <th class="py-4 px-6 text-center">Penumpang</th>
                </tr>
            </thead>
            <tbody class="text-slate-600 text-sm">
                <?php
                // Ambil 5 data verified terbaru
                $q_rec = mysqli_query($conn, "SELECT * FROM laporan_keberangkatan WHERE status='terverifikasi' ORDER BY tanggal_berangkat DESC LIMIT 5");
                if (mysqli_num_rows($q_rec) > 0) {
                    while ($r = mysqli_fetch_assoc($q_rec)) : ?>
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="py-4 px-6 font-bold text-slate-800">
                            <?= date('d M Y', strtotime($r['tanggal_berangkat'])) ?>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold"><?= $r['no_polisi'] ?></span>
                        </td>
                        <td class="py-4 px-6 text-center"><?= $r['tujuan'] ?></td>
                        <td class="py-4 px-6 text-center font-bold text-emerald-600"><?= $r['jumlah_penumpang'] ?></td>
                    </tr>
                <?php endwhile; } else { echo "<tr><td colspan='4' class='text-center py-6'>Belum ada data.</td></tr>"; } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- KONFIGURASI CHART.JS -->
<script>
    // Konfigurasi Font Global Chart.js agar sesuai Poppins
    Chart.defaults.font.family = "'Poppins', sans-serif";
    Chart.defaults.color = '#64748b';

    // 1. DATA CHART HARIAN
    const ctxDaily = document.getElementById('dailyChart').getContext('2d');
    new Chart(ctxDaily, {
        type: 'line', // Grafik Garis
        data: {
            labels: <?= json_encode($labels_harian) ?>, // Dari PHP
            datasets: [{
                label: 'Jumlah Perjalanan',
                data: <?= json_encode($data_harian) ?>, // Dari PHP
                borderColor: '#2563eb', // Blue-600
                backgroundColor: 'rgba(37, 99, 235, 0.1)', // Blue transparent
                borderWidth: 3,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#2563eb',
                pointRadius: 5,
                fill: true,
                tension: 0.4 // Garis melengkung halus
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [2, 4], color: '#e2e8f0' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 2. DATA CHART BULANAN
    const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctxMonthly, {
        type: 'bar', // Grafik Batang
        data: {
            labels: <?= json_encode($labels_bulanan) ?>,
            datasets: [{
                label: 'Total Trip',
                data: <?= json_encode($data_bulanan_trip) ?>,
                backgroundColor: '#10b981', // Emerald-500
                borderRadius: 6,
                barThickness: 12
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [2, 4], color: '#e2e8f0' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>

<?php include '../layouts/footer.php'; ?>
<?php
// Mendapatkan nama file saat ini
$current_page = basename($_SERVER['PHP_SELF']);

// Style untuk Menu AKTIF (Background Biru, Teks Putih, Shadow Biru)
$active_class = "bg-blue-600 text-white shadow-lg shadow-blue-500/30";

// Style untuk Menu TIDAK AKTIF (Teks Abu, Hover jadi Biru Muda)
$inactive_class = "text-slate-500 hover:bg-blue-50 hover:text-blue-600 font-medium";
?>

<aside class="w-64 bg-white flex flex-col shadow-xl sidebar hidden md:flex font-sans border-r border-slate-100 relative z-20 h-screen fixed">
    
    <div class="h-20 flex items-center justify-center border-b border-slate-100">
        <h1 class="text-2xl font-bold tracking-tight text-slate-800 flex items-center gap-2">
            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-blue-500/30 shadow-lg">
                <i class="fa-solid fa-bus-simple"></i>
            </div>
            <span>BUS<span class="text-blue-600">MAN</span></span>
        </h1>
    </div>

    <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto custom-scrollbar">
        
        <?php $role = $_SESSION['role'] ?? ''; ?>

        <?php if ($role == 'admin') : ?>
            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-2">Administrator</p>
            
            <a href="<?= $base_url ?>admin/dashboard.php" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group <?= $current_page == 'dashboard.php' ? $active_class : $inactive_class ?>">
                <i class="fa-solid fa-chart-pie w-6 text-center group-hover:scale-110 transition-transform"></i>
                <span class="text-sm tracking-wide ml-2">Dashboard</span>
            </a>
            
            <a href="<?= $base_url ?>admin/laporan.php" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group <?= $current_page == 'laporan.php' ? $active_class : $inactive_class ?>">
                <i class="fa-solid fa-file-invoice w-6 text-center group-hover:scale-110 transition-transform"></i>
                <span class="text-sm tracking-wide ml-2">Data Laporan</span>
            </a>
            
            <a href="<?= $base_url ?>admin/users.php" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group <?= $current_page == 'users.php' ? $active_class : $inactive_class ?>">
                <i class="fa-solid fa-users-gear w-6 text-center group-hover:scale-110 transition-transform"></i>
                <span class="text-sm tracking-wide ml-2">Manajemen User</span>
            </a>
        <?php endif; ?>


        <?php if ($role == 'mandor') : ?>
            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-2">Menu Mandor</p>
            
            <a href="<?= $base_url ?>mandor/riwayat.php" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group <?= $current_page == 'riwayat.php' ? $active_class : $inactive_class ?>">
                <i class="fa-solid fa-clipboard-list w-6 text-center group-hover:scale-110 transition-transform"></i>
                <span class="text-sm tracking-wide ml-2">Operasional Bus</span>
            </a>
        <?php endif; ?>


        <?php if ($role == 'direksi') : ?>
            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-2">Eksekutif</p>
            
            <a href="<?= $base_url ?>direksi/dashboard.php" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group <?= $current_page == 'dashboard.php' ? $active_class : $inactive_class ?>">
                <i class="fa-solid fa-chart-line w-6 text-center group-hover:scale-110 transition-transform"></i>
                <span class="text-sm tracking-wide ml-2">Statistik Utama</span>
            </a>
            
            <a href="<?= $base_url ?>direksi/laporan.php" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group <?= $current_page == 'laporan.php' ? $active_class : $inactive_class ?>">
                <i class="fa-solid fa-folder-open w-6 text-center group-hover:scale-110 transition-transform"></i>
                <span class="text-sm tracking-wide ml-2">Arsip Laporan</span>
            </a>
        <?php endif; ?>

    </nav>

    <div class="p-4 border-t border-slate-100 bg-white">
        <a href="<?= $base_url ?>auth/logout.php" class="flex items-center justify-center px-4 py-3 text-red-500 bg-red-50 hover:bg-red-100 hover:text-red-600 rounded-xl transition-all duration-200 group">
            <i class="fa-solid fa-right-from-bracket w-5 group-hover:-translate-x-1 transition-transform"></i>
            <span class="font-bold text-xs ml-2">Logout System</span>
        </a>
        <div class="text-center mt-3 text-[10px] text-slate-400 font-medium">
            &copy; 2025 Bus Management
        </div>
    </div>
</aside>
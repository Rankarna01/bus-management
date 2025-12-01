<?php
include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/topbar.php';
?>

<div class="flex flex-col md:flex-row justify-between items-end mb-6 gap-4">
    <div>
        <h3 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">Data Lokasi</h3>
        <p class="text-slate-500 mt-1 text-sm">Kelola titik keberangkatan armada (Terminal/Pool).</p>
    </div>
    
    <button onclick="toggleModal('modalLokasi')" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-white transition-all duration-200 bg-blue-600 rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-500/30">
        <i class="fa-solid fa-plus mr-2"></i> Tambah Lokasi
    </button>
</div>

<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden max-w-4xl">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
            <tr>
                <th class="py-4 px-6 w-16 text-center">No</th>
                <th class="py-4 px-6">Nama Lokasi / Titik Point</th>
                <th class="py-4 px-6 text-center w-32">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
            <?php
            $no = 1;
            $q = mysqli_query($conn, "SELECT * FROM kategori_lokasi ORDER BY nama_lokasi ASC");
            if(mysqli_num_rows($q) > 0) {
                while($row = mysqli_fetch_assoc($q)):
            ?>
            <tr class="hover:bg-slate-50 transition">
                <td class="py-4 px-6 text-center font-medium"><?= $no++ ?></td>
                <td class="py-4 px-6 font-bold text-slate-800 text-base">
                    <i class="fa-solid fa-location-dot text-red-500 mr-2"></i> <?= $row['nama_lokasi'] ?>
                </td>
                <td class="py-4 px-6 text-center">
                    <a href="proses_lokasi.php?act=hapus&id=<?= $row['id'] ?>" onclick="return confirm('Hapus lokasi ini?')" class="h-9 w-9 inline-flex items-center justify-center rounded-full bg-rose-50 text-rose-600 hover:bg-rose-100 transition border border-rose-100">
                        <i class="fa-solid fa-trash-can"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; } else { echo "<tr><td colspan='3' class='text-center py-8'>Belum ada data lokasi.</td></tr>"; } ?>
        </tbody>
    </table>
</div>

<div id="modalLokasi" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleModal('modalLokasi')"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Tambah Lokasi Baru</h3>
                    <button onclick="toggleModal('modalLokasi')" class="text-slate-400 hover:text-red-500"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <form action="proses_lokasi.php" method="POST">
                    <input type="hidden" name="act" value="tambah">
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Lokasi</label>
                        <input type="text" name="nama_lokasi" placeholder="Contoh: Terminal B" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-blue-500 focus:bg-white transition outline-none" required>
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-blue-600 py-3 text-sm font-bold text-white hover:bg-blue-700 transition">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleModal(id){ document.getElementById(id).classList.toggle('hidden'); }
</script>

<?php include '../layouts/footer.php'; ?>
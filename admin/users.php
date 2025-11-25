<?php
include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/topbar.php';
?>

<div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
    <div>
        <h3 class="text-3xl font-bold text-slate-800 tracking-tight">Manajemen Pengguna</h3>
        <p class="text-slate-500 mt-1">Kelola akses akun Admin, Mandor, dan Direksi.</p>
    </div>
    
    <button onclick="toggleModal('modalTambah')" class="group relative inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-white transition-all duration-200 bg-blue-600 rounded-xl hover:bg-blue-700 hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600">
        <i class="fa-solid fa-user-plus mr-2 transition-transform group-hover:scale-110"></i>
        Tambah User
    </button>
</div>

<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                    <th class="py-5 px-6"><i class="fa-regular fa-id-card mr-2"></i>Nama Lengkap</th>
                    <th class="py-5 px-6"><i class="fa-solid fa-user-lock mr-2"></i>Username</th>
                    <th class="py-5 px-6 text-center"><i class="fa-solid fa-shield-halved mr-2"></i>Role</th>
                    <th class="py-5 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                <?php
                $query = "SELECT * FROM users ORDER BY role ASC, nama_lengkap ASC";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) : 
                ?>
                <tr class="hover:bg-slate-50 transition-colors duration-200">
                    
                    <td class="py-4 px-6 align-middle">
                        <div class="font-bold text-slate-800 text-base"><?= $row['nama_lengkap'] ?></div>
                        <?php if($row['id'] == $_SESSION['user_id']): ?>
                            <span class="inline-block mt-1 text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full font-bold tracking-wide border border-blue-100">
                                ANDA
                            </span>
                        <?php endif; ?>
                    </td>

                    <td class="py-4 px-6 align-middle">
                        <span class="font-mono text-slate-500 bg-slate-100 px-2 py-1 rounded text-xs">
                            @<?= $row['username'] ?>
                        </span>
                    </td>

                    <td class="py-4 px-6 align-middle text-center">
                        <?php 
                        $badge_class = 'bg-slate-100 text-slate-600 ring-slate-600/20';
                        $icon = 'fa-user';
                        
                        if($row['role'] == 'admin') {
                            $badge_class = 'bg-rose-50 text-rose-600 ring-rose-600/20';
                            $icon = 'fa-crown';
                        }
                        if($row['role'] == 'mandor') {
                            $badge_class = 'bg-blue-50 text-blue-600 ring-blue-600/20';
                            $icon = 'fa-hard-hat';
                        }
                        if($row['role'] == 'direksi') {
                            $badge_class = 'bg-purple-50 text-purple-600 ring-purple-600/20';
                            $icon = 'fa-briefcase';
                        }
                        ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold ring-1 ring-inset <?= $badge_class ?>">
                            <i class="fa-solid <?= $icon ?> text-[10px]"></i> <?= ucfirst($row['role']) ?>
                        </span>
                    </td>

                    <td class="py-4 px-6 align-middle text-center">
                        <div class="flex justify-center items-center gap-2">
                            <button onclick="editUser(
                                '<?= $row['id'] ?>', 
                                '<?= htmlspecialchars($row['nama_lengkap']) ?>', 
                                '<?= htmlspecialchars($row['username']) ?>', 
                                '<?= $row['role'] ?>'
                            )" class="h-9 w-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-100 transition border border-amber-100" title="Edit User">
                                <i class="fa-solid fa-pen text-sm"></i>
                            </button>

                            <?php if($row['id'] != $_SESSION['user_id']): ?>
                                <button onclick="hapusUser(<?= $row['id'] ?>)" class="h-9 w-9 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-100 transition border border-rose-100" title="Hapus User">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalTambah" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="toggleModal('modalTambah')"></div>
    
    <div class="flex min-h-full items-center justify-center p-4 text-center">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-md border border-slate-100">
            
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Tambah Pengguna</h3>
                    <p class="text-xs text-slate-500">Buat akun akses baru.</p>
                </div>
                <button onclick="toggleModal('modalTambah')" class="h-8 w-8 rounded-full bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-red-500 transition flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="proses_users.php" method="POST" class="px-6 py-6 space-y-4">
                <input type="hidden" name="act" value="tambah">
                
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" name="nama" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition" required>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Username</label>
                    <input type="text" name="username" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition" required>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Password</label>
                    <input type="password" name="password" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition" placeholder="******" required>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Role Access</label>
                    <div class="relative">
                        <select name="role" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition appearance-none" required>
                            <option value="mandor">Mandor (Lapangan)</option>
                            <option value="direksi">Direksi (Eksekutif)</option>
                            <option value="admin">Admin (Full Akses)</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition">
                        Simpan Data User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modalEdit" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="toggleModal('modalEdit')"></div>
    
    <div class="flex min-h-full items-center justify-center p-4 text-center">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-md border border-slate-100">
            
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Edit Pengguna</h3>
                    <p class="text-xs text-slate-500">Perbarui informasi akun.</p>
                </div>
                <button onclick="toggleModal('modalEdit')" class="h-8 w-8 rounded-full bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-red-500 transition flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="proses_users.php" method="POST" class="px-6 py-6 space-y-4">
                <input type="hidden" name="act" value="edit">
                <input type="hidden" name="id" id="edit_id">

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" name="nama" id="edit_nama" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-amber-500 focus:bg-white focus:ring-1 focus:ring-amber-500 transition" required>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Username</label>
                    <input type="text" name="username" id="edit_username" class="w-full rounded-xl border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-500 cursor-not-allowed" readonly title="Username tidak bisa diubah">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Password Baru <span class="text-slate-400 font-normal lowercase">(opsional)</span></label>
                    <input type="password" name="password" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-amber-500 focus:bg-white focus:ring-1 focus:ring-amber-500 transition" placeholder="Isi untuk reset password">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Role Access</label>
                    <div class="relative">
                        <select name="role" id="edit_role" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-amber-500 focus:bg-white focus:ring-1 focus:ring-amber-500 transition appearance-none" required>
                            <option value="mandor">Mandor</option>
                            <option value="direksi">Direksi</option>
                            <option value="admin">Admin</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full rounded-xl bg-amber-500 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-amber-500/30 hover:bg-amber-600 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

    // Fungsi Pop-up Edit
    function editUser(id, nama, username, role) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_role').value = role;
        toggleModal('modalEdit');
    }

    // Konfirmasi Hapus Modern
    function hapusUser(id) {
        Swal.fire({
            title: 'Hapus User?',
            text: "Akses akun ini akan dicabut permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl',
                cancelButton: 'rounded-xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "proses_users.php?act=hapus&id=" + id;
            }
        })
    }
</script>

<?php include '../layouts/footer.php'; ?>
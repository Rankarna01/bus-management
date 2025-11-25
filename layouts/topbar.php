<div class="flex-1 flex flex-col overflow-hidden">
    <header class="flex justify-between items-center py-4 px-6 bg-white border-b border-gray-200 shadow-sm">
        <div class="flex items-center">
            <button class="text-gray-500 focus:outline-none md:hidden">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>
            <h2 class="text-xl font-semibold text-gray-800 ml-4">
                Sistem Manajemen Keberangkatan
            </h2>
        </div>

        <div class="flex items-center space-x-4">
            <div class="text-right">
                <p class="text-sm font-bold text-gray-700"><?= $_SESSION['nama_lengkap'] ?? 'Guest' ?></p>
                <span class="text-xs text-white bg-blue-500 px-2 py-1 rounded-full uppercase tracking-wide">
                    <?= $_SESSION['role'] ?? 'Unknown' ?>
                </span>
            </div>
            <div class="relative">
                <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-200" src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama_lengkap'] ?? 'User') ?>&background=random" alt="Profile">
            </div>
        </div>
    </header>
    
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
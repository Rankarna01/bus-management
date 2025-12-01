<div class="flex-1 flex flex-col overflow-hidden relative">
    
    <header class="flex justify-between items-center py-4 px-6 bg-white border-b border-slate-100 shadow-sm z-10">
        
        <div class="flex items-center gap-4">
            
            <button onclick="toggleSidebar()" class="md:hidden text-slate-500 hover:text-blue-600 transition focus:outline-none">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>

            <h2 class="text-lg md:text-xl font-bold text-slate-800 tracking-tight">
                Sistem Manajemen Bus
            </h2>
        </div>

        <div class="flex items-center space-x-4">
            <div class="text-right hidden md:block">
                <p class="text-sm font-bold text-slate-700"><?= $_SESSION['nama_lengkap'] ?? 'Guest' ?></p>
                <span class="text-[10px] font-bold text-white bg-blue-600 px-2 py-0.5 rounded-full uppercase tracking-wide">
                    <?= $_SESSION['role'] ?? 'Unknown' ?>
                </span>
            </div>
            <div class="relative">
                <img class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-md cursor-pointer hover:shadow-lg transition" 
                     src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama_lengkap'] ?? 'User') ?>&background=2563eb&color=fff" 
                     alt="Profile">
            </div>
        </div>
    </header>
    
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 md:p-8 custom-scrollbar">
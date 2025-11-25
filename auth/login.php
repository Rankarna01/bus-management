<?php
session_start();
include '../config/database.php';

// Jika sudah login, redirect sesuai role
if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] == 'admin') header("Location: ../admin/dashboard.php");
    elseif ($_SESSION['role'] == 'mandor') header("Location: ../mandor/riwayat.php");
    elseif ($_SESSION['role'] == 'direksi') header("Location: ../direksi/dashboard.php");
    exit;
}

// Proses Login
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    
    if (mysqli_num_rows($cek) === 1) {
        $row = mysqli_fetch_assoc($cek);
        if (password_verify($password, $row['password'])) {
            // Set Session
            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
            $_SESSION['role'] = $row['role'];

            // Redirect sesuai role
            if ($row['role'] == 'admin') header("Location: ../admin/dashboard.php");
            elseif ($row['role'] == 'mandor') header("Location: ../mandor/riwayat.php");
            elseif ($row['role'] == 'direksi') header("Location: ../direksi/dashboard.php");
            exit;
        }
    }
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Bus</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-white font-sans">

    <div class="min-h-screen flex">
        
        <div class="w-full md:w-1/2 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md">
                
                <div class="mb-10 text-left">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                            <i class="fa-solid fa-bus-simple text-xl"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">BUS<span class="text-blue-600">MAN</span></h1>
                    </div>
                    
                    <h2 class="text-3xl font-bold text-slate-900 mb-2">Selamat Datang Kembali! 👋</h2>
                    <p class="text-slate-500">Silakan masuk untuk mengelola operasional armada.</p>
                </div>

                <?php if (isset($error)) : ?>
                    <div class="flex items-center p-4 mb-6 text-sm text-red-800 border border-red-300 rounded-xl bg-red-50" role="alert">
                        <i class="fa-solid fa-circle-exclamation mr-2 text-lg"></i>
                        <div>
                            <span class="font-bold">Gagal Masuk!</span> Username atau Password salah.
                        </div>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-regular fa-user text-slate-400"></i>
                            </div>
                            <input type="text" name="username" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-colors duration-200 placeholder-slate-400" placeholder="Masukkan username anda" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock-open text-slate-400"></i>
                            </div>
                            <input type="password" name="password" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-colors duration-200 placeholder-slate-400" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" name="login" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-bold rounded-xl text-sm px-5 py-4 text-center transition-all duration-200 shadow-lg shadow-blue-500/30 hover:-translate-y-0.5">
                            Masuk ke Sistem <i class="fa-solid fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </form>

                <div class="mt-10 text-center">
                    <p class="text-xs text-slate-400">
                        &copy; 2025 Sistem Informasi Manajemen Bus<br>v1.0.0
                    </p>
                </div>
            </div>
        </div>

        <div class="hidden md:flex md:w-1/2 bg-slate-900 relative items-center justify-center overflow-hidden">
            <img src="https://images.unsplash.com/photo-1570125909232-eb263c188f7e?q=80&w=2071&auto=format&fit=crop" 
                 class="absolute inset-0 w-full h-full object-cover opacity-60" 
                 alt="Bus Background">
            
            <div class="absolute inset-0 bg-gradient-to-tr from-blue-900/80 to-slate-900/60 mix-blend-multiply"></div>

            <div class="relative z-10 p-12 text-white max-w-lg">
                <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center mb-6 border border-white/20">
                    <i class="fa-solid fa-route text-3xl text-blue-300"></i>
                </div>
                <h2 class="text-4xl font-bold mb-6 leading-tight">Kelola Armada & Perjalanan Lebih Efisien.</h2>
                <p class="text-lg text-blue-100 font-light leading-relaxed">
                    Sistem terintegrasi untuk memantau keberangkatan, data penumpang, dan kinerja mandor dalam satu dashboard yang handal.
                </p>
                
                <div class="flex gap-2 mt-8">
                    <div class="w-12 h-1.5 bg-blue-400 rounded-full"></div>
                    <div class="w-2 h-1.5 bg-white/30 rounded-full"></div>
                    <div class="w-2 h-1.5 bg-white/30 rounded-full"></div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
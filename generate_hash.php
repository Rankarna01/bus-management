<?php
$result_hash = "";
$password_input = "";

if (isset($_POST['generate'])) {
    $password_input = $_POST['password'];
    // Algoritma hashing default PHP (Bcrypt)
    $result_hash = password_hash($password_input, PASSWORD_DEFAULT);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Password Hash</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-800 text-gray-200 min-h-screen flex items-center justify-center p-4">

    <div class="bg-gray-900 p-8 rounded-xl shadow-2xl w-full max-w-lg border border-gray-700">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-blue-400"><i class="fa-solid fa-key"></i> Hash Generator</h2>
            <p class="text-gray-400 text-sm mt-2">Gunakan tool ini untuk membuat password akun baru secara manual di Database.</p>
        </div>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-bold mb-2">Password Biasa</label>
                <input type="text" name="password" value="<?= htmlspecialchars($password_input) ?>" 
                    class="w-full px-4 py-3 rounded bg-gray-800 border border-gray-600 focus:border-blue-500 focus:outline-none text-white" 
                    placeholder="Contoh: rahasia123" required autocomplete="off">
            </div>
            
            <button type="submit" name="generate" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded transition duration-200">
                <i class="fa-solid fa-bolt"></i> Generate Hash
            </button>
        </form>

        <?php if ($result_hash) : ?>
            <div class="mt-8 p-4 bg-gray-800 rounded border border-green-500 relative">
                <label class="block text-xs text-green-400 font-bold uppercase mb-1">Hasil Hash (Copy ini ke Database)</label>
                
                <code id="hashResult" class="block break-all text-sm text-white font-mono bg-black p-3 rounded">
                    <?= $result_hash ?>
                </code>

                <button onclick="copyToClipboard()" class="mt-3 w-full bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2 rounded transition">
                    <i class="fa-regular fa-copy"></i> Salin Hash
                </button>
            </div>
        <?php endif; ?>
        
        <div class="mt-8 text-center border-t border-gray-700 pt-4">
            <a href="index.php" class="text-gray-500 hover:text-white text-sm">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Sistem
            </a>
        </div>
    </div>

    <script>
        function copyToClipboard() {
            const hashText = document.getElementById("hashResult").innerText;
            navigator.clipboard.writeText(hashText).then(() => {
                alert("Hash berhasil disalin! Silakan paste di kolom 'password' pada phpMyAdmin.");
            });
        }
    </script>
</body>
</html>
</main>
    </div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. SPLASH SCREEN LOGIC
    window.addEventListener('load', function() {
        const loader = document.getElementById('loader');
        loader.classList.add('loader-hidden');
        
        // Hapus elemen dari DOM setelah transisi selesai agar bisa klik elemen di bawahnya
        loader.addEventListener('transitionend', function() {
            document.body.removeChild(loader);
        });
    });

    // 2. SWEETALERT LOGIC (Menerima Flash Message dari PHP session)
    <?php if (isset($_SESSION['success'])) : ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= $_SESSION['success']; ?>',
            timer: 3000,
            showConfirmButton: false
        });
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])) : ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '<?= $_SESSION['error']; ?>',
        });
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</script>

</body>
</html>
<div class="row g-4 justify-content-center py-5">
    <div class="col-12 col-md-8 col-lg-6 text-center">
        <div class="glass-card py-5 px-4">
            <h1 class="text-primary-gradient font-weight-700 mb-2" style="font-size: 6rem; letter-spacing: -2px; line-height: 1;">404</h1>
            <h4 class="text-white font-weight-600 mb-3">Halaman Tidak Ditemukan</h4>
            <p class="text-secondary fs-7 mb-4 mx-auto" style="max-width: 360px;">
                Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan ke alamat lain.
            </p>
            
            <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                <a href="/dashboard" class="btn btn-primary-gradient px-4 py-2.5 rounded-10 text-white font-weight-600">
                    <i class="fi fi-sr-home me-2 align-middle"></i> Kembali ke Dashboard
                </a>
            <?php else: ?>
                <a href="/login" class="btn btn-primary-gradient px-4 py-2.5 rounded-10 text-white font-weight-600">
                    <i class="fi fi-sr-lock me-2 align-middle"></i> Masuk ke Halaman Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

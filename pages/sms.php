<?php
// Initialize SMS session data if not exists
if (!isset($_SESSION['sms_inbox'])) {
    $_SESSION['sms_inbox'] = [
        [
            'id' => 1,
            'sender' => '+62 812-3456-7890',
            'message' => 'Halo, apakah server mobile sudah aktif? Saya ingin mengunduh beberapa berkas laporan bulanan.',
            'time' => '2026-07-09 10:15:30',
            'unread' => true
        ],
        [
            'id' => 2,
            'sender' => 'Telkomsel',
            'message' => 'INFO: Kuota internet OMG! Anda tersisa 500MB. Aktifkan paket OMG! lainnya di MyTelkomsel atau hubungi *363#.',
            'time' => '2026-07-09 08:30:12',
            'unread' => false
        ],
        [
            'id' => 3,
            'sender' => 'Google',
            'message' => 'G-682914 adalah kode verifikasi keamanan Akun Google Anda. Jangan berikan kode ini kepada siapapun.',
            'time' => '2026-07-08 17:42:05',
            'unread' => false
        ]
    ];
}

if (!isset($_SESSION['sms_sent'])) {
    $_SESSION['sms_sent'] = [
        [
            'id' => 101,
            'recipient' => '+62 812-3456-7890',
            'message' => 'Sudah aktif, silakan buka ip: 192.168.1.100:8080 untuk mengelola file melalui Tiny File Manager.',
            'time' => '2026-07-09 10:18:45'
        ]
    ];
}

// Handle SMS submission
$success_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_sms'])) {
    $recipient = isset($_POST['recipient']) ? trim($_POST['recipient']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    if (!empty($recipient) && !empty($message)) {
        // Can support sending to multiple numbers by splitting commas
        $numbers = explode(',', $recipient);
        $sent_count = 0;
        
        foreach ($numbers as $num) {
            $num = trim($num);
            if (!empty($num)) {
                $new_id = 100 + count($_SESSION['sms_sent']) + 1;
                $_SESSION['sms_sent'][] = [
                    'id' => $new_id,
                    'recipient' => $num,
                    'message' => $message,
                    'time' => date('Y-m-d H:i:s')
                ];
                $sent_count++;
            }
        }
        
        $success_msg = "SMS berhasil dikirim ke {$sent_count} penerima!";
    }
}

// Merge and sort all SMS messages by time descending
$all_sms = [];
if (isset($_SESSION['sms_inbox'])) {
    foreach ($_SESSION['sms_inbox'] as $msg) {
        $msg['type'] = 'inbox';
        $all_sms[] = $msg;
    }
}
if (isset($_SESSION['sms_sent'])) {
    foreach ($_SESSION['sms_sent'] as $msg) {
        $msg['type'] = 'sent';
        $msg['sender'] = $msg['recipient']; // normalize for display
        $all_sms[] = $msg;
    }
}

usort($all_sms, function($a, $b) {
    return strcmp($b['time'], $a['time']);
});
?>

<?php if (!empty($success_msg)): ?>
    <div class="alert alert-success bg-success bg-opacity-20 border-success border-opacity-30 text-success rounded-10 py-2.5 px-3 fs-7 mb-4 animated-fade-in" role="alert">
        <i class="fi fi-sr-shield-check me-2 align-middle"></i> <?= $success_msg ?>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Send SMS Card -->
    <div class="col-12 col-lg-5">
        <div class="glass-card">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="fi fi-sr-envelope text-primary fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Kirim SMS</h5>
            </div>
            
            <form action="/sms" method="POST">
                <input type="hidden" name="send_sms" value="1">
                
                <div class="mb-3">
                    <label for="recipient" class="form-label text-secondary fs-7 ms-1">Nomor Penerima</label>
                    <input type="text" name="recipient" id="recipient" class="form-control form-glass" placeholder="Contoh: 08123456789,08987654321" required>
                    <!-- Added subtitle for multiple recipients -->
                    <div class="fs-8 text-secondary mt-1.5 ms-1">
                        Jika ingin mengirim ke beberapa nomor sekaligus gunakan koma tanpa spasi.
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="message" class="form-label text-secondary fs-7 ms-1">Isi Pesan SMS</label>
                    <textarea name="message" id="message" rows="5" class="form-control form-glass" placeholder="Tulis pesan Anda di sini..." maxlength="160" required></textarea>
                    <div class="d-flex justify-content-between fs-8 text-secondary mt-1.5">
                        <span id="charCount">0 / 160 Karakter</span>
                        <span>1 SMS</span>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary-gradient w-100 py-2.5 rounded-10 text-white font-weight-600">
                    <i class="fi fi-sr-play me-2 align-middle" style="transform: rotate(-45deg);"></i> Kirim Pesan
                </button>
            </form>
        </div>
    </div>

    <!-- Unified SMS Feed List -->
    <div class="col-12 col-lg-7">
        <div class="glass-card h-100">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="fi fi-sr-envelope text-success fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Daftar Semua Pesan</h5>
            </div>
            
            <div class="sms-list" style="max-height: 480px; overflow-y: auto; padding-right: 5px;">
                <?php if (empty($all_sms)): ?>
                    <div class="text-center py-5">
                        <i class="fi fi-sr-envelope text-secondary fs-1 mb-2 opacity-50"></i>
                        <p class="text-secondary mb-0">Tidak ada pesan SMS.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($all_sms as $msg): ?>
                        <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 mb-3 animated-fade-in">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge <?= $msg['type'] === 'inbox' ? 'bg-success bg-opacity-10 border border-success border-opacity-25 text-success' : 'bg-primary bg-opacity-10 border border-primary border-opacity-25 text-white' ?> px-2 py-0.5 fs-9" style="font-size:0.65rem;">
                                        <?= $msg['type'] === 'inbox' ? 'MASUK' : 'TERKIRIM' ?>
                                    </span>
                                    <span class="text-white font-weight-600 fs-7"><?= htmlspecialchars($msg['sender']) ?></span>
                                </div>
                                <span class="fs-9 text-secondary" style="font-size:0.7rem;"><?= $msg['time'] ?></span>
                            </div>
                            <p class="text-secondary fs-8 mb-0" style="white-space: pre-wrap; line-height: 1.4;"><?= htmlspecialchars($msg['message']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Character counter for SMS text area
    const smsMessage = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    
    if (smsMessage && charCount) {
        smsMessage.addEventListener('input', function() {
            const count = this.value.length;
            const smsPages = Math.ceil(count / 160) || 1;
            charCount.textContent = `${count} / 160 Karakter`;
            charCount.nextElementSibling.textContent = `${smsPages} SMS`;
        });
    }
</script>

<style>
    .fs-9 { font-size: 0.725rem; }
</style>

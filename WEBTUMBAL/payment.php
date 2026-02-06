<?php
session_start();
include 'includes/config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$user = getUserInfo($user_id);
$transaction_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = "";
$success = "";

if ($transaction_id <= 0) {
    header("Location: shop.php");
    exit();
}

// Get transaction details
$transaction_query = "SELECT t.*, p.title, p.image_url, p.seller_id FROM transactions t 
                      JOIN products p ON t.product_id = p.id 
                      WHERE t.id = ? AND t.buyer_id = ?";
$stmt = $conn->prepare($transaction_query);
if (!$stmt) {
    die("Prepare error: " . $conn->error);
}

$stmt->bind_param("ii", $transaction_id, $user_id);
$stmt->execute();
$transaction_result = $stmt->get_result();

if ($transaction_result->num_rows === 0) {
    header("Location: shop.php");
    exit();
}

$transaction = $transaction_result->fetch_assoc();

// Prepare payment method details (Moved to top for cleaner HTML)
$nominal = number_format($transaction['total_price'], 0, ',', '.');
$qris_string = "00020126360014com.midtrans.qris0215" . bin2hex(substr(md5($transaction_id . time()), 0, 10)) . "5204481153033605402" . str_pad($transaction['total_price'], 12, '0', STR_PAD_LEFT) . "5802ID5913MarketHub6009Surabaya62410512" . $transaction_id . "63041D3F";

$method_details = [
    'credit_card' => [
        'name' => '💳 Kartu Kredit',
        'instructions' => '<strong>Pembayaran dengan Kartu Kredit:</strong><br>
                        <div class="payment-box cc-box">
                        <p>Gunakan sistem payment gateway kami untuk keamanan maksimal.</p>
                        <p class="nominal-text"><strong>Nominal:</strong> Rp ' . $nominal . '</p>
                        <p class="warning-text"><strong>⚠️ Transaksi 3D Secure tersedia untuk keamanan ekstra</strong></p>
                        </div>'
    ],
    'qris' => [
        'name' => '📱 QRIS (Indonesian Standard)',
        'instructions' => '<strong>Pembayaran via QRIS:</strong><br>
                        <div class="payment-box qris-box">
                        <p><strong>Instruksi:</strong> Scan QR Code di bawah dengan aplikasi pembayaran pilihan Anda</p>
                        <div class="qr-container">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($qris_string) . '" alt="QRIS QR Code">
                        <p class="qr-note"><em>Aplikasi yang didukung: GoPay, OVO, Dana, LinkAja, dan aplikasi bank lainnya</em></p>
                        </div>
                        <p class="nominal-text"><strong>Nominal:</strong> Rp ' . $nominal . '</p>
                        <p class="success-text"><strong>✓ Pembayaran instant & aman</strong></p>
                        </div>'
    ],
    'cash_cod' => [
        'name' => '💵 Bayar di Tempat (COD)',
        'instructions' => '<strong>Pembayaran Saat Barang Tiba:</strong><br>
                        <div class="info-box">
                        <p>✓ Anda akan membayar langsung saat barang tiba di alamat pengiriman</p>
                        <p class="nominal-text"><strong>Nominal:</strong> Rp ' . $nominal . '</p>
                        <p><strong>⚠️ Pastikan Anda siap membayar saat pengiriman</strong></p>
                        <p class="qr-note"><em>Jika tidak membayar, pesanan bisa dibatalkan oleh kurir</em></p>
                        </div>'
    ]
];

// Handle payment confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_payment'])) {
    $payment_proof = sanitize($_POST['payment_proof'] ?? '');
    
    if (empty($payment_proof)) {
        $error = "⚠️ Bukti pembayaran harus diisi!";
    } else {
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Update payment status
            $update_query = "UPDATE transactions SET payment_status = 'paid', status = 'paid', payment_proof = ?, confirmed_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            
            if (!$stmt) {
                throw new Exception("Prepare statement gagal: " . $conn->error);
            }
            
            $stmt->bind_param("si", $payment_proof, $transaction_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Update payment gagal: " . $stmt->error);
            }
            
            // Reduce product stock
            $stock_query = "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?";
            $stock_stmt = $conn->prepare($stock_query);
            
            if (!$stock_stmt) {
                throw new Exception("Prepare stock statement gagal: " . $conn->error);
            }
            
            $qty = (int)$transaction['quantity'];
            $product_id = (int)$transaction['product_id'];
            
            $stock_stmt->bind_param("iii", $qty, $product_id, $qty);
            
            if (!$stock_stmt->execute() || $stock_stmt->affected_rows <= 0) {
                throw new Exception("Stok tidak cukup atau produk tidak ditemukan!");
            }
            
            // Commit transaction
            $conn->commit();
            $success = "✓ Pembayaran berhasil dikonfirmasi! Pesanan Anda sedang diproses.";
            
            // Refresh transaction data untuk menampilkan status terbaru
            $refresh_stmt = $conn->prepare("SELECT t.*, p.title, p.image_url, p.seller_id FROM transactions t JOIN products p ON t.product_id = p.id WHERE t.id = ?");
            $refresh_stmt->bind_param("i", $transaction_id);
            $refresh_stmt->execute();
            $transaction = $refresh_stmt->get_result()->fetch_assoc();
            
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            $error = "❌ Gagal memproses pembayaran: " . $e->getMessage();
        }
    }
}

$payment_methods = getPaymentMethods();

$page_title = "Pembayaran - MarketHub";
$custom_css = "<style>
        .payment-instruction { background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid var(--secondary-color); }
        .payment-method-info h3 { color: var(--primary-color); margin-bottom: 15px; font-size: 16px; }
        .payment-method-info p { font-size: 13px; line-height: 1.8; color: var(--dark-color); margin: 8px 0; }
        .payment-method-info ul { margin-left: 20px; font-size: 13px; margin-bottom: 10px; }
        .payment-method-info ul li { margin: 6px 0; color: var(--dark-color); }
        .success-message { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .error-message { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .info-box { background: #fff3cd; border-left: 4px solid #ff9800; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info-box strong { color: #ff6b6b; }
        .payment-box { margin-top: 12px; padding: 12px; border-radius: 4px; }
        .payment-box p { margin: 6px 0; }
        .cc-box { background: #f3e5f5; border-left: 3px solid #7b1fa2; }
        .qris-box { background: #e8f5e9; border-left: 3px solid #27ae60; }
        .nominal-text { color: var(--primary-color); font-weight: bold; }
        .warning-text { color: #d9534f; }
        .success-text { color: #27ae60; }
        .qr-container { text-align: center; margin: 15px 0; }
        .qr-container img { border: 2px solid #27ae60; border-radius: 5px; max-width: 250px; }
        .qr-note { margin-top: 10px; font-size: 12px; color: #666; }
</style>";

include 'includes/header.php';
?>

    <div class="container">
        <div class="payment-section">
            <h1>💳 Pembayaran Pesanan #<?php echo $transaction_id; ?></h1>

            <div class="payment-container">
                <!-- Ringkasan Pesanan -->
                <div class="payment-summary">
                    <h2>📦 Ringkasan Pesanan</h2>
                    <div class="order-item">
                        <img src="<?php echo htmlspecialchars($transaction['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($transaction['title']); ?>">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($transaction['title']); ?></h3>
                            <p><strong>Jumlah:</strong> <?php echo $transaction['quantity']; ?> pcs</p>
                            <p><strong>Total:</strong> <span style="color: var(--primary-color); font-weight: bold;">Rp <?php echo number_format($transaction['total_price'], 0, ',', '.'); ?></span></p>
                        </div>
                    </div>

                    <div class="order-details">
                        <p><strong>🔹 Status Pesanan:</strong> 
                            <span class="status-badge" style="background: #fff3cd; color: #856404; padding: 5px 10px; border-radius: 3px;">
                                <?php 
                                    $status_map = array(
                                        'pending' => '⏳ Menunggu Pembayaran',
                                        'paid' => '✓ Dibayar',
                                        'shipped' => '📦 Dikirim',
                                        'completed' => '✓ Selesai',
                                        'cancelled' => '❌ Dibatalkan'
                                    );
                                    echo $status_map[$transaction['status']] ?? $transaction['status'];
                                ?>
                            </span>
                        </p>
                        <p style="margin-top: 10px;"><strong>🔹 Metode Pembayaran:</strong> 
                            <?php 
                            $method_name = '';
                            $payment_method = $transaction['payment_method'] ?? '';
                            foreach ($payment_methods as $method) {
                                if ($method['id'] === $payment_method) {
                                    $method_name = $method['icon'] . ' ' . $method['name'];
                                    break;
                                }
                            }
                            echo htmlspecialchars($method_name ?: '❓ Tidak diketahui');
                            ?>
                        </p>
                        <p style="margin-top: 10px; color: #666; font-size: 12px;">
                            <strong>ID Transaksi:</strong> <?php echo $transaction_id; ?>
                        </p>
                    </div>
                </div>

                <!-- Form Pembayaran -->
                <div class="payment-form-section">
                    <h2>💰 Instruksi Pembayaran</h2>
                    
                    <?php if ($error): ?>
                        <div class="error-message"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="success-message">
                            <h3><?php echo $success; ?></h3>
                            <p style="margin-top: 10px;">Anda akan diarahkan ke dashboard dalam beberapa detik...</p>
                        </div>
                        <script>
                            setTimeout(function() {
                                window.location.href = 'dashboard.php';
                            }, 3000);
                        </script>
                    <?php endif; ?>

                    <?php if ($transaction['payment_status'] !== 'paid' && !$success): ?>
                        <div class="payment-instruction">
                            <div class="payment-method-info">
                                <?php 
                                $payment_method_key = $transaction['payment_method'] ?? '';
                                $method_info = isset($method_details[$payment_method_key]) ? $method_details[$payment_method_key] : null;
                                if ($method_info):
                                ?>
                                    <h3><?php echo htmlspecialchars($method_info['name']); ?></h3>
                                    <p><?php echo $method_info['instructions']; ?></p>
                                <?php else: ?>
                                    <p class="error-message">❌ Metode pembayaran tidak ditemukan!</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <form method="POST" class="form" onsubmit="return validatePaymentProof()">
                            <div class="form-group">
                                <label for="payment_proof"><strong>📋 Bukti Pembayaran <span style="color: red;">*</span></strong></label>
                                <div style="background: #f9f9f9; padding: 12px; border-radius: 5px; margin-bottom: 10px;">
                                    <p style="margin: 0; font-size: 13px; color: #666;"><strong>Sertakan informasi berikut:</strong></p>
                                    <ul style="margin: 8px 0 0 20px; font-size: 12px; color: #666;">
                                        <li>✓ Nomor referensi / kode transaksi</li>
                                        <li>✓ Waktu pembayaran</li>
                                        <li>✓ Nominal yang dibayarkan</li>
                                        <li>✓ Nama pengirim (jika berbeda)</li>
                                    </ul>
                                </div>
                                <textarea id="payment_proof" name="payment_proof" rows="5" required 
                                        placeholder="Contoh: Transfer BCA, jam 10:30 pagi, nominal Rp 500.000, dari rekening atas nama Budi Santoso, ref: ABC123DEF456"
                                        minlength="20" maxlength="500" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: Arial; resize: vertical;"></textarea>
                                <small style="display: block; margin-top: 8px; color: #27ae60;"><strong>💡 Tips:</strong> Semakin detail bukti pembayaran Anda, semakin cepat pembayaran dikonfirmasi</small>
                            </div>

                            <button type="submit" name="confirm_payment" class="btn-primary btn-large" style="width: 100%; padding: 12px; font-size: 16px; cursor: pointer;">
                                ✓ Konfirmasi Pembayaran
                            </button>
                        </form>
                        
                        <script>
                        function validatePaymentProof() {
                            var proof = document.getElementById('payment_proof').value.trim();
                            if (proof.length < 20) {
                                alert('❌ Bukti pembayaran terlalu singkat. Silakan sertakan informasi yang lebih lengkap.');
                                return false;
                            }
                            if (proof.length > 500) {
                                alert('❌ Bukti pembayaran terlalu panjang (max 500 karakter).');
                                return false;
                            }
                            return confirm('✓ Yakin data bukti pembayaran sudah benar?');
                        }
                        </script>
                    <?php else: ?>
                        <div class="success-message" style="border: 2px solid #28a745; background: #d4edda;">
                            <h3>✓ Pembayaran Berhasil Dikonfirmasi!</h3>
                            <p style="margin: 10px 0 0 0;">Pesanan Anda sedang diproses. Stok barang akan dikurangi dan penjual akan segera mengkonfirmasi pengiriman.</p>
                        </div>
                        
                        <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-top: 15px; border-left: 4px solid #27ae60;">
                            <h4 style="margin-top: 0; color: #27ae60;">📋 Bukti Pembayaran Tersimpan:</h4>
                            <div style="background: white; padding: 12px; border-radius: 5px; border: 1px solid #ddd; font-family: monospace; line-height: 1.6; color: #333; word-wrap: break-word;">
                                <?php echo nl2br(htmlspecialchars($transaction['payment_proof'])); ?>
                            </div>
                            <p style="margin: 10px 0 0 0; font-size: 12px; color: #666;">
                                <strong>⏰ Dikonfirmasi pada:</strong> <?php echo date('d M Y H:i:s', strtotime($transaction['confirmed_at'])); ?>
                            </p>
                        </div>
                        
                        <div style="margin-top: 15px;">
                            <a href="dashboard.php" class="btn-primary" style="display: inline-block; padding: 10px 20px; background: var(--primary-color); color: white; text-decoration: none; border-radius: 5px;">← Kembali ke Dashboard</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

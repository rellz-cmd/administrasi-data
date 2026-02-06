<?php
session_start();
include 'includes/config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$user = getUserInfo($user_id);
$error = "";
$success = "";

// PERBAIKAN DATABASE OTOMATIS: Cek dan buat kolom jika belum ada
$columns_needed = [
    'credit_score' => 'INT DEFAULT 100',
    'fraud_count' => 'INT DEFAULT 0',
    'is_banned' => 'TINYINT(1) DEFAULT 0'
];

foreach ($columns_needed as $col => $def) {
    $check = $conn->query("SHOW COLUMNS FROM users LIKE '$col'");
    if ($check && $check->num_rows == 0) {
        $conn->query("ALTER TABLE users ADD COLUMN $col $def");
    }
}

// Handle fraud report submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['report_fraud'])) {
    $seller_username = sanitize($_POST['seller_username'] ?? '');
    $reason = sanitize($_POST['reason'] ?? '');
    
    // Validation
    if (empty($seller_username)) {
        $error = "❌ Username Penjual harus diisi!";
    } elseif (empty($reason) || strlen($reason) < 10) {
        $error = "❌ Alasan pelaporan minimal 10 karakter!";
    } else {
        // Check if seller exists
        $seller_check = $conn->prepare("SELECT id, is_banned FROM users WHERE username = ?");
        $seller_check->bind_param("s", $seller_username);
        $seller_check->execute();
        $seller_result = $seller_check->get_result();
        
        if ($seller_result->num_rows === 0) {
            $error = "❌ Username Penjual tidak ditemukan!";
        } else {
            $seller_data = $seller_result->fetch_assoc();
            $seller_id = $seller_data['id'];
            
            if ($seller_id === $user_id) {
                $error = "❌ Anda tidak bisa melaporkan akun sendiri!";
            } elseif ($seller_data['is_banned']) {
                $error = "⚠️ Penjual ini sudah di-ban!";
            } else {
                // Check if already reported by this buyer today
                $check_duplicate = $conn->prepare("SELECT id FROM fraud_reports WHERE seller_id = ? AND reporter_id = ? AND DATE(reported_date) = CURDATE()");
                $check_duplicate->bind_param("ii", $seller_id, $user_id);
                $check_duplicate->execute();
                
                if ($check_duplicate->get_result()->num_rows > 0) {
                    $error = "⚠️ Anda sudah melaporkan penjual ini hari ini. Laporan ganda akan ditolak.";
                } else {
                    // Insert fraud report
                    $conn->begin_transaction();
                    
                    try {
                        // Insert into fraud_reports table
                        $insert_report = $conn->prepare("INSERT INTO fraud_reports (seller_id, reporter_id, reason, reported_date) VALUES (?, ?, ?, NOW())");
                        $insert_report->bind_param("iis", $seller_id, $user_id, $reason);
                        
                        if (!$insert_report->execute()) {
                            throw new Exception("Gagal menyimpan laporan: " . $insert_report->error);
                        }
                        
                        // Update fraud_count
                        $update_fraud = $conn->prepare("UPDATE users SET fraud_count = fraud_count + 1 WHERE id = ?");
                        $update_fraud->bind_param("i", $seller_id);
                        
                        if (!$update_fraud->execute()) {
                            throw new Exception("Gagal update fraud count: " . $update_fraud->error);
                        }
                        
                        // Update credit_score (turun 20 poin per laporan)
                        $update_score = $conn->prepare("UPDATE users SET credit_score = GREATEST(0, credit_score - 20) WHERE id = ?");
                        $update_score->bind_param("i", $seller_id);
                        
                        if (!$update_score->execute()) {
                            throw new Exception("Gagal update credit score: " . $update_score->error);
                        }
                        
                        // Check if fraud_count >= 3 OR credit_score <= 40 (Critical), then ban the seller
                        $check_status = $conn->prepare("SELECT fraud_count, credit_score FROM users WHERE id = ?");
                        $check_status->bind_param("i", $seller_id);
                        if ($check_status->execute()) {
                            $res = $check_status->get_result();
                            if ($res && $res->num_rows > 0) {
                                $status_result = $res->fetch_assoc();
                                
                                if ($status_result['fraud_count'] >= 3 || $status_result['credit_score'] <= 40) {
                                    $ban_seller = $conn->prepare("UPDATE users SET is_banned = 1 WHERE id = ?");
                                    $ban_seller->bind_param("i", $seller_id);
                                    
                                    if (!$ban_seller->execute()) {
                                        throw new Exception("Gagal ban seller: " . $ban_seller->error);
                                    }
                                }
                            }
                        }
                        
                        $conn->commit();
                        $success = "✓ Laporan fraud berhasil dikirim! Terima kasih atas kontribusi Anda dalam menjaga keamanan marketplace.";
                        
                    } catch (Exception $e) {
                        $conn->rollback();
                        $error = "❌ Gagal memproses laporan: " . $e->getMessage();
                    }
                }
            }
        }
    }
}

// Get all sellers with their fraud status
$sellers_query = "SELECT id, username, full_name, credit_score, fraud_count, is_banned 
                  FROM users 
                  WHERE id != " . (int)$user_id . "
                  ORDER BY fraud_count DESC, credit_score ASC";

try {
    $sellers_result = $conn->query($sellers_query);
} catch (Exception $e) {
    $sellers_result = false;
    $error = "Sedang memperbarui database... Silakan refresh halaman ini sekali lagi.";
}

if (!$sellers_result && empty($error)) {
    $error = "Error query: " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapor Fraud - MarketHub</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .fraud-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .fraud-section h2 {
            color: var(--dark-color);
            margin-bottom: 20px;
        }
        
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-left: 4px solid #ff9800;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #856404;
        }
        
        .fraud-form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .transactions-table thead {
            background: var(--light-color);
        }
        
        .transactions-table th,
        .transactions-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .transactions-table tbody tr:hover {
            background: #f5f5f5;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-banned {
            background: #e2e3e5;
            color: #383d41;
        }
        
        .credit-bar {
            background: #ddd;
            height: 8px;
            border-radius: 4px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .fraud-info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .fraud-info-box ul {
            margin: 10px 0 0 20px;
            padding: 0;
        }
        
        .fraud-info-box li {
            margin: 5px 0;
            font-size: 14px;
            color: #1565c0;
        }
        
        .success-message,
        .error-message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .error-message {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <h2>🛍️ MarketHub</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="shop.php" class="nav-link">Belanja</a></li>
                <li><a href="my_products.php" class="nav-link">Produk Saya</a></li>
                <li><a href="upload_product.php" class="btn-upload">+ Jual</a></li>
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li>
                    <span class="user-info">👤 <?php echo htmlspecialchars($user['full_name']); ?></span>
                </li>
                <li><a href="logout.php" class="nav-link logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <!-- Page Title -->
        <div class="fraud-section">
            <h2>⚠️ Lapor Penjual Curang</h2>
            <p style="color: #666; margin: 0;">Bantu kami menjaga keamanan dan kepercayaan marketplace dengan melaporkan penjual yang mencurangi atau melakukan tindakan tidak etis.</p>
        </div>

        <!-- Fraud Report Form -->
        <div class="fraud-section">
            <h2>📋 Form Pelaporan Fraud</h2>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="warning-box">
                <strong>⚠️ Perhatian:</strong> Jangan membuat laporan palsu. Pelaporan yang tidak akurat dapat mempengaruhi reputasi penjual. Pastikan Anda memiliki bukti nyata sebelum melaporkan.
            </div>

            <form method="POST" class="form fraud-form" onsubmit="return validateFraudForm()">
                <div class="form-group">
                    <label for="seller_username"><strong>🔹 Username Penjual yang Dilaporkan *</strong></label>
                    <input type="text" id="seller_username" name="seller_username" required
                           value="<?php echo isset($_GET['username']) ? htmlspecialchars($_GET['username']) : ''; ?>"
                           placeholder="Contoh: seller123"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <small style="display: block; margin-top: 5px; color: #666;">Masukkan Username penjual yang akan Anda laporkan</small>
                </div>

                <div class="form-group">
                    <label for="reason"><strong>🔹 Alasan Pelaporan *</strong></label>
                    <textarea id="reason" name="reason" rows="6" required minlength="10" maxlength="500"
                              placeholder="Jelaskan dengan detail kecurangan atau masalah yang terjadi. Sertakan bukti atau informasi yang mendukung laporan Anda."
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: Arial; resize: vertical;"></textarea>
                    <small style="display: block; margin-top: 5px; color: #666;">Minimal 10 karakter, maksimal 500 karakter</small>
                </div>

                <button type="submit" name="report_fraud" class="btn-primary" style="width: 100%; padding: 12px; font-size: 16px; background: #e74c3c; cursor: pointer;">
                    🚨 Kirim Laporan Fraud
                </button>
            </form>

            <div class="fraud-info-box">
                <strong>📌 Contoh Kecurangan yang Bisa Dilaporkan:</strong>
                <ul>
                    <li>❌ Barang tidak sesuai deskripsi</li>
                    <li>❌ Barang rusak atau cacat saat diterima</li>
                    <li>❌ Penjual tidak responsif/tidak merespon</li>
                    <li>❌ Pengiriman tidak sesuai janji</li>
                    <li>❌ Melakukan penipuan atau scam</li>
                    <li>❌ Menggunakan foto produk orang lain</li>
                    <li>❌ Tidak mengembalikan uang sesuai kebijakan</li>
                </ul>
            </div>
        </div>

        <!-- Credit Score & Ban Status -->
        <div class="fraud-section">
            <h2>📊 Status Kredit Penjual</h2>
            
            <div class="fraud-info-box">
                <strong>ℹ️ Sistem Kredit Score:</strong>
                <ul>
                    <li>✓ <strong>Credit Score Awal:</strong> 100 poin per penjual</li>
                    <li>✓ <strong>Berkurang:</strong> 20 poin per laporan fraud yang valid</li>
                    <li>✓ <strong>Status Hijau:</strong> 81-100 poin (Penjual Terpercaya)</li>
                    <li>✓ <strong>Status Kuning:</strong> 60-80 poin (Hati-hati)</li>
                    <li>✓ <strong>Status Merah:</strong> <60 poin (Riwayat Buruk)</li>
                    <li>✓ <strong>Auto-Ban:</strong> Jika fraud count mencapai 3 laporan</li>
                </ul>
            </div>

            <?php if ($sellers_result && $sellers_result->num_rows > 0): ?>
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>ID Penjual</th>
                            <th>Nama Penjual</th>
                            <th>Credit Score</th>
                            <th>Fraud Count</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($seller = $sellers_result->fetch_assoc()): ?>
                            <tr>
                                <td><strong>#<?php echo $seller['id']; ?></strong></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($seller['full_name']); ?></strong>
                                    <br>
                                    <small style="color: #999;">@<?php echo htmlspecialchars($seller['username']); ?></small>
                                </td>
                                <td>
                                    <strong><?php echo $seller['credit_score']; ?>/100</strong>
                                    <div class="credit-bar">
                                        <div style="background: <?php 
                                            if ($seller['credit_score'] >= 80) echo '#27ae60';
                                            elseif ($seller['credit_score'] >= 60) echo '#f39c12';
                                            else echo '#e74c3c';
                                        ?>; width: <?php echo $seller['credit_score']; ?>%; height: 100%; border-radius: 4px;"></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge <?php 
                                        if ($seller['fraud_count'] >= 3) echo 'status-danger';
                                        elseif ($seller['fraud_count'] >= 2) echo 'status-warning';
                                        else echo 'status-active';
                                    ?>">
                                        <?php echo $seller['fraud_count']; ?>/3
                                    </span>
                                </td>
                                <td>
                                    <?php if ($seller['is_banned']): ?>
                                        <span class="status-badge status-banned">🚫 DI-BAN</span>
                                    <?php elseif ($seller['fraud_count'] >= 2): ?>
                                        <span class="status-badge status-warning">⚠️ HATI-HATI</span>
                                    <?php elseif ($seller['credit_score'] < 60): ?>
                                        <span class="status-badge status-danger">🔴 BURUK</span>
                                    <?php else: ?>
                                        <span class="status-badge status-active">✓ BAIK</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 20px;">
                    <strong>✓ Tidak ada penjual dengan riwayat fraud</strong>
                </p>
            <?php endif; ?>
        </div>

        <!-- Warning & Tips -->
        <div class="fraud-section">
            <h2>⚠️ Panduan Pelaporan</h2>
            <div style="background: #f0f7ff; padding: 15px; border-radius: 5px;">
                <p><strong>🔹 Apa yang Harus Anda Lakukan Sebelum Melaporkan:</strong></p>
                <ol style="margin: 10px 0 0 20px;">
                    <li>📸 Ambil screenshot atau dokumentasi bukti kecurangan</li>
                    <li>💬 Coba komunikasi dengan penjual terlebih dahulu</li>
                    <li>📝 Catat tanggal, waktu, dan detail masalah</li>
                    <li>🔍 Pastikan Anda memiliki bukti konkret</li>
                    <li>✅ Baru laporkan jika masalah tidak terselesaikan</li>
                </ol>
                
                <p style="margin-top: 15px;"><strong>🔹 Konsekuensi Pelaporan Palsu:</strong></p>
                <ul style="margin: 10px 0 0 20px;">
                    <li>⛔ Akun Anda bisa dibekukan</li>
                    <li>⛔ Tidak bisa melaporkan fraud selama waktu tertentu</li>
                    <li>⛔ Kredibilitas akun Anda berkurang</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 MarketHub - Platform Jual Beli Online Terpercaya</p>
    </footer>

    <script>
    function validateFraudForm() {
        var seller_username = document.getElementById('seller_username').value.trim();
        var reason = document.getElementById('reason').value.trim();
        
        if (!seller_username) {
            alert('❌ Masukkan Username penjual!');
            return false;
        }
        
        if (reason.length < 10) {
            alert('❌ Alasan pelaporan minimal 10 karakter!');
            return false;
        }
        
        if (reason.length > 500) {
            alert('❌ Alasan pelaporan maksimal 500 karakter!');
            return false;
        }
        
        return confirm('✓ Yakin ingin melaporkan penjual ini? Pastikan informasi yang Anda berikan akurat dan dapat dipertanggungjawabkan.');
    }
    </script>
</body>
</html>

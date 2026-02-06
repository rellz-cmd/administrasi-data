<?php
session_start();
include 'includes/config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$user = getUserInfo($user_id);

// Get user statistics
$stats_query = "SELECT 
                    COUNT(*) as total_products,
                    SUM(CASE WHEN p.status = 'sold' THEN 1 ELSE 0 END) as total_sold,
                    SUM(CASE WHEN p.status = 'active' THEN 1 ELSE 0 END) as active_products,
                    COALESCE(SUM(CASE WHEN t.id IS NOT NULL THEN t.total_price ELSE 0 END), 0) as total_revenue
                FROM products p
                LEFT JOIN transactions t ON p.id = t.product_id AND t.status = 'completed'
                WHERE p.seller_id = ?";

$stmt = $conn->prepare($stats_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Get recent transactions
$transactions_query = "SELECT t.*, p.title, p.image_url, u.username, u.full_name
                       FROM transactions t
                       JOIN products p ON t.product_id = p.id
                       JOIN users u ON t.buyer_id = u.id
                       WHERE t.seller_id = ?
                       ORDER BY t.created_at DESC
                       LIMIT 10";

$stmt = $conn->prepare($transactions_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$transactions_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MarketHub</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
                <li><a href="dashboard.php" class="nav-link active">Dashboard</a></li>
                <li><a href="report_fraud.php" class="nav-link" style="color: #e74c3c; font-weight: 500;">⚠️ Lapor Fraud</a></li>
                <li>
                    <span class="user-info">👤 <?php echo htmlspecialchars($user['full_name']); ?></span>
                </li>
                <li><a href="logout.php" class="nav-link logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <!-- User Profile Section -->
        <div class="dashboard-header">
            <h1>Selamat Datang, <?php echo htmlspecialchars($user['full_name']); ?>!</h1>
            <a href="edit_profile.php" class="btn-primary">Edit Profil</a>
        </div>

        <!-- Credit Score Section -->
        <div class="credit-score-section">
            <h2>📊 Credit Score Anda</h2>
            <div class="credit-score-display">
                <div class="score-card">
                    <h3>Credit Score</h3>
                    <div class="score-number" style="color: <?php 
                        $cs = isset($user['credit_score']) ? $user['credit_score'] : 100;
                        if ($cs >= 80) echo '#27ae60';
                        elseif ($cs >= 60) echo '#f39c12';
                        else echo '#e74c3c';
                    ?>;">
                        <?php echo (isset($user['credit_score']) ? $user['credit_score'] : 100); ?>/100
                    </div>
                    <div style="background: #ddd; height: 12px; border-radius: 6px; margin-top: 10px;">
                        <div style="background: <?php 
                            $cs = isset($user['credit_score']) ? $user['credit_score'] : 100;
                            if ($cs >= 80) echo '#27ae60';
                            elseif ($cs >= 60) echo '#f39c12';
                            else echo '#e74c3c';
                        ?>; width: <?php echo (isset($user['credit_score']) ? $user['credit_score'] : 100); ?>%; height: 100%; border-radius: 6px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Produk</h3>
                <p class="stat-number"><?php echo $stats['total_products']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Produk Aktif</h3>
                <p class="stat-number"><?php echo $stats['active_products']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Produk Terjual</h3>
                <p class="stat-number"><?php echo $stats['total_sold']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Pendapatan</h3>
                <p class="stat-number">Rp <?php echo number_format($stats['total_revenue'], 0, ',', '.'); ?></p>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="section">
            <h2>Transaksi Terbaru</h2>
            <?php if ($transactions_result->num_rows > 0): ?>
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Pembeli</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($transaction = $transactions_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="transaction-product">
                                        <img src="<?php echo htmlspecialchars($transaction['image_url']); ?>" alt="">
                                        <span><?php echo htmlspecialchars(substr($transaction['title'], 0, 30)); ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($transaction['full_name']); ?></td>
                                <td>Rp <?php echo number_format($transaction['total_price'], 0, ',', '.'); ?></td>
                                <td><span class="status-badge status-<?php echo $transaction['status']; ?>"><?php echo ucfirst($transaction['status']); ?></span></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($transaction['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Belum ada transaksi</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 MarketHub - Platform Jual Beli Online Terpercaya</p>
    </footer>
</body>
</html> 
<?php
// Helper untuk class active
$cur_page = basename($_SERVER['PHP_SELF']);
function isAct($p) { global $cur_page; return $cur_page == $p ? 'active' : ''; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'MarketHub'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if(isset($custom_css)) echo $custom_css; ?>
</head>
<body>
    <?php if(isset($_SESSION['user_id'])): ?>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <h2>🛍️ MarketHub</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="shop.php" class="nav-link <?php echo isAct('shop.php'); ?>">Belanja</a></li>
                <li><a href="my_products.php" class="nav-link <?php echo isAct('my_products.php'); ?>">Produk Saya</a></li>
                <li><a href="upload_product.php" class="btn-upload <?php echo isAct('upload_product.php'); ?>">+ Jual</a></li>
                <li><a href="dashboard.php" class="nav-link <?php echo isAct('dashboard.php'); ?>">Dashboard</a></li>
                <li><a href="report_fraud.php" class="nav-link <?php echo isAct('report_fraud.php'); ?>" style="color: #e74c3c; font-weight: 500;">⚠️ Lapor Fraud</a></li>
                <li>
                    <span class="user-info">👤 <?php echo htmlspecialchars($user['full_name'] ?? 'User'); ?></span>
                </li>
                <li><a href="logout.php" class="nav-link logout">Logout</a></li>
            </ul>
        </div>
    </nav>
    <?php endif; ?>
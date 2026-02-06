<?php
session_start();
include 'includes/config.php';
requireLogin();

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];
$user = getUserInfo($user_id);
$error = "";
$success = "";

if ($product_id <= 0) {
    header("Location: shop.php");
    exit();
}

// Get product details
$product_query = "SELECT p.*, u.id as seller_id, u.username, u.full_name, u.rating 
                  FROM products p 
                  JOIN users u ON p.seller_id = u.id 
                  WHERE p.id = ?";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();

if ($product_result->num_rows === 0) {
    header("Location: shop.php");
    exit();
}

$product = $product_result->fetch_assoc();

// Update views
$update_views = "UPDATE products SET views = views + 1 WHERE id = ?";
$stmt = $conn->prepare($update_views);
$stmt->bind_param("i", $product_id);
$stmt->execute();

// Handle pembelian
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy'])) {
    $quantity = (int)$_POST['quantity'];
    $shipping_address = sanitize($_POST['shipping_address']);
    $notes = sanitize($_POST['notes']);
    $payment_method = sanitize($_POST['payment_method']);

    if ($quantity <= 0 || $quantity > $product['stock']) {
        $error = "Jumlah produk tidak valid!";
    } elseif (empty($shipping_address)) {
        $error = "Alamat pengiriman harus diisi!";
    } elseif (empty($payment_method)) {
        $error = "Metode pembayaran harus dipilih!";
    } else {
        // Insert transaction
        $total_price = $product['price'] * $quantity;
        $transaction_query = "INSERT INTO transactions (product_id, buyer_id, seller_id, quantity, total_price, shipping_address, notes, status, payment_method)
                             VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?)";
        
        $stmt = $conn->prepare($transaction_query);
        $stmt->bind_param("iiidisss", $product_id, $user_id, $product['seller_id'], $quantity, $total_price, $shipping_address, $notes, $payment_method);
        
        if ($stmt->execute()) {
            $transaction_id = $stmt->insert_id;
            $success = "Pembelian berhasil! Silakan lakukan pembayaran.";
            // Update stock
            $new_stock = $product['stock'] - $quantity;
            $update_stock = "UPDATE products SET stock = ?, status = ? WHERE id = ?";
            $status = $new_stock > 0 ? 'active' : 'sold';
            $stmt = $conn->prepare($update_stock);
            $stmt->bind_param("isi", $new_stock, $status, $product_id);
            $stmt->execute();
            
            // Redirect ke payment
            header("refresh:2;url=payment.php?id=$transaction_id");
        } else {
            $error = "Gagal memproses pembelian!";
        }
    }
}

// Handle submit review
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $rating = (int)$_POST['rating'];
    $review_text = $_POST['review_text'];
    
    if ($rating < 1 || $rating > 5) {
        $error = "Rating harus antara 1-5!";
    } else {
        $result = submitReview($product_id, $user_id, $product['seller_id'], $rating, $review_text);
        if ($result === 'success') {
            $success = "Terima kasih atas ulasan Anda!";
            header("refresh:1;url=product_detail.php?id=$product_id");
        } elseif ($result === 'already_reviewed') {
            $error = "Anda sudah memberikan ulasan untuk produk ini!";
        } else {
            $error = "Gagal menyimpan ulasan!";
        }
    }
}

// Get reviews
$reviews_query = "SELECT r.*, u.full_name FROM reviews r 
                  JOIN users u ON r.buyer_id = u.id 
                  WHERE r.product_id = ?
                  ORDER BY r.created_at DESC
                  LIMIT 5";
$stmt = $conn->prepare($reviews_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$reviews_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['title']); ?> - MarketHub</title>
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
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="report_fraud.php" class="nav-link" style="color: #e74c3c; font-weight: 500;">⚠️ Lapor Fraud</a></li>
                <li>
                    <span class="user-info">👤 <?php echo htmlspecialchars($user['full_name']); ?></span>
                </li>
                <li><a href="logout.php" class="nav-link logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="product-detail">
            <div class="product-detail-image">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($product['title']); ?>">
                <p class="views-count">👁️ <?php echo $product['views']; ?> views</p>
            </div>

            <div class="product-detail-info">
                <h1><?php echo htmlspecialchars($product['title']); ?></h1>
                
                <div class="seller-section">
                    <h3>Penjual: <?php echo htmlspecialchars($product['full_name']); ?></h3>
                    <p>Username: <strong><?php echo htmlspecialchars($product['username']); ?></strong></p>
                    <p>Rating: ⭐ <?php echo round($product['rating'], 1); ?></p>
                    <p><a href="report_fraud.php?username=<?php echo urlencode($product['username']); ?>" style="color: #e74c3c; font-size: 0.9em; text-decoration: none;">⚠️ Laporkan Penjual ini</a></p>
                </div>

                <div class="price-detail">
                    <h2>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></h2>
                    <p class="stock-status <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                        <?php echo $product['stock'] > 0 ? '✓ Tersedia (' . $product['stock'] . ')' : '✗ Habis'; ?>
                    </p>
                </div>

                <div class="product-description">
                    <h3>Deskripsi Produk</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if ($product['stock'] > 0): ?>
                    <form method="POST" class="purchase-form">
                        <div class="form-group">
                            <label for="quantity">Jumlah</label>
                            <input type="number" id="quantity" name="quantity" min="1" max="<?php echo $product['stock']; ?>" value="1" required>
                        </div>

                        <div class="form-group">
                            <label for="payment_method">Metode Pembayaran *</label>
                            <select id="payment_method" name="payment_method" required>
                                <option value="">Pilih Metode Pembayaran</option>
                                <?php 
                                $methods = getPaymentMethods();
                                foreach ($methods as $method): 
                                ?>
                                    <option value="<?php echo $method['id']; ?>">
                                        <?php echo $method['icon'] . ' ' . $method['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="shipping_address">Alamat Pengiriman *</label>
                            <textarea id="shipping_address" name="shipping_address" rows="3" required 
                                    placeholder="Masukkan alamat lengkap untuk pengiriman"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="notes">Catatan (Opsional)</label>
                            <textarea id="notes" name="notes" rows="2" 
                                    placeholder="Contoh: Tolong dikemas rapi"></textarea>
                        </div>

                        <button type="submit" name="buy" class="btn-primary btn-large">🛒 Beli Sekarang</button>
                    </form>
                <?php else: ?>
                    <div class="out-of-stock-message">
                        <p>Produk ini sudah habis terjual</p>
                    </div>
                <?php endif; ?>

                <a href="shop.php" class="btn-secondary">← Kembali</a>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="reviews-section">
            <h2>Ulasan Produk</h2>
            
            <!-- Form Submit Review -->
            <div class="review-form">
                <h3>Berikan Ulasan Anda</h3>
                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" class="form">
                    <div class="form-group">
                        <label for="rating">Rating *</label>
                        <select id="rating" name="rating" required>
                            <option value="">Pilih Rating</option>
                            <option value="5">⭐⭐⭐⭐⭐ Sangat Puas</option>
                            <option value="4">⭐⭐⭐⭐ Puas</option>
                            <option value="3">⭐⭐⭐ Cukup</option>
                            <option value="2">⭐⭐ Kurang Puas</option>
                            <option value="1">⭐ Sangat Tidak Puas</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="review_text">Ulasan *</label>
                        <textarea id="review_text" name="review_text" rows="4" required 
                                placeholder="Bagikan pengalaman Anda membeli produk ini..."></textarea>
                    </div>

                    <button type="submit" name="submit_review" class="btn-primary">Kirim Ulasan</button>
                </form>
            </div>

            <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">

            <!-- Display Reviews -->
            <h3>Ulasan dari Pembeli</h3>
            <?php if ($reviews_result->num_rows > 0): ?>
                <div class="reviews-list">
                    <?php while ($review = $reviews_result->fetch_assoc()): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                                <span class="review-rating">⭐ <?php echo $review['rating']; ?>/5</span>
                            </div>
                            <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                            <small><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></small>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>Belum ada ulasan untuk produk ini</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 MarketHub - Platform Jual Beli Online Terpercaya</p>
    </footer>
</body>
</html>
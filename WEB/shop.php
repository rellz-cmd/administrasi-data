<?php
session_start();
include 'includes/config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$user = getUserInfo($user_id);

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 12;
$offset = ($page - 1) * $items_per_page;

// Filter kategori dan pencarian
$category_filter = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$search_query = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$where = "WHERE p.status = 'active'";
$params = array();
$types = "";

if ($search_query) {
    $where .= " AND (p.title LIKE ? OR p.description LIKE ?)";
    $search = "%$search_query%";
    array_push($params, $search, $search);
    $types .= "ss";
}

if ($category_filter) {
    $where .= " AND p.category = ?";
    array_push($params, $category_filter);
    $types .= "s";
}

// Get total products
$count_query = "SELECT COUNT(*) as total FROM products p $where";
$stmt = $conn->prepare($count_query);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$count_result = $stmt->get_result();
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $items_per_page);

// Get products
$query = "SELECT p.*, u.username, u.rating FROM products p 
          JOIN users u ON p.seller_id = u.id 
          $where 
          ORDER BY p.created_at DESC 
          LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
if ($types) {
    array_push($params, $items_per_page, $offset);
    $types .= "ii";
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param("ii", $items_per_page, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

// Get categories
$categories_query = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL";
$categories_result = $conn->query($categories_query);
$categories = array();
while ($cat = $categories_result->fetch_assoc()) {
    $categories[] = $cat['category'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belanja - MarketHub</title>
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
                <li><a href="shop.php" class="nav-link active">Belanja</a></li>
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

    <!-- Search Section -->
    <div class="search-section">
        <div class="container">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Cari produk..." 
                       value="<?php echo htmlspecialchars($search_query); ?>">
                <select name="category">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" 
                                <?php echo $cat === $category_filter ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-primary">Cari</button>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="container">
        <div class="products-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($product = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['title']); ?>">
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars(substr($product['title'], 0, 40)); ?></h3>
                            <p class="seller-info">Penjual: <strong><?php echo htmlspecialchars($product['username']); ?></strong></p>
                            <p class="rating">⭐ <?php echo round($product['rating'], 1); ?></p>
                            <div class="price-section">
                                <span class="price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                            </div>
                            <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn-detail">Lihat Detail</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-products">
                    <p>Produk tidak ditemukan</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="shop.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search_query); ?>&category=<?php echo urlencode($category_filter); ?>"
                       class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 MarketHub - Platform Jual Beli Online Terpercaya</p>
    </footer>
</body>
</html>
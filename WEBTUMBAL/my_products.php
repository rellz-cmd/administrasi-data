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

// Get user products
$query = "SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $items_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Get total products
$count_query = "SELECT COUNT(*) as total FROM products WHERE seller_id = ?";
$stmt = $conn->prepare($count_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$count_result = $stmt->get_result();
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $items_per_page);

$page_title = "Produk Saya - MarketHub";
include 'includes/header.php';
?>
    <div class="container">
        <div class="my-products-header">
            <h1>Produk Saya</h1>
            <a href="upload_product.php" class="btn-primary">+ Tambah Produk</a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="products-grid">
                <?php while ($product = $result->fetch_assoc()): ?>
                    <div class="product-card my-product">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['title']); ?>">
                            <div class="product-status">
                                <span class="status-badge status-<?php echo $product['status']; ?>">
                                    <?php echo ucfirst($product['status']); ?>
                                </span>
                                <span class="stock-info">Stok: <?php echo $product['stock']; ?></span>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars(substr($product['title'], 0, 40)); ?></h3>
                            <p><?php echo htmlspecialchars(substr($product['description'], 0, 80)); ?>...</p>
                            <div class="price-section">
                                <span class="price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                                <span class="views">👁️ <?php echo $product['views']; ?></span>
                            </div>
                            <div class="product-actions">
                                <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn-detail">Lihat</a>
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn-edit">Edit</a>
                                <a href="delete_product.php?id=<?php echo $product['id']; ?>" 
                                   onclick="return confirm('Yakin ingin menghapus?');" class="btn-delete">Hapus</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="my_products.php?page=<?php echo $i; ?>"
                           class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-products">
                <p>Anda belum memiliki produk. <a href="upload_product.php">Jual produk sekarang</a></p>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
<?php
session_start();
include 'includes/config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = "";
$success = "";

// Get product
$query = "SELECT * FROM products WHERE id = ? AND seller_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: my_products.php");
    exit();
}

$product = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $price = (float)$_POST['price'];
    $category = sanitize($_POST['category']);
    $stock = (int)$_POST['stock'];
    $status = sanitize($_POST['status']);

    $image_url = $product['image_url'];

    // Handle file upload jika ada
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $file = $_FILES['image'];
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        
        if (!in_array($file['type'], $allowed_types)) {
            $error = "Format gambar harus JPG, PNG, atau GIF!";
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $error = "Ukuran gambar maksimal 5MB!";
        } else {
            // Hapus gambar lama
            if (file_exists($product['image_url'])) {
                unlink($product['image_url']);
            }

            // Simpan gambar baru
            $filename = time() . '_' . basename($file['name']);
            $image_url = 'uploads/products/' . $filename;
            
            if (!move_uploaded_file($file['tmp_name'], $image_url)) {
                $error = "Gagal upload gambar!";
            }
        }
    }

    if (!$error) {
        // Update produk
        $update_query = "UPDATE products SET title = ?, description = ?, price = ?, category = ?, stock = ?, status = ?, image_url = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssdsissi", $title, $description, $price, $category, $stock, $status, $image_url, $product_id);

        if ($stmt->execute()) {
            $success = "Produk berhasil diperbarui!";
            $product = compact('title', 'description', 'price', 'category', 'stock', 'status', 'image_url', 'product_id');
        } else {
            $error = "Gagal memperbarui produk!";
        }
    }
}

$categories = array('Elektronik', 'Fashion', 'Rumah Tangga', 'Olahraga', 'Buku', 'Mainan', 'Lainnya');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - MarketHub</title>
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
                <li><a href="my_products.php" class="nav-link active">Produk Saya</a></li>
                <li><a href="upload_product.php" class="btn-upload">+ Jual</a></li>
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="report_fraud.php" class="nav-link" style="color: #e74c3c; font-weight: 500;">⚠️ Lapor Fraud</a></li>
                <li><a href="logout.php" class="nav-link logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="form-section">
            <h1>Edit Produk</h1>
            
            <form method="POST" enctype="multipart/form-data" class="upload-form">
                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="title">Judul Produk</label>
                    <input type="text" id="title" name="title" required maxlength="200" 
                           value="<?php echo htmlspecialchars($product['title']); ?>">
                </div>

                <div class="form-group">
                    <label for="category">Kategori</label>
                    <select id="category" name="category" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" 
                                    <?php echo $cat === $product['category'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi Produk</label>
                    <textarea id="description" name="description" required rows="6"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Harga (Rp)</label>
                        <input type="number" id="price" name="price" required min="1000" step="1000"
                               value="<?php echo $product['price']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="stock">Stok</label>
                        <input type="number" id="stock" name="stock" required min="1" 
                               value="<?php echo $product['stock']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>Aktif</option>
                            <option value="inactive" <?php echo $product['status'] === 'inactive' ? 'selected' : ''; ?>>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="image">Ganti Foto Produk (Opsional)</label>
                    <div class="current-image">
                        <p>Gambar saat ini:</p>
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product" style="max-width: 200px;">
                    </div>
                    <div class="file-upload">
                        <input type="file" id="image" name="image" accept="image/*">
                        <p>Klik atau drag gambar baru ke sini</p>
                        <small>Format: JPG, PNG, GIF | Ukuran max: 5MB</small>
                    </div>
                    <img id="preview" src="" alt="Preview" style="display:none; max-width: 300px; margin-top: 10px;">
                </div>

                <button type="submit" class="btn-primary btn-large">Simpan Perubahan</button>
                <a href="my_products.php" class="btn-secondary btn-large">Batal</a>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 MarketHub</p>
    </footer>

    <script>
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
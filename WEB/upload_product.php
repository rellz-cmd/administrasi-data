<?php
session_start();
include 'includes/config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$user = getUserInfo($user_id);
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $price = (float)$_POST['price'];
    $category = sanitize($_POST['category']);
    $stock = (int)$_POST['stock'];

    // Validasi input
    if (empty($title) || empty($description) || $price <= 0) {
        $error = "Semua field harus diisi dengan benar!";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
        $error = "Silakan upload gambar produk!";
    } else {
        // Handle file upload
        $file = $_FILES['image'];
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        
        if (!in_array($file['type'], $allowed_types)) {
            $error = "Format gambar harus JPG, PNG, atau GIF!";
        } elseif ($file['size'] > 5 * 1024 * 1024) { // 5MB
            $error = "Ukuran gambar maksimal 5MB!";
        } else {
            // Simpan file
            $filename = time() . '_' . basename($file['name']);
            $upload_path = 'uploads/products/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Insert produk ke database
                $insert_query = "INSERT INTO products (seller_id, title, description, price, category, stock, image_url) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("issdsss", $user_id, $title, $description, $price, $category, $stock, $upload_path);
                
                if ($stmt->execute()) {
                    $product_id = $stmt->insert_id;
                    $success = "Produk berhasil ditambahkan!";
                    // Redirect ke produk yang baru dibuat
                    header("refresh:2;url=product_detail.php?id=$product_id");
                } else {
                    $error = "Terjadi kesalahan saat menyimpan produk!";
                }
            } else {
                $error = "Gagal upload gambar!";
            }
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
    <title>Jual Produk - MarketHub</title>
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
                <li><a href="upload_product.php" class="btn-upload active">+ Jual</a></li>
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
        <div class="form-section">
            <h1>Jual Produk Baru</h1>
            
            <form method="POST" enctype="multipart/form-data" class="upload-form">
                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="title">Judul Produk *</label>
                    <input type="text" id="title" name="title" required maxlength="200" 
                           placeholder="Contoh: iPhone 13 Pro 256GB">
                </div>

                <div class="form-group">
                    <label for="category">Kategori *</label>
                    <select id="category" name="category" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>">
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi Produk *</label>
                    <textarea id="description" name="description" required rows="6" 
                              placeholder="Jelaskan kondisi produk, fitur, kelebihan, dll"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Harga (Rp) *</label>
                        <input type="number" id="price" name="price" required min="1000" step="1000"
                               placeholder="100000">
                    </div>
                    <div class="form-group">
                        <label for="stock">Stok *</label>
                        <input type="number" id="stock" name="stock" required min="1" value="1">
                    </div>
                </div>

                <div class="form-group">
                    <label for="image">Foto Produk *</label>
                    <div class="file-upload">
                        <input type="file" id="image" name="image" accept="image/*" required>
                        <p>Klik atau drag gambar ke sini</p>
                        <small>Format: JPG, PNG, GIF | Ukuran max: 5MB</small>
                    </div>
                    <img id="preview" src="" alt="Preview" style="display:none; max-width: 300px; margin-top: 10px;">
                </div>

                <button type="submit" class="btn-primary btn-large">Posting Produk</button>
                <a href="my_products.php" class="btn-secondary btn-large">Batal</a>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 MarketHub - Platform Jual Beli Online Terpercaya</p>
    </footer>

    <script>
        // Preview gambar
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

        // Drag and drop
        const fileInput = document.getElementById('image');
        const fileUpload = document.querySelector('.file-upload');
        
        fileUpload.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUpload.style.backgroundColor = '#f0f0f0';
        });

        fileUpload.addEventListener('dragleave', () => {
            fileUpload.style.backgroundColor = 'transparent';
        });

        fileUpload.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUpload.style.backgroundColor = 'transparent';
            fileInput.files = e.dataTransfer.files;
            
            // Trigger change event
            const event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        });
    </script>
</body>
</html>
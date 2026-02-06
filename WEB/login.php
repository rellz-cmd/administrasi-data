<?php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: shop.php");
    exit();
}

include 'includes/config.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_email = sanitize($_POST['username_email']);
    $password = $_POST['password'];

    // Query untuk mencari user berdasarkan username atau email
    $query = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username_email, $username_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            
            header("Location: shop.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username atau email tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Commerce</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="logo-section">
                <h1>🛍️ MarketHub</h1>
                <p>Platform Jual Beli Online Terpercaya</p>
            </div>

            <form method="POST" class="form">
                <h2>Masuk</h2>

                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="username_email">Username atau Email</label>
                    <input type="text" id="username_email" name="username_email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn-primary">Masuk</button>

                <p class="form-footer">
                    Belum punya akun? <a href="register.php">Daftar di sini</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
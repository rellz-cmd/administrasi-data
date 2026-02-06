<?php
session_start();
include 'includes/config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$user = getUserInfo($user_id);
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $city = sanitize($_POST['city']);
    $province = sanitize($_POST['province']);
    $postal_code = sanitize($_POST['postal_code']);
    $address = sanitize($_POST['address']);

    if (empty($full_name)) {
        $error = "Nama lengkap harus diisi!";
    } else {
        $update_query = "UPDATE users SET full_name = ?, phone = ?, address = ?, city = ?, province = ?, postal_code = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssssssi", $full_name, $phone, $address, $city, $province, $postal_code, $user_id);

        if ($stmt->execute()) {
            $success = "Profil berhasil diperbarui!";
            $_SESSION['full_name'] = $full_name;
            $user = getUserInfo($user_id);
        } else {
            $error = "Gagal memperbarui profil!";
        }
    }
}

$page_title = "Edit Profil - MarketHub";
include 'includes/header.php';
?>
    <div class="container">
        <div class="form-section">
            <h1>Edit Profil</h1>

            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" class="form">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" readonly disabled>
                    <small>Username tidak dapat diubah</small>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly disabled>
                    <small>Email tidak dapat diubah</small>
                </div>

                <div class="form-group">
                    <label for="full_name">Nama Lengkap *</label>
                    <input type="text" id="full_name" name="full_name" required
                           value="<?php echo htmlspecialchars($user['full_name']); ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Nomor Telepon</label>
                    <input type="tel" id="phone" name="phone"
                           value="<?php echo htmlspecialchars($user['phone']); ?>">
                </div>

                <div class="form-group">
                    <label for="address">Alamat</label>
                    <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">Kota</label>
                        <input type="text" id="city" name="city"
                               value="<?php echo htmlspecialchars($user['city']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="province">Provinsi</label>
                        <input type="text" id="province" name="province"
                               value="<?php echo htmlspecialchars($user['province']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="postal_code">Kode Pos</label>
                        <input type="text" id="postal_code" name="postal_code"
                               value="<?php echo htmlspecialchars($user['postal_code']); ?>">
                    </div>
                </div>

                <button type="submit" class="btn-primary btn-large">Simpan Perubahan</button>
                <a href="dashboard.php" class="btn-secondary btn-large">Batal</a>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
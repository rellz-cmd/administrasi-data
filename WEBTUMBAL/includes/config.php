<?php
// Konfigurasi Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce_db";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset ke utf8
$conn->set_charset("utf8");

// Fungsi helper untuk secure input
function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Fungsi untuk cek jika user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fungsi untuk redirect ke login jika belum login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Fungsi untuk mendapatkan informasi user
function getUserInfo($user_id) {
    global $conn;
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Fungsi untuk update credit score
function updateCreditScore($user_id, $points) {
    global $conn;
    $query = "UPDATE users SET credit_score = GREATEST(0, credit_score + ?) WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $points, $user_id);
    return $stmt->execute();
}

// Fungsi untuk mendapatkan payment methods
function getPaymentMethods() {
    return [
        ['id' => 'qris', 'name' => 'QRIS', 'icon' => '📱'],
        ['id' => 'cash_cod', 'name' => 'Bayar di Tempat (COD)', 'icon' => '💵'],
        ['id' => 'credit_card', 'name' => 'Kartu Kredit', 'icon' => '💳']
    ];
}

// Fungsi untuk update payment status
function updatePaymentStatus($transaction_id, $status) {
    global $conn;
    $query = "UPDATE transactions SET payment_status = ?, status = ? WHERE id = ?";
    $new_status = ($status === 'paid') ? 'paid' : 'pending';
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $status, $new_status, $transaction_id);
    return $stmt->execute();
}

// Fungsi untuk submit review
function submitReview($product_id, $buyer_id, $seller_id, $rating, $review_text) {
    global $conn;
    
    // Cek apakah sudah ada review dari pembeli untuk produk ini
    $check_query = "SELECT id FROM reviews WHERE product_id = ? AND buyer_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $product_id, $buyer_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        return "already_reviewed";
    }
    
    // Insert review
    $insert_query = "INSERT INTO reviews (product_id, buyer_id, seller_id, rating, review_text) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $review_text = sanitize($review_text);
    $insert_stmt->bind_param("iiiis", $product_id, $buyer_id, $seller_id, $rating, $review_text);
    
    if ($insert_stmt->execute()) {
        // Update seller rating
        updateSellerRating($seller_id);
        return "success";
    }
    
    return "error";
}

// Fungsi untuk update seller rating berdasarkan reviews
function updateSellerRating($seller_id) {
    global $conn;
    $query = "UPDATE users SET rating = (SELECT AVG(rating) FROM reviews WHERE seller_id = ?) WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $seller_id, $seller_id);
    return $stmt->execute();
}

// Fungsi untuk get reviews produk
function getProductReviews($product_id) {
    global $conn;
    $query = "SELECT r.*, u.full_name FROM reviews r 
              JOIN users u ON r.buyer_id = u.id 
              WHERE r.product_id = ? 
              ORDER BY r.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    return $stmt->get_result();
}
?>
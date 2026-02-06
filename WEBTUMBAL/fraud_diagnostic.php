<?php
session_start();
include 'includes/config.php';

echo "<h2>🔍 Fraud Report Diagnostic Check</h2>";
echo "<hr>";

// 1. Check database connection
echo "<h3>1. Database Connection</h3>";
if ($conn->connect_error) {
    echo "❌ ERROR: " . $conn->connect_error;
} else {
    echo "✅ Database connected successfully";
}
echo "<br><br>";

// 2. Check if fraud_reports table exists
echo "<h3>2. Check fraud_reports Table</h3>";
$result = $conn->query("SHOW TABLES LIKE 'fraud_reports'");
if ($result && $result->num_rows > 0) {
    echo "✅ fraud_reports table exists";
    
    // Check columns
    $columns = $conn->query("DESCRIBE fraud_reports");
    echo "<table border='1' cellpadding='5'>";
    while ($col = $columns->fetch_assoc()) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "❌ fraud_reports table NOT found - Need to create it!";
}
echo "<br><br>";

// 3. Check users table columns
echo "<h3>3. Check users Table Columns</h3>";
$user_columns = $conn->query("DESCRIBE users");
$has_credit_score = false;
$has_fraud_count = false;
$has_is_banned = false;

echo "<table border='1' cellpadding='5'>";
while ($col = $user_columns->fetch_assoc()) {
    echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td></tr>";
    if ($col['Field'] === 'credit_score') $has_credit_score = true;
    if ($col['Field'] === 'fraud_count') $has_fraud_count = true;
    if ($col['Field'] === 'is_banned') $has_is_banned = true;
}
echo "</table>";

echo "<h4>Column Status:</h4>";
echo $has_credit_score ? "✅ credit_score exists" : "❌ credit_score MISSING";
echo "<br>";
echo $has_fraud_count ? "✅ fraud_count exists" : "❌ fraud_count MISSING";
echo "<br>";
echo $has_is_banned ? "✅ is_banned exists" : "❌ is_banned MISSING";
echo "<br><br>";

// 4. Check sample data
echo "<h3>4. Sample Sellers Data</h3>";
$sellers = $conn->query("SELECT id, username, full_name, credit_score, fraud_count, is_banned FROM users WHERE role = 'seller' LIMIT 5");
if ($sellers && $sellers->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Username</th><th>Name</th><th>Credit Score</th><th>Fraud Count</th><th>Banned</th></tr>";
    while ($seller = $sellers->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$seller['id']}</td>";
        echo "<td>{$seller['username']}</td>";
        echo "<td>{$seller['full_name']}</td>";
        echo "<td>{$seller['credit_score']}</td>";
        echo "<td>{$seller['fraud_count']}</td>";
        echo "<td>" . ($seller['is_banned'] ? 'YES' : 'NO') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "⚠️ No sellers found in database";
}
echo "<br><br>";

// 5. Check fraud reports
echo "<h3>5. Existing Fraud Reports</h3>";
$reports = $conn->query("SELECT * FROM fraud_reports ORDER BY reported_date DESC LIMIT 5");
if ($reports && $reports->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Seller ID</th><th>Reporter ID</th><th>Reason</th><th>Date</th></tr>";
    while ($report = $reports->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$report['id']}</td>";
        echo "<td>{$report['seller_id']}</td>";
        echo "<td>{$report['reporter_id']}</td>";
        echo "<td>" . substr($report['reason'], 0, 50) . "...</td>";
        echo "<td>{$report['reported_date']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "ℹ️ No fraud reports yet";
}
echo "<br><br>";

// 6. Test insert
echo "<h3>6. Test Functions</h3>";
if ($_SESSION['user_id'] && $_POST['test_insert'] ?? false) {
    $test_seller_id = intval($_POST['test_seller_id']);
    $test_reason = sanitize($_POST['test_reason']);
    
    echo "Testing insert with Seller ID: $test_seller_id<br>";
    
    $test_insert = $conn->prepare("INSERT INTO fraud_reports (seller_id, reporter_id, reason, reported_date) VALUES (?, ?, ?, NOW())");
    $test_insert->bind_param("iis", $test_seller_id, $_SESSION['user_id'], $test_reason);
    
    if ($test_insert->execute()) {
        echo "✅ Test insert successful!";
    } else {
        echo "❌ Test insert failed: " . $test_insert->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fraud Diagnostic</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        h2, h3 { color: #333; }
        .test-form { background: #f0f0f0; padding: 20px; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="test-form">
        <h4>Test Fraud Report Insert</h4>
        <form method="POST">
            <input type="hidden" name="test_insert" value="1">
            Seller ID: <input type="number" name="test_seller_id" required min="1">
            Reason: <input type="text" name="test_reason" required minlength="10" placeholder="Min 10 chars">
            <button type="submit">Test Insert</button>
        </form>
    </div>
</body>
</html>

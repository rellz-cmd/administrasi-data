<?php
session_start();

// Jika user sudah login, redirect ke shop
if (isset($_SESSION['user_id'])) {
    header("Location: shop.php");
    exit();
}

// Jika belum login, tampilkan landing page
include 'index.html';
?>
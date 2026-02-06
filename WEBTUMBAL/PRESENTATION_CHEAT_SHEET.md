# 🎤 PANDUAN PRESENTASI & LIVE CODING MARKETHUB

Dokumen ini adalah panduan rahasia ("Cheat Sheet") untuk mempresentasikan alur kode fitur utama dan menjawab tantangan modifikasi kode secara langsung (Live Coding).

---

## 1️⃣ BAGIAN 1: PENJELASAN ALUR KODE (CODE WALKTHROUGH)

Gunakan poin-poin ini saat menjelaskan bagaimana kode Anda bekerja kepada penguji.

### 💳 A. Fitur Payment (`payment.php` & `config.php`)
**Penjelasan:**
"Sistem pembayaran kami menggunakan desain terpusat. Daftar metode pembayaran tidak di-*hardcode* di halaman tampilan, melainkan diambil dari fungsi konfigurasi."

**Alur Kode:**
1.  **Inisialisasi:** Halaman `payment.php` memanggil fungsi `getPaymentMethods()` dari `includes/config.php`.
2.  **Logika Tampilan:** Menggunakan *looping* untuk menampilkan instruksi yang sesuai dengan metode yang dipilih (QRIS menampilkan QR, Transfer menampilkan Rekening).
3.  **Konfirmasi:** Saat user mengupload bukti, sistem melakukan update status transaksi menjadi `paid` dan mengurangi stok produk secara atomik (menggunakan Transaction SQL).

### 🛡️ B. Fitur Fraud & Auto-Ban (`report_fraud.php`)
**Penjelasan:**
"Fitur ini memiliki logika bisnis yang ketat untuk menjaga keamanan. Kami menggunakan *Database Transaction* untuk memastikan konsistensi data."

**Alur Kode:**
1.  **Validasi:** Sistem mengecek apakah pelapor bukan melaporkan diri sendiri dan belum melapor hari ini.
2.  **Eksekusi (Try-Catch Block):**
    *   Insert laporan ke tabel `fraud_reports`.
    *   Update `fraud_count` (+1) di tabel `users`.
    *   Update `credit_score` (-20) di tabel `users`.
3.  **Pengecekan Ban:** Setelah update, sistem langsung mengecek: `JIKA fraud_count >= 3 MAKA set is_banned = 1`.

### 📊 C. Fitur Credit Score (`dashboard.php`)
**Penjelasan:**
"Credit score adalah representasi visual dari kesehatan akun penjual."

**Alur Kode:**
1.  **Query:** Mengambil data `credit_score` dari tabel `users`.
2.  **Visualisasi:** Menggunakan logika PHP sederhana untuk menentukan warna bar:
    *   Hijau jika score >= 80.
    *   Kuning jika score >= 60.
    *   Merah jika score < 60.

---

## 2️⃣ BAGIAN 2: TANTANGAN LIVE CODING (MODIFIKASI)

Bagian ini berisi petunjuk langkah demi langkah jika penguji meminta Anda mengubah fitur di depan mereka.

### 🔴 KASUS 1: Mengubah Metode Pembayaran
**Pertanyaan Penguji:**
*"Sistem pembayaran terlalu banyak. Saya ingin aplikasi ini hanya menerima **QRIS** dan **Cash (COD)** saja. Bisakah Anda ubah kodenya sekarang?"*

**Langkah Penyelesaian:**
1.  Buka file `includes/config.php`.
2.  Cari fungsi bernama `getPaymentMethods()`.
3.  Hapus atau beri komentar (`//`) pada baris array `credit_card`.

**Kode di `includes/config.php`:**
```php
// --- SEBELUM DIUBAH ---
function getPaymentMethods() {
    return [
        ['id' => 'qris', 'name' => 'QRIS', 'icon' => '📱'],
        ['id' => 'cash_cod', 'name' => 'Bayar di Tempat (COD)', 'icon' => '💵'],
        ['id' => 'credit_card', 'name' => 'Kartu Kredit', 'icon' => '💳'] // <--- Hapus baris ini
    ];
}

// --- SETELAH DIUBAH ---
function getPaymentMethods() {
    return [
        ['id' => 'qris', 'name' => 'QRIS', 'icon' => '📱'],
        ['id' => 'cash_cod', 'name' => 'Bayar di Tempat (COD)', 'icon' => '💵']
    ];
}
```
*Hasil: Refresh halaman checkout, pilihan Kartu Kredit akan hilang.*

---

### 🔴 KASUS 2: Mengubah Batas Auto-Ban Fraud
**Pertanyaan Penguji:**
*"Batas 3 kali laporan fraud terlalu sedikit dan kejam. Tolong ganti menjadi **5 kali** baru di-ban."*

**Langkah Penyelesaian:**
1.  Buka file `report_fraud.php`.
2.  Cari logika pengecekan `fraud_count` (sekitar baris 80-90).
3.  Ubah angka `3` menjadi `5`.

**Kode di `report_fraud.php`:**
```php
// --- SEBELUM DIUBAH ---
if ($status_result['fraud_count'] >= 3 || $status_result['credit_score'] <= 40) {
    // Logic ban...
}

// --- SETELAH DIUBAH ---
if ($status_result['fraud_count'] >= 5 || $status_result['credit_score'] <= 40) {
    // Logic ban...
}
```

---

### 🔴 KASUS 3: Mengubah Hukuman Poin Credit Score
**Pertanyaan Penguji:**
*"Saya ingin hukuman pengurangan poin lebih berat. Ubah pengurangan poin dari 20 menjadi **50 poin** per laporan."*

**Langkah Penyelesaian:**
1.  Buka file `report_fraud.php`.
2.  Cari query update `credit_score` (sekitar baris 70-80).
3.  Ubah angka `20` menjadi `50`.

**Kode di `report_fraud.php`:**
```php
// --- SEBELUM DIUBAH ---
$update_score = $conn->prepare("UPDATE users SET credit_score = GREATEST(0, credit_score - 20) WHERE id = ?");

// --- SETELAH DIUBAH ---
$update_score = $conn->prepare("UPDATE users SET credit_score = GREATEST(0, credit_score - 50) WHERE id = ?");
```

---

**💡 Tips Tambahan:**
Selalu katakan: *"Karena kode saya terstruktur dan menggunakan konfigurasi terpusat (modular), perubahan ini sangat mudah dilakukan tanpa merusak fitur lain."*
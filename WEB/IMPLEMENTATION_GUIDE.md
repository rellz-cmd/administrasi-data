             # 🚀 PANDUAN IMPLEMENTASI FITUR BARU

## Ringkas Fitur Baru

Saya telah menambahkan **3 fitur utama** ke MarketHub:

### ✅ Fitur 1: Sistem Kredit Score & Anti-Fraud
**Tujuan:** Melindungi pembeli dari penjual curang
- Setiap penjual punya credit score 100 poin
- Jika dilaporkan fraud, credit score turun
- Auto-BAN jika fraud count mencapai 3x
- Penjual yang di-ban tidak bisa berjualan

### ✅ Fitur 2: Sistem Review & Rating Pembeli
**Tujuan:** Pembeli bisa memberikan feedback
- Pembeli bisa rating dan review produk (1-5 bintang)
- Rating auto-update penjual
- 1 pembeli = 1 review per produk (tidak bisa double)
- Validasi penuh

### ✅ Fitur 3: Sistem Pembayaran Multi-Metode  
**Tujuan:** Pembeli punya pilihan pembayaran
- Transfer Bank
- Kartu Kredit
- E-Wallet (GCash/Dana)
- Bayar di Tempat (COD)
- Halaman payment dengan instruksi detail

---

## 📝 File yang Diubah/Ditambah

### File BARU (yang ditambahkan):
```
✓ payment.php                    - Halaman pembayaran
✓ report_fraud.php               - Halaman lapor fraud
✓ FEATURES.md                    - Dokumentasi fitur
✓ IMPLEMENTATION_GUIDE.md        - File ini
```

### File DIUBAH:
```
✓ includes/config.php            - +12 fungsi baru
✓ includes/db.sql               - +3 kolom baru
✓ product_detail.php            - +Review form, +Payment method
✓ dashboard.php                 - +Credit score display
✓ index.php                     - +Link "Lapor Fraud"
✓ upload_product.php            - +Link "Lapor Fraud"
✓ my_products.php               - +Link "Lapor Fraud"
✓ assets/css/style.css          - +100+ baris CSS
```

### File TIDAK DIUBAH (Aman):
```
✓ login.php
✓ register.php
✓ logout.php
✓ edit_profile.php
✓ edit_product.php
✓ delete_product.php
```

---

## 🔧 Cara Setup

### Step 1: Backup Database (PENTING!)
```bash
# Backup database lama (jika ada)
Export dari phpMyAdmin atau:
mysqldump -u root ecommerce_db > backup.sql
```

### Step 2: Update Database

**Opsi A: Import db.sql baru (Recommended)**
1. Buka phpMyAdmin: http://localhost/phpmyadmin
2. DROP database `ecommerce_db` (jika ada)
3. Buat database baru `ecommerce_db`
4. Import file `includes/db.sql`
5. Done!

**Opsi B: Manual ALTER (Jika sudah ada data)**
```sql
-- Jalankan di phpMyAdmin Query tab
ALTER TABLE users ADD COLUMN credit_score INT DEFAULT 100;
ALTER TABLE users ADD COLUMN fraud_count INT DEFAULT 0;
ALTER TABLE users ADD COLUMN is_banned BOOLEAN DEFAULT FALSE;

ALTER TABLE transactions ADD COLUMN payment_status ENUM('unpaid', 'paid', 'failed') DEFAULT 'unpaid';
```

### Step 3: Copy File
- Copy file baru: `payment.php`, `report_fraud.php`
- Update existing files (jangan delete yang lama!)
- Update CSS file

### Step 4: Test

**Test Fraud System:**
1. Login sebagai buyer
2. Klik "⚠️ Lapor Fraud" di navbar
3. Masukkan seller ID (misal: 2)
4. Isi alasan fraud
5. Klik lapor
6. Lihat fraud_count naik
7. Lapor 3x untuk test auto-ban

**Test Review System:**
1. Login sebagai buyer
2. Buka product detail
3. Scroll ke "Ulasan Produk"
4. Isi rating dan review
5. Klik "Kirim Ulasan"
6. Lihat rating seller terupdate

**Test Payment:**
1. Beli produk
2. Saat checkout, pilih payment method
3. Klik "Beli Sekarang"
4. Redirect ke payment page
5. Lihat instruksi pembayaran
6. Isi bukti pembayaran
7. Klik confirm
8. Status berubah ke "Dibayar"

---

## 📊 Perubahan Database

### Tabel: `users`
**Kolom Baru:**
```sql
credit_score INT DEFAULT 100           -- 100 poin awal
fraud_count INT DEFAULT 0              -- Jumlah laporan fraud
is_banned BOOLEAN DEFAULT FALSE        -- Status ban
```

### Tabel: `transactions`
**Kolom Baru:**
```sql
payment_status ENUM('unpaid', 'paid', 'failed') DEFAULT 'unpaid'
```

---

## 💻 Fungsi Baru di config.php

### Fraud & Credit Score
```php
isUserBanned($user_id)
// Return: true/false
// Gunakan untuk cek apakah user di-ban

addFraudReport($user_id, $reason)
// Return: "reported" atau "banned"
// Tambah fraud_count +1, auto-ban jika === 3

updateCreditScore($user_id, $points)
// Return: true/false
// Update credit score (bisa +/-)
```

### Review
```php
submitReview($product_id, $buyer_id, $seller_id, $rating, $review_text)
// Return: "success", "already_reviewed", "error"
// Submit review + auto-update seller rating

getProductReviews($product_id)
// Return: MySQLi Result object
// Get semua reviews produk

updateSellerRating($seller_id)
// Return: true/false
// Auto-calculate rata-rata rating dari reviews
```

### Payment
```php
getPaymentMethods()
// Return: Array dengan 4 metode pembayaran
// Format: ['id', 'name', 'icon']

updatePaymentStatus($transaction_id, $status)
// Return: true/false
// Update payment_status di database
```

---

## 🔌 Integrasi Payment (Real)

Untuk integrasi payment real (bukan simulasi):

### Bank Transfer
- Integrase dengan API bank (optional)
- Contoh: iPaymu, Midtrans, dll

### Kartu Kredit
- Gunakan gateway: Stripe, Midtrans, Xendit
- Update payment.php dengan API credentials

### E-Wallet
- Integrase dengan: GCash API, Dana API
- Minimal webhook untuk confirm pembayaran

### COD
- Tidak perlu API
- Carrier integration (optional)

---

## 🎨 Customization

### Ubah Jumlah Fraud untuk Ban
File: `includes/config.php`, fungsi `addFraudReport()`
```php
if ($user['fraud_count'] >= 3) {  // Ubah 3 menjadi nilai lain
    // Ban user
}
```

### Ubah Credit Score Awal
File: `includes/db.sql`
```sql
credit_score INT DEFAULT 100,  -- Ubah 100 menjadi nilai lain
```

### Ubah Metode Pembayaran
File: `includes/config.php`, fungsi `getPaymentMethods()`
```php
return array(
    array('id' => 'bank_transfer', 'name' => '...', 'icon' => '...'),
    // Tambah/hapus metode sesuai kebutuhan
);
```

---

## ⚠️ Troubleshooting

### Error: "Unknown column 'payment_status'"
**Solusi:** Run ALTER TABLE command atau re-import db.sql

### Error: "Call to undefined function submitReview()"
**Solusi:** Include config.php sudah benar? Cek `session_start()` sebelum `include`

### Review tidak tampil
**Solusi:** Cek di database, apakah review sudah tersimpan?
```sql
SELECT * FROM reviews WHERE product_id = 1;
```

### Payment page blank
**Solusi:** Cek transaction ID di URL valid?
```
http://localhost/WEB/payment.php?id=1
```

### Ban tidak bekerja
**Solusi:** Cek fraud_count di database
```sql
SELECT id, fraud_count, is_banned FROM users;
```

---

## 📞 Support & Questions

Jika ada error atau pertanyaan:
1. Cek FEATURES.md untuk detail fitur
2. Lihat console browser (F12) untuk JS error
3. Cek error log phpMyAdmin
4. Verify database kolom sudah ada

---

## ✅ Checklist Pre-Launch

- [ ] Database sudah update (kolom baru ada)
- [ ] File baru sudah ter-copy (payment.php, report_fraud.php)
- [ ] CSS sudah updated (payment & review styles)
- [ ] Test fraud report system
- [ ] Test review system
- [ ] Test all 4 payment methods
- [ ] Test auto-ban (fraud_count === 3)
- [ ] Backup database sebelum go-live

---

## 🎯 Next Steps

### Fitur yang bisa ditambah selanjutnya:
- Notifikasi email saat ada review baru
- Chat antara buyer & seller
- Integration payment gateway real
- Admin dashboard untuk manage fraud
- Analytics & reporting

---

## 📚 File Dokumentasi

1. **README.md** - Overview umum
2. **FEATURES.md** - Detail setiap fitur
3. **IMPLEMENTATION_GUIDE.md** - Panduan ini (setup & troubleshoot)

Baca semua 3 file untuk pemahaman lengkap!

---

Selamat! Fitur baru sudah siap digunakan. 🎉

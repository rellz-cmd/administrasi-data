# 🛍️ MarketHub - Platform E-Commerce Jual Beli

Aplikasi web komersial untuk jual beli barang online dengan fitur login, listing produk, foto, deskripsi, dan sistem transaksi.

## ✨ Fitur Utama

### 1. **Sistem Autentikasi**
- ✅ Registrasi akun baru dengan validasi
- ✅ Login dengan username atau email
- ✅ Password hashing menggunakan bcrypt
- ✅ Session management yang aman

### 2. **Manajemen Produk (Seller)**
- ✅ Upload produk dengan foto
- ✅ Deskripsi produk lengkap
- ✅ Kategori produk
- ✅ Harga dan stok management
- ✅ Edit dan hapus produk
- ✅ Lihat statistik penjualan

### 3. **Belanja (Buyer)**
- ✅ Browsing produk dengan search dan filter kategori
- ✅ Lihat detail produk dengan foto dan deskripsi
- ✅ Beli produk dengan sistem pemesanan
- ✅ Masukkan alamat pengiriman
- ✅ Track pembelian

### 4. **Dashboard**
- ✅ Statistik penjualan
- ✅ Total produk, produk terjual, pendapatan
- ✅ Riwayat transaksi
- ✅ Edit profil pengguna

### 5. **Fitur Tambahan**
- ✅ Rating dan review produk
- ✅ View counter untuk setiap produk
- ✅ Responsive design (mobile, tablet, desktop)
- ✅ Pagination untuk produk
- ✅ Validasi input yang ketat

### 6. **Sistem Kredit Score & Anti-Fraud** ⭐ NEW
- ✅ Credit Score untuk setiap penjual (100 poin awal)
- ✅ Fraud Count tracking untuk penjual yang bermasalah
- ✅ Auto-ban otomatis jika fraud count mencapai 3x
- ✅ Halaman lapor fraud untuk melaporkan penjual curang
- ✅ Dashboard kredit score realtime

### 7. **Sistem Review Pembeli** ⭐ NEW
- ✅ Pembeli bisa memberikan ulasan dan rating (1-5 bintang)
- ✅ Ulasan akan otomatis update rating penjual
- ✅ Validasi: 1 pembeli hanya bisa review 1x per produk
- ✅ Tampilan ulasan dengan nama pembeli, rating, dan teks review
- ✅ Sorting ulasan berdasarkan terbaru

### 8. **Sistem Pembayaran Multi-Metode** ⭐ NEW
- ✅ Transfer Bank (BCA, instruksi jelas)
- ✅ Kartu Kredit (integrasi ready)
- ✅ E-Wallet (GCash/Dana)
- ✅ Bayar di Tempat (COD)
- ✅ Payment status tracking (unpaid, paid, failed)
- ✅ Halaman payment dengan instruksi per metode

## 📋 Requirement

### Server Requirements
- **PHP**: 7.4 atau lebih tinggi
- **MySQL**: 5.7 atau lebih tinggi
- **Apache**: Dengan mod_rewrite enabled
- **XAMPP**: Recommended untuk development

### Browser Compatibility
- Chrome (terbaru)
- Firefox (terbaru)
- Safari (terbaru)
- Edge (terbaru)

## 🚀 Instalasi

### Step 1: Clone/Copy Aplikasi
```bash
Letakkan folder WEB di C:\xampp\htdocs\
```

### Step 2: Setup Database
1. Buka phpMyAdmin: http://localhost/phpmyadmin
2. Buat database baru dengan nama `ecommerce_db`
3. Import file SQL dari `includes/db.sql`:
   - Klik pada database `ecommerce_db`
   - Pilih tab "Import"
   - Pilih file `includes/db.sql`
   - Klik "Go"

### Step 3: Konfigurasi
Sudah otomatis! File `includes/config.php` sudah dikonfigurasi untuk XAMPP default.

Jika menggunakan setup yang berbeda, edit `includes/config.php`:
```php
$servername = "localhost";    // Host database
$username = "root";           // Username database
$password = "";               // Password database
$dbname = "ecommerce_db";     // Nama database
```

### Step 4: Permissions
Pastikan folder `uploads/products` memiliki write permission:
```bash
# Di Windows, tidak perlu action khusus
# Cek folder uploads/products sudah ada dan writable
```

### Step 5: Jalankan Aplikasi
1. Buka browser: http://localhost/WEB
2. Anda akan diarahkan ke halaman login
3. Klik "Daftar di sini" untuk membuat akun baru

## 📁 Struktur Folder

```
WEB/
├── assets/
│   ├── css/
│   │   └── style.css           # File CSS utama
│   ├── js/
│   │   └── (untuk script JS tambahan)
│   └── images/
├── includes/
│   ├── config.php              # Konfigurasi database
│   └── db.sql                  # File SQL untuk membuat database
├── uploads/
│   └── products/               # Folder untuk menyimpan foto produk
├── login.php                   # Halaman login
├── register.php                # Halaman registrasi
├── index.php                   # Halaman belanja (home)
├── dashboard.php               # Dashboard pengguna
├── my_products.php             # Halaman produk saya
├── upload_product.php          # Halaman upload produk
├── product_detail.php          # Halaman detail produk
├── edit_product.php            # Halaman edit produk
├── delete_product.php          # Script hapus produk
├── edit_profile.php            # Halaman edit profil
├── logout.php                  # Script logout
└── README.md                   # File dokumentasi ini
```

## 🔑 Akun Testing

Setelah setup database, buat akun baru melalui halaman registrasi:

**Contoh Akun:**
- **Username**: testuser
- **Email**: test@example.com
- **Password**: password123
- **Nama Lengkap**: Test User

## 💡 Cara Penggunaan

### Sebagai Pembeli (Buyer)

1. **Registrasi & Login**
   - Kunjungi halaman registrasi
   - Isi data diri dan buat akun
   - Login dengan username/email dan password

2. **Belanja**
   - Lihat produk di halaman home
   - Gunakan search untuk mencari produk tertentu
   - Filter berdasarkan kategori
   - Klik "Lihat Detail" untuk melihat produk lengkap
   - Tentukan jumlah dan alamat pengiriman
   - Klik "Beli Sekarang"

### Sebagai Penjual (Seller)

1. **Upload Produk**
   - Klik tombol "+ Jual" di navbar
   - Isi informasi produk:
     - Judul produk
     - Kategori
     - Deskripsi detail
     - Harga
     - Stok
     - Upload foto produk
   - Klik "Posting Produk"

2. **Kelola Produk**
   - Klik "Produk Saya" di navbar
   - Lihat semua produk yang sudah diupload
   - Klik "Edit" untuk mengubah produk
   - Klik "Hapus" untuk menghapus produk

3. **Monitor Penjualan**
   - Klik "Dashboard" di navbar
   - Lihat statistik:
     - Total produk
     - Produk aktif
     - Produk terjual
     - Total pendapatan
   - Lihat riwayat transaksi terbaru

4. **Edit Profil**
   - Klik "Edit Profil" di dashboard
   - Update informasi:
     - Nama lengkap
     - Nomor telepon
     - Alamat
     - Kota, provinsi, kode pos

### Fitur Review & Rating (NEW) ⭐

**Sebagai Pembeli:**
1. Setelah membeli produk, scroll ke bagian "Ulasan Produk"
2. Isi form review dengan:
   - Rating (1-5 bintang)
   - Teks ulasan (opsional tapi recommended)
3. Klik "Kirim Ulasan"
4. Ulasan akan langsung terupdate di halaman
5. Rating pembeli akan otomatis naikkan rating penjual

**Batasan:**
- Setiap pembeli hanya bisa 1x review per produk
- Sistem akan mencegah review ganda
- Review harus terisi dengan rating

### Fitur Pembayaran Multi-Metode (NEW) ⭐

**Proses Pembayaran:**
1. Saat checkout, pilih metode pembayaran:
   - 🏦 Transfer Bank
   - 💳 Kartu Kredit
   - 📱 E-Wallet (GCash/Dana)
   - 💵 Bayar di Tempat (COD)
2. Klik "Beli Sekarang"
3. Redirect ke halaman pembayaran
4. Lihat instruksi pembayaran sesuai metode
5. Isi bukti pembayaran
6. Klik "Konfirmasi Pembayaran"

### Sistem Kredit Score & Anti-Fraud (NEW) ⭐

**Untuk Pembeli yang Ingin Lapor:**
1. Klik menu "⚠️ Lapor Fraud" di navbar
2. Pilih ID penjual yang ingin dilaporkan
3. Isi alasan pelaporan detail
4. Klik "🚨 Lapor Fraud"

**Sistem Otomatis:**
- Setiap laporan menambah fraud_count penjual +1
- Credit score penjual akan turun
- Jika fraud_count mencapai 3x: **Penjual otomatis DI-BAN**
- Penjual yang di-ban tidak bisa lagi berjualan
- Semua orang bisa melihat status kredit di halaman "Lapor Fraud"

**Monitoring:**
- Lihat tabel "Status Kredit Penjual"
- Lihat progress bar credit score setiap penjual
- Status 🚫 DI-BAN berarti penjual sudah tidak bisa berjualan

## 🔐 Keamanan

Aplikasi ini menggunakan:
- ✅ Password hashing dengan `password_hash()` PHP (bcrypt)
- ✅ SQL injection prevention dengan prepared statements
- ✅ XSS protection dengan `htmlspecialchars()`
- ✅ Session management yang aman
- ✅ CSRF token ready (dapat ditambahkan)

## 🎨 Tampilan & Design

- **Responsive Design**: Mobile-first approach
- **Modern UI**: Gradient colors, shadows, dan transitions
- **User-Friendly**: Navigasi yang mudah dipahami
- **Fast Loading**: CSS terotomasi dan minimized
- **Accessibility**: Semantic HTML dan WCAG compliant

## 📱 Responsive Breakpoints

- **Desktop**: > 1024px
- **Tablet**: 768px - 1024px
- **Mobile**: < 768px
- **Small Mobile**: < 480px

## 🐛 Troubleshooting

### Error: "Koneksi gagal"
- Cek apakah MySQL sudah running di XAMPP
- Cek username dan password di `config.php`
- Cek apakah database `ecommerce_db` sudah dibuat

### Error: "No data received"
- Cek apakah file `.php` berada di folder yang benar
- Cek permission folder `uploads/products`
- Cek error log di XAMPP

### Upload gambar gagal
- Cek ukuran gambar (max 5MB)
- Cek format gambar (JPG, PNG, GIF)
- Cek permission folder `uploads/products`

### Foto produk tidak tampil
- Cek path di database
- Cek apakah file masih ada di folder `uploads/products`
- Cek permission file

## 📝 Database Schema

### Tabel `users`
- id (PK)
- username (UNIQUE)
- email (UNIQUE)
- password
- full_name
- phone
- address
- city
- province
- postal_code
- profile_picture
- rating
- total_sales
- created_at
- updated_at

### Tabel `products`
- id (PK)
- seller_id (FK)
- title
- description
- price
- category
- image_url
- stock
- status (active/sold/inactive)
- views
- created_at
- updated_at

### Tabel `transactions`
- id (PK)
- product_id (FK)
- buyer_id (FK)
- seller_id (FK)
- quantity
- total_price
- status (pending/paid/shipped/completed/cancelled)
- payment_method
- shipping_address
- notes
- created_at
- updated_at

### Tabel `reviews`
- id (PK)
- product_id (FK)
- buyer_id (FK)
- seller_id (FK)
- rating (1-5)
- review_text
- created_at

### Tabel `messages`
- id (PK)
- sender_id (FK)
- receiver_id (FK)
- product_id (FK)
- message
- is_read
- created_at

## 🚀 Fitur Pengembangan Ke Depan

- 💳 Integrasi payment gateway (Midtrans, GoPay, etc)
- 💬 Real-time chat antara pembeli dan penjual
- 📦 Tracking pengiriman
- 🔔 Notifikasi real-time
- ⭐ Advanced review system dengan foto
- 🔍 Advanced search dengan AI recommendation
- 📊 Analytics dashboard untuk seller
- 🛒 Shopping cart functionality
- 🎁 Coupon dan discount system
- 📧 Email notification system
- 🔐 Two-factor authentication

## 📞 Support

Jika ada pertanyaan atau masalah, silakan cek:
1. Folder `includes/db.sql` untuk struktur database
2. File `includes/config.php` untuk konfigurasi
3. Console browser (F12) untuk error JavaScript
4. XAMPP error log untuk PHP errors

## 📄 License

Free to use for commercial and personal projects.

## 👨‍💻 Version

**v1.0.0** - Initial Release
- Login & Registration
- Product Management
- Shopping System
- Dashboard & Statistics
- Profile Management

---

**Created**: January 2026
**Last Updated**: January 25, 2026

Selamat menggunakan MarketHub! 🎉
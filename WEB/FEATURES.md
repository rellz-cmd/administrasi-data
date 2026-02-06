# 🎯 Fitur-Fitur Baru MarketHub

## 1. 📊 Sistem Kredit Score & Anti-Fraud

### Deskripsi
Sistem untuk melindungi pembeli dari penjual yang tidak jujur. Setiap penjual memiliki credit score yang dapat turun jika menerima laporan fraud/kecurangan.

### Detail Fitur

**Credit Score:**
- Awal: 100 poin per penjual
- Turun jika ada laporan fraud
- Progress bar visual di dashboard dan halaman lapor fraud
- Status: ✓ Aktif, Kuning (60-80), Merah (<60), 🚫 DI-BAN

**Fraud Count:**
- Tracking jumlah laporan kecurangan
- Otomatis +1 setiap kali ada laporan
- Otomatis BAN jika mencapai 3x
- Tidak bisa di-reset (permanen)

**Auto-Ban:**
- Jika fraud_count === 3 → Akun DI-BAN otomatis
- Penjual yang di-ban tidak bisa lagi berjualan
- Tidak bisa upload produk baru
- Pesan warning di dashboard mereka
- Terlihat 🚫 DI-BAN di halaman lapor fraud

### File Terlibat
- `report_fraud.php` - Halaman untuk lapor fraud
- `includes/config.php` - Fungsi `addFraudReport()`, `isUserBanned()`
- `includes/db.sql` - Kolom `credit_score`, `fraud_count`, `is_banned`
- `dashboard.php` - Display credit score status

### Cara Pakai

**Sebagai Pembeli:**
1. Klik menu "⚠️ Lapor Fraud" di navbar
2. Masukkan ID penjual yang bermasalah
3. Isi alasan pelaporan detail
4. Klik "🚨 Lapor Fraud"
5. Lihat tabel untuk tracking fraud count penjual

---

## 2. ⭐ Sistem Review & Rating Pembeli

### Deskripsi
Pembeli dapat memberikan ulasan dan rating (1-5 bintang) untuk produk yang telah dibeli. Rating akan otomatis update rating penjual.

### Detail Fitur

**Review Form:**
- Rating: 1-5 bintang (required)
- Teks ulasan: Opsional tapi recommended
- Validation: Tidak boleh review 2x untuk produk yang sama
- Automatic rating update untuk penjual

**Display Reviews:**
- Tampil di bagian bawah product detail page
- Sorted by newest first
- Menampilkan: nama pembeli, rating, teks, tanggal
- Update otomatis setelah submit

**Rating System:**
- Seller rating = Average dari semua reviews
- Rating float, tampil 1 desimal (contoh: 4.5)
- Langsung update di navbar dan product card

### File Terlibat
- `product_detail.php` - Display review form & reviews list
- `includes/config.php` - Fungsi `submitReview()`, `getProductReviews()`, `updateSellerRating()`
- `includes/db.sql` - Tabel `reviews` (sudah ada, hanya optimasi)
- `assets/css/style.css` - Styling review form

### Cara Pakai

**Sebagai Pembeli:**
1. Scroll ke section "Ulasan Produk" di product detail page
2. Isi form:
   - Pilih rating (1-5 bintang)
   - Tulis ulasan (optional)
3. Klik "Kirim Ulasan"
4. Ulasan langsung tampil di halaman
5. Rating penjual otomatis terupdate

**Batasan:**
- 1 pembeli = 1 review per produk (tidak bisa double)
- Jika sudah review akan dapat pesan "Anda sudah memberikan ulasan"

---

## 3. 💳 Sistem Pembayaran Multi-Metode

### Deskripsi
Pembeli dapat memilih dari berbagai metode pembayaran saat checkout. Setiap metode memiliki instruksi pembayaran yang spesifik.

### Metode Pembayaran

1. **🏦 Transfer Bank**
   - Instruksi: Bank BCA, No Rekening, Nominal
   - Status: Ready
   - Simulasi: Manual confirm

2. **💳 Kartu Kredit**
   - Instruksi: Gateway payment ready
   - Status: Integration ready
   - Simulasi: Form input

3. **📱 E-Wallet (GCash/Dana)**
   - Instruksi: Nomor e-wallet, Nominal
   - Status: Ready
   - Simulasi: Manual confirm

4. **💵 Bayar di Tempat (COD)**
   - Instruksi: Pembayaran saat barang tiba
   - Status: Ready
   - Simulasi: Pembayaran manual

### Detail Fitur

**Payment Form:**
- Pilih metode pembayaran saat checkout
- Quantity & shipping address
- Catatan (notes) optional
- Submit = buat transaction + redirect ke payment

**Payment Page:**
- Ringkasan pesanan (foto, judul, qty, total)
- Status pesanan realtime
- Instruksi pembayaran per metode
- Form bukti pembayaran
- Konfirmasi payment status

**Payment Status:**
- unpaid: Default state
- paid: Setelah confirm pembayaran
- failed: Jika ada error

### File Terlibat
- `payment.php` - Halaman pembayaran (NEW)
- `product_detail.php` - Updated form checkout
- `includes/config.php` - Fungsi `getPaymentMethods()`, `updatePaymentStatus()`
- `includes/db.sql` - Kolom `payment_status` di transactions
- `assets/css/style.css` - Styling payment page

### Cara Pakai

**Sebagai Pembeli:**
1. Di product detail page, isi form:
   - Jumlah barang
   - Pilih metode pembayaran ⭐ NEW
   - Alamat pengiriman
   - Catatan (optional)
2. Klik "🛒 Beli Sekarang"
3. Redirect ke halaman pembayaran
4. Lihat instruksi pembayaran sesuai metode
5. Lakukan pembayaran
6. Isi bukti pembayaran
7. Klik "✓ Konfirmasi Pembayaran"
8. Status berubah ke "Dibayar"

---

## 📋 Database Changes

### Kolom Baru di Table `users`
```sql
credit_score INT DEFAULT 100,
fraud_count INT DEFAULT 0,
is_banned BOOLEAN DEFAULT FALSE
```

### Kolom Baru di Table `transactions`
```sql
payment_status ENUM('unpaid', 'paid', 'failed') DEFAULT 'unpaid'
```

---

## 🔗 Navigation Links

Menu baru di navbar:
- **⚠️ Lapor Fraud** - Akses dari manapun untuk lapor penjual curang

---

## 💻 Function Baru di config.php

```php
// Fungsi anti-fraud
isUserBanned($user_id)              // Cek apakah user di-ban
addFraudReport($user_id, $reason)   // Tambah fraud report

// Fungsi credit score
updateCreditScore($user_id, $points) // Update credit score

// Fungsi review
submitReview(...)                    // Submit review
getProductReviews($product_id)      // Get reviews produk
updateSellerRating($seller_id)      // Update rating penjual

// Fungsi payment
getPaymentMethods()                  // Get list metode bayar
updatePaymentStatus(...)             // Update status pembayaran
```

---

## ✅ Testing Checklist

### Credit Score & Fraud
- [ ] Buka report_fraud.php
- [ ] Lapor seller dengan ID valid
- [ ] Cek fraud_count naik +1
- [ ] Credit score turun
- [ ] Lapor 3x untuk auto-ban
- [ ] Lihat status 🚫 DI-BAN
- [ ] Verify seller di-ban di dashboard mereka

### Review System
- [ ] Buka product detail
- [ ] Isi review form
- [ ] Submit review
- [ ] Lihat review tampil di halaman
- [ ] Cek seller rating update
- [ ] Coba review 2x = error

### Payment
- [ ] Beli produk
- [ ] Pilih payment method
- [ ] Lihat instruksi payment
- [ ] Submit bukti pembayaran
- [ ] Status berubah ke "Dibayar"
- [ ] Test semua 4 metode pembayaran

---

## 🚀 Deployment Notes

1. **Update Database:**
   - Re-import `includes/db.sql` atau
   - Manual run ALTER TABLE commands

2. **File Baru:**
   - `payment.php` - Pastikan sudah ter-copy
   - `report_fraud.php` - Pastikan sudah ter-copy

3. **CSS Updates:**
   - Sudah include di `assets/css/style.css`
   - No separate CSS file needed

4. **Config Updates:**
   - Fungsi-fungsi baru sudah di `includes/config.php`
   - Backward compatible dengan kode lama

---

## 📞 Support

Jika ada pertanyaan atau error, hubungi:
- Email: support@markethub.com
- Issue: Report via admin panel

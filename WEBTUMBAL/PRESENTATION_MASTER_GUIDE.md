# 🎓 PANDUAN MASTER PRESENTASI MARKETHUB

Dokumen ini berisi strategi langkah-demi-langkah untuk mempresentasikan proyek MarketHub dengan percaya diri, mulai dari sikap tubuh hingga penjelasan teknis kode.

---

## 1️⃣ BAGIAN 1: ETIKA & TEKNIK PENYAMPAIAN (SOFT SKILLS)

Sebelum masuk ke kode, cara Anda berdiri dan berbicara menentukan 50% kesuksesan.

### 🔹 Sikap Tubuh & Suara
1.  **Posisi Berdiri:** Jangan bersembunyi di balik layar laptop. Berdirilah tegak, sedikit serong menghadap audiens dan layar.
2.  **Kontak Mata:** Jangan menatap kode terus menerus. Tataplah penguji/audiens saat menjelaskan konsep, lalu lihat layar hanya saat menunjuk baris kode tertentu.
3.  **Intonasi Suara:**
    *   Gunakan nada **antusias** saat membuka presentasi.
    *   Gunakan nada **serius & pelan** saat menjelaskan logika keamanan (Fraud/Transaction).
    *   Tekankan kata kunci: *"Otomatis"*, *"Aman"*, *"Terstruktur"*, *"Modular"*.

### 🔹 Filosofi "Tunjukkan, Jangan Hanya Cerita"
Selalu ikuti urutan ini:
1.  **Masalah:** "User sering tertipu penjual nakal."
2.  **Solusi (Demo):** "Maka saya buat fitur Lapor Fraud." (Demokan fitur di browser).
3.  **Bukti (Kode):** "Mari kita lihat bagaimana kodenya bekerja di belakang layar." (Buka VS Code).

---

## 2️⃣ BAGIAN 2: SKENARIO PENJELASAN KODE (HARD SKILLS)

Ini adalah naskah rahasia Anda saat membuka file kode tertentu.

### 🟢 Skenario 1: Menjelaskan Struktur & Kerapian (`includes/header.php`)
*Buka file ini di awal untuk menunjukkan bahwa Anda programmer yang rapi.*

**🗣️ Narasi:**
"Sebelum masuk ke fitur kompleks, saya ingin menunjukkan struktur dasar aplikasi ini. Saya menerapkan prinsip **DRY (Don't Repeat Yourself)**. Lihat file `header.php` ini. Semua navigasi dan logika session user saya pusatkan di sini. Jadi, jika saya ingin mengubah menu, saya cukup ubah satu file, dan ratusan halaman lain akan otomatis terupdate. Ini membuat kode saya sangat *maintainable* (mudah dirawat)."

---

### 🟢 Skenario 2: Menjelaskan Fitur Payment (`includes/config.php` & `payment.php`)
*Tujuannya: Menunjukkan kemampuan abstraksi data.*

**Langkah:**
1.  Buka `includes/config.php` dan tunjukkan fungsi `getPaymentMethods()`.

**🗣️ Narasi:**
"Untuk sistem pembayaran, saya tidak menuliskannya secara manual (hardcode) di HTML. Saya menyimpannya dalam bentuk Array di `config.php`.

Kenapa? Karena jika besok bos meminta menambah metode pembayaran baru seperti 'OVO' atau 'GoPay', saya **tidak perlu menyentuh logika tampilan**. Saya cukup tambahkan satu baris array di sini, dan sistem akan otomatis merender tampilannya. Ini membuat aplikasi sangat fleksibel."

---

### 🟢 Skenario 3: Menjelaskan Keamanan Transaksi (`report_fraud.php`)
*Tujuannya: Menunjukkan pemahaman tentang integritas data database.*

**Langkah:**
1.  Buka `report_fraud.php`.
2.  Scroll ke bagian `$conn->begin_transaction();`.

**🗣️ Narasi:**
"Fitur keamanan adalah prioritas saya. Di fitur pelaporan fraud ini, ada tiga hal yang terjadi sekaligus:
1.  Menyimpan laporan.
2.  Menambah counter fraud penjual.
3.  Mengurangi skor kredit penjual.

Di sini (tunjuk baris `begin_transaction`), saya menggunakan **Database Transaction**. Ini menjamin bahwa ketiga proses tersebut harus berhasil semua. Jika satu saja gagal (misalnya koneksi putus saat update skor), maka **semua perubahan akan dibatalkan (Rollback)**. Ini mencegah data menjadi korup atau tidak sinkron."

---

### 🟢 Skenario 4: Menjelaskan Logika Bisnis / Auto-Ban (`report_fraud.php`)
*Tujuannya: Menunjukkan kemampuan logika pemrograman.*

**Langkah:**
1.  Tetap di `report_fraud.php`, scroll ke bawah sedikit (bagian pengecekan `fraud_count`).

**🗣️ Narasi:**
"Sistem ini tidak hanya mencatat, tapi juga bertindak. Perhatikan logika `if` di sini.

Sistem secara otomatis mengecek: Jika jumlah fraud (`fraud_count`) sudah mencapai 3 kali, sistem langsung mengeksekusi query `UPDATE users SET is_banned = 1`. Penjual tersebut langsung kehilangan akses saat itu juga tanpa perlu admin memantau 24 jam. Ini adalah bentuk efisiensi sistem."

---

## 3️⃣ BAGIAN 3: MENJAWAB PERTANYAAN JEBAKAN

Penguji sering bertanya "Bagaimana jika..." untuk menguji pemahaman Anda.

**Q: "Kenapa codingan kamu dipisah-pisah jadi banyak file (header, footer, config)? Kenapa gak digabung aja biar gampang bacanya?"**
**A:** "Justru memisahkannya membuat kode lebih mudah dibaca dan dikelola dalam jangka panjang, Pak/Bu. Jika digabung, satu file bisa ribuan baris. Dengan dipisah (Modular), jika ada error di bagian menu, saya tahu pasti saya hanya perlu cek `header.php`, tidak perlu mencari di seluruh kode."

**Q: "Apakah sistem ini aman dari SQL Injection?"**
**A:** "Sangat aman. Jika Bapak/Ibu lihat di semua query database saya (tunjukkan salah satu `$stmt = $conn->prepare...`), saya selalu menggunakan **Prepared Statements**. Saya tidak pernah memasukkan input user mentah langsung ke dalam query SQL. Ini menutup celah bagi peretas untuk memanipulasi database."

**Q: "Gimana kalau saya mau ganti warna bar Credit Score jadi Biru kalau nilainya 100?"**
**A:** (Senyum percaya diri) "Mudah sekali. Kita tinggal buka `dashboard.php`, cari logika pewarnaan CSS-nya, dan tambahkan satu kondisi `if ($score == 100) echo 'blue';`. Karena logikanya ada di PHP, perubahan visualnya dinamis."

---

## 4️⃣ CHECKLIST SEBELUM MAJU

1.  [ ] **Buka XAMPP:** Pastikan Apache & MySQL sudah Start (Hijau).
2.  [ ] **Buka Browser:** Siapkan tab MarketHub (sudah login) dan phpMyAdmin.
3.  [ ] **Buka VS Code:**
    *   Tutup semua tab file yang tidak perlu.
    *   Buka file kunci: `config.php`, `payment.php`, `report_fraud.php` agar siap diklik.
4.  [ ] **Bersihkan Desktop:** Sembunyikan file-file sampah agar tampilan proyektor bersih.
5.  [ ] **Tarik Napas:** Anda yang membuat kodenya, Anda yang paling mengerti.

---

**Kata Penutup Presentasi:**
*"Demikian presentasi MarketHub. Aplikasi ini saya bangun bukan hanya untuk sekedar jalan, tapi dengan memperhatikan struktur kode yang baik, keamanan data, dan kemudahan pengembangan di masa depan. Terima kasih."*
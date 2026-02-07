# KantinYuk - Aplikasi Pemesanan Makanan

## Struktur File
- `app.py` - File utama aplikasi (Flask routes)
- `database.py` - Fungsi database & user management
- `menu_data.py` - Data menu makanan & minuman
- `templates.py` - Semua HTML templates
- `requirements.txt` - Dependencies yang diperlukan

## Cara Menjalankan

1. Install dependencies:
   ```
   pip install flask
   ```

2. Jalankan aplikasi:
   ```
   python app.py
   ```

3. Buka browser dan akses:
   ```
   http://localhost:5000
   ```

## Fitur Utama
✅ Register & Login (Wajib daftar terlebih dahulu)
✅ Kategori Menu: Makanan Berat, Makanan Ringan, Minuman
✅ Keranjang Belanja Persistent (Tersimpan di database)
✅ Checkout dengan Data Diri Pemesan
✅ Pembayaran: QRIS & Cash
✅ Design Web Komersial dengan Background Biru Dongker
✅ Hanya Python (Flask) - Tidak menggunakan bahasa lain
✅ Maksimal 4 file utama (app, database, menu_data, templates)

## Database
Menggunakan SQLite (`kantin.db`) dengan tabel:
- `users` - Menyimpan username & password terenkripsi
- `keranjang` - Menyimpan item keranjang belanja

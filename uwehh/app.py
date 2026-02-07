from flask import Flask, render_template_string, request, session, redirect, url_for, flash
import os
import base64
from io import BytesIO
import qrcode
from database import init_db, get_keranjang, save_keranjang, register_user, login_user, check_user_exists
from menu_data import menu, get_menu_items
from templates import (home_template, login_template, register_template, makanan_template, 
                       menu_template, keranjang_template, checkout_template, pembayaran_template)

app = Flask(__name__)
app.secret_key = os.urandom(24)

init_db()

# Function untuk generate QR code
def generate_qr_code(data):
    """Generate QR code dan return sebagai base64 string"""
    qr = qrcode.QRCode(
        version=1,
        error_correction=qrcode.constants.ERROR_CORRECT_L,
        box_size=10,
        border=4,
    )
    qr.add_data(data)
    qr.make(fit=True)
    img = qr.make_image(fill_color="black", back_color="white")
    
    # Convert ke base64 untuk embed di HTML
    img_io = BytesIO()
    img.save(img_io, 'PNG')
    img_io.seek(0)
    img_base64 = base64.b64encode(img_io.getvalue()).decode()
    return f"data:image/png;base64,{img_base64}"

# HALAMAN UTAMA
@app.route('/')
def home():
    if 'username' not in session:
        return redirect(url_for('login'))
    return render_template_string(home_template)

# LOGIN
@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form.get('username')
        password = request.form.get('password')
        if login_user(username, password):
            session['username'] = username
            flash('Login berhasil!', 'success')
            return redirect(url_for('home'))
        flash('Username atau password salah!', 'error')
    return render_template_string(login_template)

# REGISTER
@app.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        username = request.form.get('username')
        password = request.form.get('password')
        if not username or not password:
            flash('Username dan password harus diisi!', 'error')
        elif check_user_exists(username):
            flash('Username sudah terdaftar!', 'error')
        else:
            register_user(username, password)
            flash('Registrasi berhasil! Silakan login.', 'success')
            return redirect(url_for('login'))
    return render_template_string(register_template)

# MAKANAN KATEGORI
@app.route('/makanan')
def makanan():
    if 'username' not in session:
        return redirect(url_for('login'))
    return render_template_string(makanan_template)

# MENU ITEMS
@app.route('/menu/<kategori>/<subkategori>')
def menu_items(kategori, subkategori):
    if 'username' not in session:
        return redirect(url_for('login'))
    items = get_menu_items(kategori, subkategori)
    title = f"{kategori.capitalize()} - {subkategori.capitalize()}"
    return render_template_string(menu_template, kategori=title, items=items)

# MINUMAN
@app.route('/minuman')
def minuman():
    if 'username' not in session:
        return redirect(url_for('login'))
    items = get_menu_items('minuman', None)
    return render_template_string(menu_template, kategori="Minuman", items=items)

# TAMBAH KE KERANJANG
@app.route('/tambah_ke_keranjang', methods=['POST'])
def tambah_ke_keranjang():
    if 'username' not in session:
        return redirect(url_for('login'))
    nama = request.form.get('nama')
    harga = int(request.form.get('harga'))
    quantity = int(request.form.get('quantity'))
    keranjang = get_keranjang()
    
    item_exists = False
    for item in keranjang:
        if item['nama'] == nama:
            item['quantity'] += quantity
            item_exists = True
            break
    
    if not item_exists:
        keranjang.append({'nama': nama, 'harga': harga, 'quantity': quantity})
    
    save_keranjang(keranjang)
    flash(f'{nama} ditambahkan ke keranjang!', 'success')
    return redirect(request.referrer or url_for('home'))

# LIHAT KERANJANG
@app.route('/keranjang')
def keranjang():
    if 'username' not in session:
        return redirect(url_for('login'))
    keranjang_items = get_keranjang()
    total = sum(item['harga'] * item['quantity'] for item in keranjang_items)
    return render_template_string(keranjang_template, keranjang=keranjang_items, total=total)

# HAPUS DARI KERANJANG
@app.route('/hapus_dari_keranjang/<nama>')
def hapus_dari_keranjang(nama):
    if 'username' not in session:
        return redirect(url_for('login'))
    keranjang = get_keranjang()
    keranjang = [item for item in keranjang if item['nama'] != nama]
    save_keranjang(keranjang)
    flash(f'{nama} dihapus dari keranjang!', 'success')
    return redirect(url_for('keranjang'))

# KOSONGKAN KERANJANG
@app.route('/kosongkan_keranjang')
def kosongkan_keranjang():
    if 'username' not in session:
        return redirect(url_for('login'))
    save_keranjang([])
    flash('Keranjang dikosongkan!', 'success')
    return redirect(url_for('keranjang'))

# CHECKOUT
@app.route('/checkout')
def checkout():
    if 'username' not in session:
        return redirect(url_for('login'))
    keranjang_items = get_keranjang()
    if not keranjang_items:
        flash('Keranjang kosong!', 'error')
        return redirect(url_for('keranjang'))
    total = sum(item['harga'] * item['quantity'] for item in keranjang_items)
    return render_template_string(checkout_template, keranjang=keranjang_items, total=total)

# BAYAR
@app.route('/bayar', methods=['POST'])
def bayar():
    if 'username' not in session:
        return redirect(url_for('login'))
    nama = request.form.get('nama')
    telepon = request.form.get('telepon')
    metode = request.form.get('metode')
    
    # Generate QR code hanya jika metode QRIS
    qr_code = None
    if metode == 'QRIS':
        # Data QR code (bisa disesuaikan dengan format QRIS yang sesuai)
        qr_data = f"https://kantinyuk.app/payment/{nama}/{telepon}"
        qr_code = generate_qr_code(qr_data)
    
    save_keranjang([])
    flash('Pembayaran berhasil!', 'success')
    return render_template_string(pembayaran_template, nama=nama, telepon=telepon, metode=metode, qr_code=qr_code)

# LOGOUT
@app.route('/logout')
def logout():
    session.pop('username', None)
    flash('Logout berhasil!', 'success')
    return redirect(url_for('login'))

if __name__ == '__main__':
    app.run(debug=True, port=5000)

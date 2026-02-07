home_template = """
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>KantinYuk - Pemesanan Online</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: #333;
            min-height: 100vh;
            padding: 20px;
        }
        .navbar {
            background: rgba(0, 0, 0, 0.8);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .navbar-content {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        .navbar h1 { color: #fff; font-size: 24px; }
        .navbar a { color: #fff; text-decoration: none; margin: 0 15px; font-weight: 500; }
        .navbar a:hover { color: #ffd700; }
        .container {
            max-width: 1000px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h1 { color: #1a4d7a; text-align: center; margin-bottom: 30px; font-size: 32px; }
        h2 { color: #0d2d47; margin: 20px 0 15px; font-size: 22px; }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .menu-card {
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            text-decoration: none;
            display: block;
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        .menu-card h3 { font-size: 20px; margin-bottom: 10px; }
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        a.btn, button { 
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }
        a.btn:hover, button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .success-msg { 
            background: #4caf50; 
            color: white; 
            padding: 10px; 
            border-radius: 5px; 
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-content">
            <h1>🍜 KantinYuk</h1>
            <div>
                <a href="/keranjang">🛒 Keranjang</a>
                <a href="/logout">Logout</a>
            </div>
        </div>
    </div>
    <div class="container">
        <h1>Selamat Datang di KantinYuk</h1>
        {% with messages = get_flashed_messages() %}
            {% if messages %}
                {% for message in messages %}
                    <div class="success-msg">{{ message }}</div>
                {% endfor %}
            {% endif %}
        {% endwith %}
        <h2>Kategori Menu:</h2>
        <div class="menu-grid">
            <a href="/makanan" class="menu-card">
                <h3>🍚 Makanan</h3>
                <p>Makanan Berat & Ringan</p>
            </a>
            <a href="/minuman" class="menu-card">
                <h3>🥤 Minuman</h3>
                <p>Berbagai pilihan minuman</p>
            </a>
        </div>
    </div>
</body>
</html>
"""

login_template = """
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login - KantinYuk</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: #333;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
        }
        h1 { color: #1a4d7a; text-align: center; margin-bottom: 30px; }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #1a4d7a;
            border-radius: 8px;
            font-size: 14px;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #0d2d47;
            box-shadow: 0 0 5px rgba(26, 77, 122, 0.3);
        }
        button { 
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 15px;
            transition: all 0.3s;
        }
        button:hover { transform: scale(1.02); box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .link { text-align: center; margin-top: 15px; }
        .link a { color: #1a4d7a; text-decoration: none; font-weight: 600; }
        .link a:hover { color: #0d2d47; }
        .error { background: #f44336; color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🍜 KantinYuk</h1>
        {% with messages = get_flashed_messages() %}
            {% if messages %}
                {% for message in messages %}
                    <div class="error">{{ message }}</div>
                {% endfor %}
            {% endif %}
        {% endwith %}
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Login</button>
        </form>
        <div class="link">Belum punya akun? <a href="/register">Daftar di sini</a></div>
    </div>
</body>
</html>
"""

register_template = """
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register - KantinYuk</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: #333;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
        }
        h1 { color: #1a4d7a; text-align: center; margin-bottom: 30px; }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #1a4d7a;
            border-radius: 8px;
            font-size: 14px;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #0d2d47;
            box-shadow: 0 0 5px rgba(26, 77, 122, 0.3);
        }
        button { 
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 15px;
            transition: all 0.3s;
        }
        button:hover { transform: scale(1.02); box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .link { text-align: center; margin-top: 15px; }
        .link a { color: #1a4d7a; text-decoration: none; font-weight: 600; }
        .link a:hover { color: #0d2d47; }
        .error { background: #f44336; color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🍜 KantinYuk</h1>
        <h2 style="font-size: 18px; color: #1a4d7a; text-align: center; margin-bottom: 20px;">Daftar Akun Baru</h2>
        {% with messages = get_flashed_messages() %}
            {% if messages %}
                {% for message in messages %}
                    <div class="error">{{ message }}</div>
                {% endfor %}
            {% endif %}
        {% endwith %}
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Daftar</button>
        </form>
        <div class="link">Sudah punya akun? <a href="/login">Login di sini</a></div>
    </div>
</body>
</html>
"""

makanan_template = """
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Makanan - KantinYuk</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: #333;
            min-height: 100vh;
            padding: 20px;
        }
        .navbar {
            background: rgba(0, 0, 0, 0.8);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .navbar-content {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        .navbar h1 { color: #fff; font-size: 24px; }
        .navbar a { color: #fff; text-decoration: none; margin: 0 15px; font-weight: 500; }
        .navbar a:hover { color: #ffd700; }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h1 { color: #1a4d7a; margin-bottom: 30px; }
        .subcategory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .subcat-card {
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
            display: block;
        }
        .subcat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
        .subcat-card h3 { font-size: 18px; }
        a.btn { 
            display: inline-block;
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 20px;
        }
        a.btn:hover { transform: scale(1.05); }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-content">
            <h1>🍜 KantinYuk</h1>
            <div>
                <a href="/keranjang">🛒 Keranjang</a>
                <a href="/logout">Logout</a>
            </div>
        </div>
    </div>
    <div class="container">
        <h1>🍚 Makanan</h1>
        <div class="subcategory-grid">
            <a href="/menu/makanan/berat" class="subcat-card">
                <h3>Makanan Berat</h3>
                <p>Nasi, Mie, Bakso</p>
            </a>
            <a href="/menu/makanan/ringan" class="subcat-card">
                <h3>Makanan Ringan</h3>
                <p>Snack & Cemilan</p>
            </a>
        </div>
        <a href="/" class="btn">← Kembali ke Beranda</a>
    </div>
</body>
</html>
"""

menu_template = """
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ kategori }} - KantinYuk</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: #333;
            min-height: 100vh;
            padding: 20px;
        }
        .navbar {
            background: rgba(0, 0, 0, 0.8);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar-content {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        .navbar h1 { color: #fff; font-size: 24px; }
        .navbar a { color: #fff; text-decoration: none; margin: 0 15px; }
        .navbar a:hover { color: #ffd700; }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h1 { color: #1a4d7a; margin-bottom: 30px; font-size: 28px; }
        .menu-list {
            display: grid;
            gap: 20px;
        }
        .menu-item {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .item-info { flex: 1; }
        .item-name { font-weight: 600; font-size: 16px; color: #1a4d7a; }
        .item-price { color: #d32f2f; font-weight: bold; font-size: 18px; margin-top: 5px; }
        .item-form { display: flex; gap: 10px; align-items: center; }
        input[type="number"] { width: 60px; padding: 8px; border: 1px solid #1a4d7a; border-radius: 5px; }
        button { 
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        button:hover { transform: scale(1.05); }
        a.btn { 
            display: inline-block;
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-content">
            <h1>🍜 KantinYuk</h1>
            <div>
                <a href="/keranjang">🛒 Keranjang</a>
                <a href="/logout">Logout</a>
            </div>
        </div>
    </div>
    <div class="container">
        <h1>{{ kategori }}</h1>
        <div class="menu-list">
            {% for item in items %}
            <div class="menu-item">
                <div class="item-info">
                    <div class="item-name">{{ item.nama }}</div>
                    <div class="item-price">Rp {{ item.harga }}</div>
                </div>
                <form method="POST" action="/tambah_ke_keranjang" class="item-form">
                    <input type="hidden" name="nama" value="{{ item.nama }}">
                    <input type="hidden" name="harga" value="{{ item.harga }}">
                    <input type="number" name="quantity" value="1" min="1">
                    <button type="submit">Tambah</button>
                </form>
            </div>
            {% endfor %}
        </div>
        <a href="/" class="btn">← Kembali ke Beranda</a>
    </div>
</body>
</html>
"""

keranjang_template = """
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Keranjang - KantinYuk</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: #333;
            min-height: 100vh;
            padding: 20px;
        }
        .navbar {
            background: rgba(0, 0, 0, 0.8);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar-content {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }
        .navbar h1 { color: #fff; }
        .navbar a { color: #fff; text-decoration: none; margin: 0 15px; }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h1 { color: #1a4d7a; margin-bottom: 30px; }
        .cart-item {
            background: #f5f5f5;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .item-details { flex: 1; }
        .item-total { color: #d32f2f; font-weight: bold; font-size: 16px; }
        .delete-btn {
            background: #f44336;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-btn:hover { background: #d32f2f; }
        .total-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #1a4d7a;
            text-align: right;
        }
        .total { color: #d32f2f; font-weight: bold; font-size: 24px; }
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        a.btn, button { 
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            font-weight: 600;
        }
        a.btn:hover, button:hover { transform: scale(1.05); }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-content">
            <h1>🍜 KantinYuk</h1>
            <a href="/logout">Logout</a>
        </div>
    </div>
    <div class="container">
        <h1>🛒 Keranjang Belanja</h1>
        {% if keranjang %}
            {% for item in keranjang %}
            <div class="cart-item">
                <div class="item-details">
                    <strong>{{ item.nama }}</strong><br>
                    Rp {{ item.harga }} x {{ item.quantity }}
                </div>
                <div style="text-align: right;">
                    <div class="item-total">Rp {{ item.harga * item.quantity }}</div>
                    <a href="/hapus_dari_keranjang/{{ item.nama }}" class="delete-btn">Hapus</a>
                </div>
            </div>
            {% endfor %}
            <div class="total-section">
                <p class="total">Total: Rp {{ total }}</p>
            </div>
            <div class="button-group">
                <a href="/" class="btn">← Lanjut Belanja</a>
                <a href="/checkout" class="btn">Checkout →</a>
                <a href="/kosongkan_keranjang" class="btn">Kosongkan</a>
            </div>
        {% else %}
        <p style="text-align: center; font-size: 16px; color: #666;">Keranjang Anda kosong</p>
        <div style="text-align: center; margin-top: 20px;">
            <a href="/" class="btn">← Mulai Belanja</a>
        </div>
        {% endif %}
    </div>
</body>
</html>
"""

checkout_template = """
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Checkout - KantinYuk</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: #333;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h1 { color: #1a4d7a; margin-bottom: 30px; }
        h2 { color: #0d2d47; margin-top: 25px; margin-bottom: 15px; font-size: 18px; }
        .order-summary {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .order-item { padding: 8px 0; border-bottom: 1px solid #ddd; }
        .order-item:last-child { border-bottom: none; }
        .total { color: #d32f2f; font-weight: bold; font-size: 20px; padding-top: 10px; }
        input, select {
            width: 100%;
            padding: 12px;
            margin: 8px 0 15px;
            border: 2px solid #1a4d7a;
            border-radius: 8px;
            font-size: 14px;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #0d2d47;
            box-shadow: 0 0 5px rgba(26, 77, 122, 0.3);
        }
        label { display: block; margin-top: 15px; color: #1a4d7a; font-weight: 600; }
        button { 
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
        }
        button:hover { transform: scale(1.02); }
        a { color: #1a4d7a; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Checkout Pesanan</h1>
        
        <div class="order-summary">
            <h2>Ringkasan Pesanan:</h2>
            {% for item in keranjang %}
            <div class="order-item">
                {{ item.nama }} - Rp {{ item.harga }} x {{ item.quantity }} = <strong>Rp {{ item.harga * item.quantity }}</strong>
            </div>
            {% endfor %}
            <div class="total">Total: Rp {{ total }}</div>
        </div>

        <form method="POST" action="/bayar">
            <h2>Data Diri Pemesan:</h2>
            
            <label for="nama">Nama Lengkap:</label>
            <input type="text" id="nama" name="nama" required>
            
            <label for="telepon">Nomor Telepon:</label>
            <input type="tel" id="telepon" name="telepon" required>
            
            <label for="metode">Metode Pembayaran:</label>
            <select id="metode" name="metode" required>
                <option value="">Pilih Metode Pembayaran</option>
                <option value="QRIS">QRIS (E-Wallet)</option>
                <option value="Cash">Cash (Bayar Langsung)</option>
            </select>
            
            <button type="submit">Lanjut Pembayaran</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            <a href="/keranjang">← Kembali ke Keranjang</a>
        </p>
    </div>
</body>
</html>
"""

pembayaran_template = """
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pembayaran Berhasil - KantinYuk</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: #333;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
        }
        .success-icon { font-size: 60px; margin-bottom: 20px; }
        h1 { color: #4caf50; margin-bottom: 10px; }
        .message { color: #666; margin: 20px 0; }
        .info-box {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        .info-box p { margin: 10px 0; }
        .label { color: #1a4d7a; font-weight: 600; }
        .method-box {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #1a4d7a;
        }
        .qr-box {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            margin: 20px 0;
            border: 2px solid #1a4d7a;
        }
        .qr-box img {
            max-width: 300px;
            width: 100%;
            height: auto;
            margin: 15px 0;
        }
        .qr-instruction {
            color: #666;
            font-size: 14px;
            margin-top: 15px;
            padding: 15px;
            background: #fff9e6;
            border-radius: 8px;
        }
        a.btn { 
            display: inline-block;
            background: linear-gradient(135deg, #1a4d7a 0%, #0d2d47 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 20px;
            font-weight: 600;
        }
        a.btn:hover { transform: scale(1.05); }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✅</div>
        <h1>Pembayaran Berhasil!</h1>
        <p class="message">Terima kasih telah berbelanja di KantinYuk</p>
        
        <div class="info-box">
            <p><span class="label">Nama Pemesan:</span> {{ nama }}</p>
            <p><span class="label">Nomor Telepon:</span> {{ telepon }}</p>
        </div>

        <div class="method-box">
            <p style="font-weight: 600; margin-bottom: 10px;">Metode Pembayaran:</p>
            {% if metode == 'QRIS' %}
                <p>💳 QRIS (E-Wallet)</p>
            {% elif metode == 'Cash' %}
                <p>💵 Cash (Bayar Langsung) - Pembayaran tunai saat pengambilan pesanan</p>
            {% endif %}
        </div>

        <!-- QR CODE UNTUK QRIS -->
        {% if metode == 'QRIS' and qr_code %}
        <div class="qr-box">
            <h3 style="color: #1a4d7a; margin-bottom: 10px;">Kode QRIS Anda</h3>
            <img src="{{ qr_code }}" alt="QRIS Code">
            <div class="qr-instruction">
                <strong>Cara Pembayaran:</strong><br>
                1. Buka aplikasi e-wallet Anda (GoPay, OVO, Dana, dll)<br>
                2. Pilih menu "Scan QRIS"<br>
                3. Arahkan kamera ke kode QRIS di atas<br>
                4. Konfirmasi dan selesaikan pembayaran
            </div>
        </div>
        {% endif %}

        <p style="color: #d32f2f; font-weight: 600;">Pesanan Anda sedang diproses 🔄</p>
        
        <a href="/" class="btn">← Kembali ke Beranda</a>
    </div>
</body>
</html>
"""


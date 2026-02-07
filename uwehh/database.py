import sqlite3
import hashlib

def init_db():
    conn = sqlite3.connect('kantin.db')
    c = conn.cursor()
    c.execute('''CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY,
                    username TEXT UNIQUE,
                    password TEXT
                )''')
    c.execute('''CREATE TABLE IF NOT EXISTS keranjang (
                    id INTEGER PRIMARY KEY,
                    nama TEXT,
                    harga INTEGER,
                    quantity INTEGER
                )''')
    conn.commit()
    conn.close()

def hash_password(password):
    return hashlib.sha256(password.encode()).hexdigest()

def register_user(username, password):
    conn = sqlite3.connect('kantin.db')
    c = conn.cursor()
    hashed_pw = hash_password(password)
    try:
        c.execute("INSERT INTO users (username, password) VALUES (?, ?)", (username, hashed_pw))
        conn.commit()
    except sqlite3.IntegrityError:
        pass
    conn.close()

def login_user(username, password):
    conn = sqlite3.connect('kantin.db')
    c = conn.cursor()
    hashed_pw = hash_password(password)
    c.execute("SELECT * FROM users WHERE username=? AND password=?", (username, hashed_pw))
    user = c.fetchone()
    conn.close()
    return user is not None

def check_user_exists(username):
    conn = sqlite3.connect('kantin.db')
    c = conn.cursor()
    c.execute("SELECT * FROM users WHERE username=?", (username,))
    user = c.fetchone()
    conn.close()
    return user is not None

def get_keranjang():
    conn = sqlite3.connect('kantin.db')
    c = conn.cursor()
    c.execute("SELECT nama, harga, quantity FROM keranjang")
    items = [{'nama': row[0], 'harga': row[1], 'quantity': row[2]} for row in c.fetchall()]
    conn.close()
    return items

def save_keranjang(keranjang):
    conn = sqlite3.connect('kantin.db')
    c = conn.cursor()
    c.execute("DELETE FROM keranjang")
    for item in keranjang:
        c.execute("INSERT INTO keranjang (nama, harga, quantity) VALUES (?, ?, ?)",
                  (item['nama'], item['harga'], item['quantity']))
    conn.commit()
    conn.close()

menu = {
    'makanan': {
        'berat': [
            {'nama': 'Mie Ayam', 'harga': 5000},
            {'nama': 'Bakso', 'harga': 7000},
            {'nama': 'Nasi Pecel', 'harga': 8000},
            {'nama': 'Nasi Rames', 'harga': 8000},
            {'nama': 'Nasi Rawon', 'harga': 8000}
        ],
        'ringan': [
            {'nama': 'Dimsum Goreng', 'harga': 5000},
            {'nama': 'Salad Buah', 'harga': 5000},
            {'nama': 'Kebab', 'harga': 6000},
            {'nama': 'Tahu Walik', 'harga': 2000},
            {'nama': 'Tahu Bakso', 'harga': 5000}
        ]
    },
    'minuman': [
        {'nama': 'Es Teh', 'harga': 3000},
        {'nama': 'Es Jeruk', 'harga': 3000},
        {'nama': 'Kopi Susu', 'harga': 3000},
        {'nama': 'Pop Ice', 'harga': 3000},
        {'nama': 'Thaitea', 'harga': 3000}
    ]
}

def get_menu_items(kategori, subkategori):
    if kategori == 'makanan' and subkategori:
        return menu['makanan'].get(subkategori, [])
    elif kategori == 'minuman':
        return menu['minuman']
    return []

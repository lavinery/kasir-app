<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - Kasir App</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; color: #333; line-height: 1.6; }
        .container { max-width: 1100px; margin: 0 auto; padding: 20px; }
        h1 { font-size: 2rem; margin-bottom: 0.5rem; color: #1a1a2e; }
        h2 { font-size: 1.4rem; margin: 2rem 0 1rem; color: #16213e; border-bottom: 2px solid #0f3460; padding-bottom: 0.5rem; }
        h3 { font-size: 1.1rem; margin: 1.5rem 0 0.5rem; color: #0f3460; }
        .header { background: #1a1a2e; color: #fff; padding: 2rem 0; margin-bottom: 2rem; }
        .header .container { display: flex; justify-content: space-between; align-items: center; }
        .header p { color: #a0a0b0; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; color: #fff; }
        .badge-get { background: #28a745; }
        .badge-post { background: #007bff; }
        .badge-put { background: #ffc107; color: #333; }
        .badge-delete { background: #dc3545; }
        .badge-auth { background: #6f42c1; }
        .badge-public { background: #17a2b8; }
        .badge-admin { background: #e83e8c; }
        .card { background: #fff; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .endpoint { background: #fff; border-radius: 8px; padding: 1rem 1.5rem; margin-bottom: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-left: 4px solid #ddd; }
        .endpoint-get { border-left-color: #28a745; }
        .endpoint-post { border-left-color: #007bff; }
        .endpoint-put { border-left-color: #ffc107; }
        .endpoint-delete { border-left-color: #dc3545; }
        .endpoint .method-url { display: flex; align-items: center; gap: 10px; margin-bottom: 0.25rem; }
        .endpoint .url { font-family: 'Courier New', monospace; font-size: 0.95rem; font-weight: 600; }
        .endpoint .desc { color: #666; font-size: 0.9rem; }
        pre { background: #1e1e2e; color: #cdd6f4; padding: 1rem; border-radius: 6px; overflow-x: auto; font-size: 0.85rem; margin: 0.75rem 0; }
        code { font-family: 'Courier New', monospace; }
        .inline-code { background: #e9ecef; padding: 2px 6px; border-radius: 3px; font-size: 0.85rem; }
        table { width: 100%; border-collapse: collapse; margin: 0.75rem 0; font-size: 0.9rem; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; }
        .nav { position: sticky; top: 0; background: #fff; border-bottom: 1px solid #eee; padding: 0.75rem 0; z-index: 10; }
        .nav .container { display: flex; gap: 1rem; flex-wrap: wrap; }
        .nav a { text-decoration: none; color: #0f3460; font-size: 0.85rem; padding: 4px 10px; border-radius: 4px; }
        .nav a:hover { background: #e9ecef; }
        .params { margin: 0.5rem 0; }
        .params span { display: inline-block; background: #f0f0f0; padding: 2px 8px; border-radius: 3px; font-size: 0.8rem; margin: 2px; font-family: monospace; }
    </style>
</head>
<body>

<div class="header">
    <div class="container">
        <div>
            <h1>Kasir App REST API</h1>
            <p>Dokumentasi lengkap endpoint API untuk integrasi dengan sistem lain</p>
        </div>
        <div>
            <span class="badge badge-public">v1.0</span>
        </div>
    </div>
</div>

<div class="nav">
    <div class="container">
        <a href="#autentikasi">Autentikasi</a>
        <a href="#produk">Produk</a>
        <a href="#kategori">Kategori</a>
        <a href="#member">Member</a>
        <a href="#supplier">Supplier</a>
        <a href="#penjualan">Penjualan</a>
        <a href="#pembelian">Pembelian</a>
        <a href="#pengeluaran">Pengeluaran</a>
        <a href="#dashboard">Dashboard</a>
    </div>
</div>

<div class="container">

    <!-- BASE INFO -->
    <div class="card">
        <h3>Base URL</h3>
        <pre><code>{{ url('/api') }}</code></pre>

        <h3>Format Response</h3>
        <p>Semua response menggunakan format JSON yang konsisten:</p>
        <pre><code>{
    "success": true,
    "message": "Pesan deskriptif",
    "data": { ... }
}</code></pre>

        <h3>Response Paginated (List)</h3>
        <pre><code>{
    "success": true,
    "message": "Data berhasil diambil",
    "data": [ ... ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 72,
        "from": 1,
        "to": 15
    }
}</code></pre>

        <h3>Query Parameters Umum</h3>
        <table>
            <tr><th>Parameter</th><th>Deskripsi</th><th>Default</th></tr>
            <tr><td><code class="inline-code">per_page</code></td><td>Jumlah data per halaman</td><td>15</td></tr>
            <tr><td><code class="inline-code">page</code></td><td>Nomor halaman</td><td>1</td></tr>
            <tr><td><code class="inline-code">sort_by</code></td><td>Kolom untuk sorting</td><td>primary key</td></tr>
            <tr><td><code class="inline-code">sort_order</code></td><td><code class="inline-code">asc</code> atau <code class="inline-code">desc</code></td><td>desc</td></tr>
        </table>
    </div>

    <!-- AUTENTIKASI -->
    <h2 id="autentikasi">Autentikasi</h2>

    <div class="card">
        <p>API menggunakan <strong>Laravel Sanctum</strong> token-based authentication. Dapatkan token melalui endpoint login, lalu sertakan di setiap request.</p>
        <h3>Header Authorization</h3>
        <pre><code>Authorization: Bearer {token}</code></pre>
        <p>Atau bisa juga header <code class="inline-code">Accept: application/json</code> untuk memastikan response JSON pada error.</p>
    </div>

    <div class="endpoint endpoint-post">
        <div class="method-url">
            <span class="badge badge-post">POST</span>
            <span class="url">/api/auth/login</span>
            <span class="badge badge-public">Public</span>
        </div>
        <div class="desc">Login dan mendapatkan API token</div>
        <h3>Request Body</h3>
        <pre><code>{
    "email": "admin@example.com",
    "password": "password123"
}</code></pre>
        <h3>Response (200)</h3>
        <pre><code>{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "user": {
            "id": 1,
            "name": "Administrator",
            "email": "admin@example.com",
            "level": 1,
            "foto": "/img/user.jpg"
        },
        "token": "1|abc123def456..."
    }
}</code></pre>
    </div>

    <div class="endpoint endpoint-post">
        <div class="method-url">
            <span class="badge badge-post">POST</span>
            <span class="url">/api/auth/logout</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Logout dan menghapus token saat ini</div>
    </div>

    <div class="endpoint endpoint-get">
        <div class="method-url">
            <span class="badge badge-get">GET</span>
            <span class="url">/api/auth/profile</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Mendapatkan data profil user yang sedang login</div>
    </div>

    <!-- PRODUK -->
    <h2 id="produk">Produk</h2>

    <div class="endpoint endpoint-get">
        <div class="method-url">
            <span class="badge badge-get">GET</span>
            <span class="url">/api/produk</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Daftar semua produk (paginated)</div>
        <div class="params">
            <strong>Filter:</strong>
            <span>search=keyword</span>
            <span>kategori=id_kategori</span>
            <span>include=kategori</span>
        </div>
        <h3>Contoh Request</h3>
        <pre><code>GET /api/produk?search=laptop&kategori=1&include=kategori&per_page=10&sort_by=harga_jual&sort_order=asc</code></pre>
    </div>

    <div class="endpoint endpoint-get">
        <div class="method-url">
            <span class="badge badge-get">GET</span>
            <span class="url">/api/produk/{id}</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Detail satu produk</div>
        <div class="params"><span>include=kategori</span></div>
    </div>

    <div class="endpoint endpoint-post">
        <div class="method-url">
            <span class="badge badge-post">POST</span>
            <span class="url">/api/produk</span>
            <span class="badge badge-auth">Auth</span>
            <span class="badge badge-admin">Admin</span>
        </div>
        <div class="desc">Tambah produk baru</div>
        <h3>Request Body</h3>
        <pre><code>{
    "nama_produk": "Laptop Asus",
    "id_kategori": 1,
    "harga_beli": 5000000,
    "harga_jual": 6500000,
    "stok": 10,
    "diskon": 0,
    "kode_produk": null
}</code></pre>
        <table>
            <tr><th>Field</th><th>Tipe</th><th>Wajib</th><th>Keterangan</th></tr>
            <tr><td>nama_produk</td><td>string</td><td>Ya</td><td>Max 255 karakter</td></tr>
            <tr><td>id_kategori</td><td>integer</td><td>Ya</td><td>ID kategori yang valid</td></tr>
            <tr><td>harga_beli</td><td>numeric</td><td>Ya</td><td>0 - 999999999</td></tr>
            <tr><td>harga_jual</td><td>numeric</td><td>Ya</td><td>0 - 999999999</td></tr>
            <tr><td>stok</td><td>integer</td><td>Ya</td><td>0 - 999999</td></tr>
            <tr><td>diskon</td><td>integer</td><td>Tidak</td><td>0 - 100 (persen)</td></tr>
            <tr><td>kode_produk</td><td>string</td><td>Tidak</td><td>Auto-generate jika kosong</td></tr>
        </table>
    </div>

    <div class="endpoint endpoint-put">
        <div class="method-url">
            <span class="badge badge-put">PUT</span>
            <span class="url">/api/produk/{id}</span>
            <span class="badge badge-auth">Auth</span>
            <span class="badge badge-admin">Admin</span>
        </div>
        <div class="desc">Update produk (body sama seperti POST)</div>
    </div>

    <div class="endpoint endpoint-delete">
        <div class="method-url">
            <span class="badge badge-delete">DELETE</span>
            <span class="url">/api/produk/{id}</span>
            <span class="badge badge-auth">Auth</span>
            <span class="badge badge-admin">Admin</span>
        </div>
        <div class="desc">Hapus produk</div>
    </div>

    <!-- KATEGORI -->
    <h2 id="kategori">Kategori</h2>

    <div class="endpoint endpoint-get">
        <div class="method-url">
            <span class="badge badge-get">GET</span>
            <span class="url">/api/kategori</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Daftar semua kategori (paginated)</div>
        <div class="params"><span>search=keyword</span></div>
    </div>

    <div class="endpoint endpoint-get">
        <div class="method-url">
            <span class="badge badge-get">GET</span>
            <span class="url">/api/kategori/{id}</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Detail satu kategori</div>
    </div>

    <div class="endpoint endpoint-post">
        <div class="method-url">
            <span class="badge badge-post">POST</span>
            <span class="url">/api/kategori</span>
            <span class="badge badge-auth">Auth</span>
            <span class="badge badge-admin">Admin</span>
        </div>
        <div class="desc">Tambah kategori baru</div>
        <pre><code>{ "nama_kategori": "Elektronik" }</code></pre>
    </div>

    <div class="endpoint endpoint-put">
        <div class="method-url">
            <span class="badge badge-put">PUT</span>
            <span class="url">/api/kategori/{id}</span>
            <span class="badge badge-auth">Auth</span>
            <span class="badge badge-admin">Admin</span>
        </div>
        <div class="desc">Update kategori</div>
    </div>

    <div class="endpoint endpoint-delete">
        <div class="method-url">
            <span class="badge badge-delete">DELETE</span>
            <span class="url">/api/kategori/{id}</span>
            <span class="badge badge-auth">Auth</span>
            <span class="badge badge-admin">Admin</span>
        </div>
        <div class="desc">Hapus kategori</div>
    </div>

    <!-- MEMBER -->
    <h2 id="member">Member</h2>

    <div class="endpoint endpoint-get">
        <div class="method-url">
            <span class="badge badge-get">GET</span>
            <span class="url">/api/member</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Daftar semua member (paginated)</div>
        <div class="params"><span>search=nama/kode/telepon</span></div>
    </div>

    <div class="endpoint endpoint-get">
        <div class="method-url">
            <span class="badge badge-get">GET</span>
            <span class="url">/api/member/{id}</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Detail satu member</div>
    </div>

    <div class="endpoint endpoint-post">
        <div class="method-url">
            <span class="badge badge-post">POST</span>
            <span class="url">/api/member</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Tambah member baru (Admin & Kasir). Kode member di-generate otomatis.</div>
        <pre><code>{
    "nama": "John Doe",
    "telepon": "081234567890",
    "alamat": "Jl. Contoh No. 1"
}</code></pre>
    </div>

    <div class="endpoint endpoint-put">
        <div class="method-url">
            <span class="badge badge-put">PUT</span>
            <span class="url">/api/member/{id}</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Update member (Admin & Kasir)</div>
    </div>

    <div class="endpoint endpoint-delete">
        <div class="method-url">
            <span class="badge badge-delete">DELETE</span>
            <span class="url">/api/member/{id}</span>
            <span class="badge badge-auth">Auth</span>
            <span class="badge badge-admin">Admin</span>
        </div>
        <div class="desc">Hapus member (Admin only)</div>
    </div>

    <!-- SUPPLIER -->
    <h2 id="supplier">Supplier</h2>

    <div class="endpoint endpoint-get">
        <div class="method-url">
            <span class="badge badge-get">GET</span>
            <span class="url">/api/supplier</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Daftar semua supplier (paginated)</div>
        <div class="params"><span>search=nama/telepon</span></div>
    </div>

    <div class="endpoint endpoint-get">
        <div class="method-url">
            <span class="badge badge-get">GET</span>
            <span class="url">/api/supplier/{id}</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Detail satu supplier</div>
    </div>

    <div class="endpoint endpoint-post">
        <div class="method-url">
            <span class="badge badge-post">POST</span>
            <span class="url">/api/supplier</span>
            <span class="badge badge-auth">Auth</span>
            <span class="badge badge-admin">Admin</span>
        </div>
        <div class="desc">Tambah supplier baru</div>
        <pre><code>{
    "nama": "PT Supplier Jaya",
    "telepon": "021-1234567",
    "alamat": "Jl. Industri No. 10"
}</code></pre>
    </div>

    <div class="endpoint endpoint-put">
        <div class="method-url">
            <span class="badge badge-put">PUT</span>
            <span class="url">/api/supplier/{id}</span>
            <span class="badge badge-auth">Auth</span>
            <span class="badge badge-admin">Admin</span>
        </div>
        <div class="desc">Update supplier</div>
    </div>

    <div class="endpoint endpoint-delete">
        <div class="method-url">
            <span class="badge badge-delete">DELETE</span>
            <span class="url">/api/supplier/{id}</span>
            <span class="badge badge-auth">Auth</span>
            <span class="badge badge-admin">Admin</span>
        </div>
        <div class="desc">Hapus supplier</div>
    </div>

    <!-- PENJUALAN -->
    <h2 id="penjualan">Penjualan</h2>

    <div class="endpoint endpoint-get">
        <div class="method-url">
            <span class="badge badge-get">GET</span>
            <span class="url">/api/penjualan</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Daftar penjualan (paginated, read-only)</div>
        <div class="params">
            <span>tanggal_dari=2025-01-01</span>
            <span>tanggal_sampai=2025-12-31</span>
            <span>id_member=1</span>
            <span>include=member,user</span>
        </div>
    </div>

    <div class="endpoint endpoint-get">
        <div class="method-url">
            <span class="badge badge-get">GET</span>
            <span class="url">/api/penjualan/{id}</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Detail penjualan beserta item detail dan produk</div>
        <div class="params"><span>include=detail</span></div>
        <h3>Response (200)</h3>
        <pre><code>{
    "success": true,
    "message": "Berhasil",
    "data": {
        "id_penjualan": 1,
        "id_member": 1,
        "total_item": 3,
        "total_harga": 150000,
        "diskon": 5,
        "bayar": 142500,
        "diterima": 150000,
        "member": { "id_member": 1, "nama": "John", ... },
        "user": { "id": 1, "name": "Admin" },
        "detail": [
            {
                "id_penjualan_detail": 1,
                "id_produk": 5,
                "harga_jual": 50000,
                "jumlah": 3,
                "subtotal": 150000,
                "produk": { "id_produk": 5, "nama_produk": "...", ... }
            }
        ]
    }
}</code></pre>
    </div>

    <!-- PEMBELIAN -->
    <h2 id="pembelian">Pembelian</h2>

    <div class="endpoint endpoint-get">
        <div class="method-url">
            <span class="badge badge-get">GET</span>
            <span class="url">/api/pembelian</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Daftar pembelian (paginated, read-only)</div>
        <div class="params">
            <span>tanggal_dari=2025-01-01</span>
            <span>tanggal_sampai=2025-12-31</span>
            <span>id_supplier=1</span>
            <span>include=supplier</span>
        </div>
    </div>

    <div class="endpoint endpoint-get">
        <div class="method-url">
            <span class="badge badge-get">GET</span>
            <span class="url">/api/pembelian/{id}</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Detail pembelian beserta item detail dan produk</div>
        <div class="params"><span>include=detail</span></div>
    </div>

    <!-- PENGELUARAN -->
    <h2 id="pengeluaran">Pengeluaran</h2>

    <div class="endpoint endpoint-get">
        <div class="method-url">
            <span class="badge badge-get">GET</span>
            <span class="url">/api/pengeluaran</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Daftar pengeluaran (paginated, read-only)</div>
        <div class="params">
            <span>tanggal_dari=2025-01-01</span>
            <span>tanggal_sampai=2025-12-31</span>
            <span>search=keyword</span>
        </div>
    </div>

    <div class="endpoint endpoint-get">
        <div class="method-url">
            <span class="badge badge-get">GET</span>
            <span class="url">/api/pengeluaran/{id}</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Detail satu pengeluaran</div>
    </div>

    <!-- DASHBOARD -->
    <h2 id="dashboard">Dashboard</h2>

    <div class="endpoint endpoint-get">
        <div class="method-url">
            <span class="badge badge-get">GET</span>
            <span class="url">/api/dashboard/stats</span>
            <span class="badge badge-auth">Auth</span>
        </div>
        <div class="desc">Statistik ringkasan (total produk, penjualan hari ini, dll)</div>
        <h3>Response (200)</h3>
        <pre><code>{
    "success": true,
    "message": "Statistik dashboard berhasil diambil",
    "data": {
        "total_kategori": 5,
        "total_produk": 120,
        "total_supplier": 10,
        "total_member": 50,
        "penjualan_hari_ini": 5000000,
        "pembelian_hari_ini": 2000000,
        "pengeluaran_hari_ini": 500000,
        "pendapatan_hari_ini": 2500000,
        "penjualan_bulan_ini": 75000000,
        "transaksi_hari_ini": 15
    }
}</code></pre>
    </div>

    <!-- ERROR CODES -->
    <h2 id="errors">Kode Error</h2>
    <div class="card">
        <table>
            <tr><th>HTTP Code</th><th>Deskripsi</th></tr>
            <tr><td><code class="inline-code">200</code></td><td>Request berhasil</td></tr>
            <tr><td><code class="inline-code">201</code></td><td>Data berhasil dibuat</td></tr>
            <tr><td><code class="inline-code">401</code></td><td>Tidak terautentikasi (token tidak valid atau tidak disertakan)</td></tr>
            <tr><td><code class="inline-code">403</code></td><td>Tidak memiliki akses (level user tidak sesuai)</td></tr>
            <tr><td><code class="inline-code">404</code></td><td>Data tidak ditemukan</td></tr>
            <tr><td><code class="inline-code">422</code></td><td>Validasi gagal (lihat field <code class="inline-code">errors</code> di response)</td></tr>
            <tr><td><code class="inline-code">500</code></td><td>Server error</td></tr>
        </table>

        <h3>Contoh Error 422 (Validasi)</h3>
        <pre><code>{
    "message": "The given data was invalid.",
    "errors": {
        "nama_produk": ["Nama produk harus diisi."],
        "harga_jual": ["Harga jual harus berupa angka."]
    }
}</code></pre>

        <h3>Contoh Error 401 (Unauthenticated)</h3>
        <pre><code>{
    "message": "Unauthenticated."
}</code></pre>
    </div>

    <!-- CONTOH PENGGUNAAN -->
    <h2 id="contoh">Contoh Penggunaan (cURL)</h2>
    <div class="card">
        <h3>1. Login</h3>
        <pre><code>curl -X POST {{ url('/api/auth/login') }} \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'</code></pre>

        <h3>2. Get Produk (dengan token)</h3>
        <pre><code>curl -X GET "{{ url('/api/produk') }}?include=kategori&per_page=5" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"</code></pre>

        <h3>3. Tambah Produk</h3>
        <pre><code>curl -X POST {{ url('/api/produk') }} \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "nama_produk": "Produk Baru",
    "id_kategori": 1,
    "harga_beli": 10000,
    "harga_jual": 15000,
    "stok": 50,
    "diskon": 0
  }'</code></pre>

        <h3>4. Logout</h3>
        <pre><code>curl -X POST {{ url('/api/auth/logout') }} \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"</code></pre>
    </div>

    <div style="text-align:center; padding: 2rem 0; color: #999; font-size: 0.85rem;">
        Kasir App API Documentation &mdash; Laravel Sanctum
    </div>

</div>

</body>
</html>

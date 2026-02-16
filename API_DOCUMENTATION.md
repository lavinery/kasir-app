# Kasir App - REST API Documentation

> Dokumentasi lengkap untuk integrasi dengan sistem Kasir App melalui REST API.

---

## Informasi Umum

| Item | Detail |
|------|--------|
| Base URL | `http://kasir-app.test/api` |
| Format | JSON |
| Autentikasi | Bearer Token (Laravel Sanctum) |
| Content-Type | `application/json` |

### Header yang Wajib Disertakan

```
Accept: application/json
Content-Type: application/json
Authorization: Bearer {token}
```

### Format Response Standar

Semua endpoint mengembalikan format yang konsisten:

```json
{
    "success": true,
    "message": "Pesan deskriptif",
    "data": { ... }
}
```

### Format Response List (Paginated)

```json
{
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
}
```

### Query Parameters Umum (Berlaku di Semua List Endpoint)

| Parameter | Tipe | Default | Keterangan |
|-----------|------|---------|------------|
| `per_page` | integer | 15 | Jumlah data per halaman |
| `page` | integer | 1 | Nomor halaman |
| `sort_by` | string | primary key | Kolom untuk sorting |
| `sort_order` | string | `desc` | `asc` atau `desc` |

### Kode HTTP Response

| Code | Keterangan |
|------|------------|
| `200` | Berhasil |
| `201` | Data berhasil dibuat |
| `401` | Tidak terautentikasi (token tidak valid / tidak ada) |
| `403` | Tidak punya akses (level user tidak sesuai) |
| `404` | Data tidak ditemukan |
| `422` | Validasi gagal |
| `500` | Server error |

### Level User

| Level | Role | Akses |
|-------|------|-------|
| 1 | Admin | Full akses (CRUD semua data) |
| 2 | Kasir | Baca semua data, CRUD member |

---

## 1. Autentikasi

### 1.1 Login

Mendapatkan token untuk mengakses API.

```
POST /api/auth/login
```

**Header:** Tidak perlu token (public endpoint)

**Request Body:**

```json
{
    "email": "admin@example.com",
    "password": "password123"
}
```

**Response Sukses (200):**

```json
{
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
        "token": "1|abc123def456ghi789..."
    }
}
```

**Response Gagal (422):**

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["Email atau password salah."]
    }
}
```

> **Penting:** Simpan nilai `token` dari response. Token ini harus disertakan di setiap request berikutnya sebagai `Authorization: Bearer {token}`.

---

### 1.2 Logout

Menghapus token yang sedang digunakan.

```
POST /api/auth/logout
```

**Header:** `Authorization: Bearer {token}`

**Response (200):**

```json
{
    "success": true,
    "message": "Logout berhasil",
    "data": null
}
```

---

### 1.3 Profil User

Mendapatkan data user yang sedang login.

```
GET /api/auth/profile
```

**Header:** `Authorization: Bearer {token}`

**Response (200):**

```json
{
    "success": true,
    "message": "Profil berhasil diambil",
    "data": {
        "id": 1,
        "name": "Administrator",
        "email": "admin@example.com",
        "level": 1,
        "foto": "/img/user.jpg"
    }
}
```

---

## 2. Produk

### 2.1 Daftar Produk

```
GET /api/produk
```

**Akses:** Semua user yang login

**Query Parameters:**

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| `search` | string | Cari berdasarkan nama, kode, atau merk produk |
| `kategori` | integer | Filter berdasarkan `id_kategori` |
| `include` | string | Sertakan relasi: `kategori` |

**Contoh Request:**

```
GET /api/produk?search=laptop&kategori=1&include=kategori&per_page=10&sort_by=harga_jual&sort_order=asc
```

**Response (200):**

```json
{
    "success": true,
    "message": "Data berhasil diambil",
    "data": [
        {
            "id_produk": 1,
            "kode_produk": "P000001",
            "nama_produk": "Laptop Asus",
            "merk": "Asus",
            "id_kategori": 1,
            "harga_beli": 5000000,
            "harga_jual": 6500000,
            "diskon": 0,
            "stok": 10,
            "keuntungan": 1500000,
            "kategori": {
                "id_kategori": 1,
                "nama_kategori": "Elektronik"
            },
            "created_at": "2025-01-15T10:30:00.000000Z",
            "updated_at": "2025-01-15T10:30:00.000000Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 10,
        "total": 1,
        "from": 1,
        "to": 1
    }
}
```

---

### 2.2 Detail Produk

```
GET /api/produk/{id}
```

**Akses:** Semua user yang login

**Query Parameters:**

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| `include` | string | Sertakan relasi: `kategori` |

**Contoh:** `GET /api/produk/1?include=kategori`

---

### 2.3 Tambah Produk

```
POST /api/produk
```

**Akses:** Admin only (level 1)

**Request Body:**

```json
{
    "nama_produk": "Laptop Asus",
    "id_kategori": 1,
    "harga_beli": 5000000,
    "harga_jual": 6500000,
    "stok": 10,
    "diskon": 0,
    "kode_produk": null
}
```

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|------------|
| `nama_produk` | string | Ya | Max 255 karakter |
| `id_kategori` | integer | Ya | ID kategori yang valid |
| `harga_beli` | numeric | Ya | 0 - 999.999.999 |
| `harga_jual` | numeric | Ya | 0 - 999.999.999 |
| `stok` | integer | Ya | 0 - 999.999 |
| `diskon` | integer | Tidak | 0 - 100 (persen) |
| `kode_produk` | string | Tidak | Max 50 karakter, auto-generate jika kosong |

**Response (201):**

```json
{
    "success": true,
    "message": "Produk berhasil ditambahkan",
    "data": {
        "id_produk": 2,
        "kode_produk": "P000002",
        "nama_produk": "Laptop Asus",
        "..."
    }
}
```

---

### 2.4 Update Produk

```
PUT /api/produk/{id}
```

**Akses:** Admin only (level 1)

**Request Body:** Sama seperti Tambah Produk

---

### 2.5 Hapus Produk

```
DELETE /api/produk/{id}
```

**Akses:** Admin only (level 1)

**Response (200):**

```json
{
    "success": true,
    "message": "Produk berhasil dihapus",
    "data": null
}
```

---

## 3. Kategori

### 3.1 Daftar Kategori

```
GET /api/kategori
```

**Akses:** Semua user yang login

**Query Parameters:**

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| `search` | string | Cari berdasarkan nama kategori |

**Response (200):**

```json
{
    "success": true,
    "message": "Data berhasil diambil",
    "data": [
        {
            "id_kategori": 1,
            "nama_kategori": "Elektronik",
            "created_at": "2025-01-10T08:00:00.000000Z",
            "updated_at": "2025-01-10T08:00:00.000000Z"
        }
    ],
    "meta": { "..." }
}
```

---

### 3.2 Detail Kategori

```
GET /api/kategori/{id}
```

**Akses:** Semua user yang login

---

### 3.3 Tambah Kategori

```
POST /api/kategori
```

**Akses:** Admin only (level 1)

**Request Body:**

```json
{
    "nama_kategori": "Elektronik"
}
```

---

### 3.4 Update Kategori

```
PUT /api/kategori/{id}
```

**Akses:** Admin only (level 1)

**Request Body:** Sama seperti Tambah Kategori

---

### 3.5 Hapus Kategori

```
DELETE /api/kategori/{id}
```

**Akses:** Admin only (level 1)

---

## 4. Member

### 4.1 Daftar Member

```
GET /api/member
```

**Akses:** Semua user yang login

**Query Parameters:**

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| `search` | string | Cari berdasarkan nama, kode member, atau telepon |

**Response (200):**

```json
{
    "success": true,
    "message": "Data berhasil diambil",
    "data": [
        {
            "id_member": 1,
            "kode_member": "00001",
            "nama": "John Doe",
            "telepon": "081234567890",
            "alamat": "Jl. Contoh No. 1",
            "created_at": "2025-01-10T08:00:00.000000Z",
            "updated_at": "2025-01-10T08:00:00.000000Z"
        }
    ],
    "meta": { "..." }
}
```

---

### 4.2 Detail Member

```
GET /api/member/{id}
```

**Akses:** Semua user yang login

---

### 4.3 Tambah Member

```
POST /api/member
```

**Akses:** Admin & Kasir (level 1, 2)

**Request Body:**

```json
{
    "nama": "John Doe",
    "telepon": "081234567890",
    "alamat": "Jl. Contoh No. 1"
}
```

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|------------|
| `nama` | string | Ya | Max 255 karakter |
| `telepon` | string | Ya | Max 20 karakter |
| `alamat` | string | Tidak | Max 500 karakter |

> `kode_member` di-generate otomatis oleh sistem.

---

### 4.4 Update Member

```
PUT /api/member/{id}
```

**Akses:** Admin & Kasir (level 1, 2)

**Request Body:** Sama seperti Tambah Member

---

### 4.5 Hapus Member

```
DELETE /api/member/{id}
```

**Akses:** Admin only (level 1)

---

## 5. Supplier

### 5.1 Daftar Supplier

```
GET /api/supplier
```

**Akses:** Semua user yang login

**Query Parameters:**

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| `search` | string | Cari berdasarkan nama atau telepon |

---

### 5.2 Detail Supplier

```
GET /api/supplier/{id}
```

**Akses:** Semua user yang login

---

### 5.3 Tambah Supplier

```
POST /api/supplier
```

**Akses:** Admin only (level 1)

**Request Body:**

```json
{
    "nama": "PT Supplier Jaya",
    "telepon": "021-1234567",
    "alamat": "Jl. Industri No. 10"
}
```

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|------------|
| `nama` | string | Ya | Max 255 karakter |
| `telepon` | string | Ya | Max 20 karakter |
| `alamat` | string | Tidak | Max 500 karakter |

---

### 5.4 Update Supplier

```
PUT /api/supplier/{id}
```

**Akses:** Admin only (level 1)

---

### 5.5 Hapus Supplier

```
DELETE /api/supplier/{id}
```

**Akses:** Admin only (level 1)

---

## 6. Penjualan (Read Only)

### 6.1 Daftar Penjualan

```
GET /api/penjualan
```

**Akses:** Semua user yang login

**Query Parameters:**

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| `tanggal_dari` | date | Filter dari tanggal (format: `YYYY-MM-DD`) |
| `tanggal_sampai` | date | Filter sampai tanggal (format: `YYYY-MM-DD`) |
| `id_member` | integer | Filter berdasarkan member |
| `include` | string | Sertakan relasi: `member`, `user` (pisah koma) |

**Contoh:**

```
GET /api/penjualan?tanggal_dari=2025-01-01&tanggal_sampai=2025-01-31&include=member,user
```

---

### 6.2 Detail Penjualan

```
GET /api/penjualan/{id}
```

**Akses:** Semua user yang login

**Query Parameters:**

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| `include` | string | Sertakan `detail` untuk melihat item-item penjualan |

**Contoh:** `GET /api/penjualan/1?include=detail`

**Response (200):**

```json
{
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
        "id_user": 1,
        "member": {
            "id_member": 1,
            "kode_member": "00001",
            "nama": "John Doe",
            "telepon": "081234567890",
            "alamat": "Jl. Contoh No. 1"
        },
        "user": {
            "id": 1,
            "name": "Administrator"
        },
        "detail": [
            {
                "id_penjualan_detail": 1,
                "id_penjualan": 1,
                "id_produk": 5,
                "harga_jual": 50000,
                "jumlah": 3,
                "diskon": 0,
                "subtotal": 150000,
                "produk": {
                    "id_produk": 5,
                    "kode_produk": "P000005",
                    "nama_produk": "Mouse Wireless"
                }
            }
        ],
        "created_at": "2025-01-15T14:30:00.000000Z",
        "updated_at": "2025-01-15T14:30:00.000000Z"
    }
}
```

---

## 7. Pembelian (Read Only)

### 7.1 Daftar Pembelian

```
GET /api/pembelian
```

**Akses:** Semua user yang login

**Query Parameters:**

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| `tanggal_dari` | date | Filter dari tanggal (format: `YYYY-MM-DD`) |
| `tanggal_sampai` | date | Filter sampai tanggal (format: `YYYY-MM-DD`) |
| `id_supplier` | integer | Filter berdasarkan supplier |
| `include` | string | Sertakan relasi: `supplier` |

---

### 7.2 Detail Pembelian

```
GET /api/pembelian/{id}
```

**Akses:** Semua user yang login

**Query Parameters:**

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| `include` | string | Sertakan `detail` untuk melihat item-item pembelian |

---

## 8. Pengeluaran (Read Only)

### 8.1 Daftar Pengeluaran

```
GET /api/pengeluaran
```

**Akses:** Semua user yang login

**Query Parameters:**

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| `tanggal_dari` | date | Filter dari tanggal (format: `YYYY-MM-DD`) |
| `tanggal_sampai` | date | Filter sampai tanggal (format: `YYYY-MM-DD`) |
| `search` | string | Cari berdasarkan deskripsi |

**Response item:**

```json
{
    "id_pengeluaran": 1,
    "deskripsi": "Bayar listrik",
    "nominal": 500000,
    "created_at": "2025-01-15T10:00:00.000000Z",
    "updated_at": "2025-01-15T10:00:00.000000Z"
}
```

---

### 8.2 Detail Pengeluaran

```
GET /api/pengeluaran/{id}
```

**Akses:** Semua user yang login

---

## 9. Dashboard

### 9.1 Statistik

```
GET /api/dashboard/stats
```

**Akses:** Semua user yang login

**Response (200):**

```json
{
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
}
```

---

## Ringkasan Semua Endpoint

| Method | Endpoint | Akses | Keterangan |
|--------|----------|-------|------------|
| `POST` | `/api/auth/login` | Public | Login, dapat token |
| `POST` | `/api/auth/logout` | Auth | Logout, hapus token |
| `GET` | `/api/auth/profile` | Auth | Data user login |
| | | | |
| `GET` | `/api/produk` | Auth | List produk |
| `GET` | `/api/produk/{id}` | Auth | Detail produk |
| `POST` | `/api/produk` | Admin | Tambah produk |
| `PUT` | `/api/produk/{id}` | Admin | Update produk |
| `DELETE` | `/api/produk/{id}` | Admin | Hapus produk |
| | | | |
| `GET` | `/api/kategori` | Auth | List kategori |
| `GET` | `/api/kategori/{id}` | Auth | Detail kategori |
| `POST` | `/api/kategori` | Admin | Tambah kategori |
| `PUT` | `/api/kategori/{id}` | Admin | Update kategori |
| `DELETE` | `/api/kategori/{id}` | Admin | Hapus kategori |
| | | | |
| `GET` | `/api/member` | Auth | List member |
| `GET` | `/api/member/{id}` | Auth | Detail member |
| `POST` | `/api/member` | Admin & Kasir | Tambah member |
| `PUT` | `/api/member/{id}` | Admin & Kasir | Update member |
| `DELETE` | `/api/member/{id}` | Admin | Hapus member |
| | | | |
| `GET` | `/api/supplier` | Auth | List supplier |
| `GET` | `/api/supplier/{id}` | Auth | Detail supplier |
| `POST` | `/api/supplier` | Admin | Tambah supplier |
| `PUT` | `/api/supplier/{id}` | Admin | Update supplier |
| `DELETE` | `/api/supplier/{id}` | Admin | Hapus supplier |
| | | | |
| `GET` | `/api/penjualan` | Auth | List penjualan |
| `GET` | `/api/penjualan/{id}` | Auth | Detail penjualan |
| | | | |
| `GET` | `/api/pembelian` | Auth | List pembelian |
| `GET` | `/api/pembelian/{id}` | Auth | Detail pembelian |
| | | | |
| `GET` | `/api/pengeluaran` | Auth | List pengeluaran |
| `GET` | `/api/pengeluaran/{id}` | Auth | Detail pengeluaran |
| | | | |
| `GET` | `/api/dashboard/stats` | Auth | Statistik ringkasan |

---

## Contoh Penggunaan (cURL)

### Login

```bash
curl -X POST http://kasir-app.test/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"admin@example.com","password":"password123"}'
```

### Get Produk dengan Token

```bash
curl -X GET "http://kasir-app.test/api/produk?include=kategori&per_page=5" \
  -H "Authorization: Bearer 1|abc123def456..." \
  -H "Accept: application/json"
```

### Tambah Produk Baru

```bash
curl -X POST http://kasir-app.test/api/produk \
  -H "Authorization: Bearer 1|abc123def456..." \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "nama_produk": "Produk Baru",
    "id_kategori": 1,
    "harga_beli": 10000,
    "harga_jual": 15000,
    "stok": 50,
    "diskon": 0
  }'
```

### Filter Penjualan per Tanggal

```bash
curl -X GET "http://kasir-app.test/api/penjualan?tanggal_dari=2025-01-01&tanggal_sampai=2025-01-31&include=member,user" \
  -H "Authorization: Bearer 1|abc123def456..." \
  -H "Accept: application/json"
```

### Logout

```bash
curl -X POST http://kasir-app.test/api/auth/logout \
  -H "Authorization: Bearer 1|abc123def456..." \
  -H "Accept: application/json"
```

---

## Catatan Penting

1. **Ganti Base URL** sesuai domain yang digunakan (misalnya `https://pos.namatoko.com/api`)
2. **Token bersifat rahasia** - jangan bagikan token ke pihak yang tidak berwenang
3. **Satu token = satu sesi** - setelah logout, token tidak bisa dipakai lagi
4. **Semua nilai uang dalam satuan Rupiah** tanpa format (contoh: `5000000` bukan `5.000.000`)
5. **Tanggal menggunakan format ISO** (`YYYY-MM-DD`) untuk filter, response menggunakan ISO 8601

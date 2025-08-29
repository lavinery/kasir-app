# 📋 Panduan Sistem Barang Habis

## 🎯 **Overview**

Sistem barang habis memiliki **2 mode sinkronisasi**:

1. **🔄 Auto Sync** - Otomatis ketika stok produk berubah
2. **⚡ Manual Sync** - Manual melalui web interface atau command

## 🚀 **Fitur yang Tersedia**

### **1. Auto Sync (Otomatis)**
- ✅ **Trigger**: Setiap kali stok produk diubah
- ✅ **Threshold**: Default 5 (bisa diubah di config)
- ✅ **Logic**: 
  - Stok ≤ 5 → Masuk daftar barang habis (auto)
  - Stok > 5 → Keluar dari daftar (auto)
- ✅ **Logging**: Semua aktivitas tercatat di log

### **2. Manual Sync (Manual)**
- ✅ **Web Interface**: Tombol "Refresh + Sinkronisasi"
- ✅ **Command Line**: `php artisan barang-habis:sync`
- ✅ **Threshold**: Bisa diatur per request
- ✅ **Force Mode**: Sync semua produk

### **3. Monitoring & Status**
- ✅ **Real-time Status**: Badge status di halaman
- ✅ **Statistics**: Statistik lengkap sync
- ✅ **Log History**: Riwayat aktivitas sync

## 📊 **Cara Kerja**

### **Auto Sync Flow:**
```
Produk Stok Berubah → Observer Terpicu → Check Threshold → Update Barang Habis
```

### **Manual Sync Flow:**
```
User Trigger → Check Products → Add/Remove → Update Status → Show Results
```

## 🛠️ **Cara Menggunakan**

### **1. Auto Sync (Sudah Aktif)**
Auto sync sudah aktif secara default. Setiap kali Anda:
- Menambah produk baru dengan stok ≤ 5
- Mengubah stok produk menjadi ≤ 5
- Mengubah stok produk menjadi > 5

Sistem akan otomatis:
- Menambah/menghapus dari daftar barang habis
- Mencatat aktivitas di log
- Menampilkan status real-time

### **2. Manual Sync via Web**
1. Buka halaman **Barang Habis**
2. Klik dropdown **"Refresh"**
3. Pilih **"Refresh + Sinkronisasi"**
4. Konfirmasi sinkronisasi
5. Lihat hasil di tabel dan notifikasi

### **3. Manual Sync via Command**
```bash
# Normal sync (threshold default 5)
php artisan barang-habis:sync

# Custom threshold
php artisan barang-habis:sync --threshold=3

# Force sync semua produk
php artisan barang-habis:sync --force

# Kombinasi
php artisan barang-habis:sync --threshold=3 --force
```

### **4. Check Status Sync**
```bash
# Via web interface
Klik "Status Sinkronisasi" di dropdown

# Via command
php artisan barang-habis:sync --help
```

## ⚙️ **Konfigurasi**

### **1. Threshold Default**
Edit file `config/app.php`:
```php
'stock_threshold' => env('STOCK_THRESHOLD', 5),
```

Atau di file `.env`:
```env
STOCK_THRESHOLD=3
```

### **2. Auto Sync Schedule (Opsional)**
Edit file `app/Console/Kernel.php`:
```php
// Uncomment untuk auto sync terjadwal
$schedule->command('barang-habis:sync')
    ->hourly()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/barang-habis-sync.log'));
```

## 📈 **Monitoring & Logs**

### **1. Log Files**
- **Auto Sync**: `storage/logs/laravel.log`
- **Manual Sync**: `storage/logs/laravel.log`
- **Scheduled Sync**: `storage/logs/barang-habis-sync.log`

### **2. Log Format**
```
[2024-01-15 10:30:45] local.INFO: Auto added to barang habis: Produk A (stok: 3)
[2024-01-15 10:35:12] local.INFO: Auto removed from barang habis: Produk B (stok: 8)
```

### **3. Status Monitoring**
- **Web Interface**: Badge status real-time
- **Statistics**: Total item, auto/manual entries
- **Last Sync**: Waktu terakhir auto sync

## 🔧 **Troubleshooting**

### **1. Auto Sync Tidak Berfungsi**
```bash
# Check observer registration
php artisan tinker
>>> App\Models\Produk::observe(App\Observers\ProdukStockObserver::class);

# Check logs
tail -f storage/logs/laravel.log
```

### **2. Manual Sync Error**
```bash
# Check command
php artisan barang-habis:sync --help

# Test dengan verbose
php artisan barang-habis:sync --verbose

# Check database connection
php artisan tinker
>>> App\Models\Produk::count();
```

### **3. Performance Issues**
```bash
# Optimize database
php artisan optimize

# Clear cache
php artisan cache:clear
php artisan config:clear
```

## 📋 **Best Practices**

### **1. Threshold Setting**
- **Retail**: 5-10 (sesuai permintaan)
- **Wholesale**: 20-50 (sesuai kapasitas)
- **Custom**: Sesuai kebutuhan bisnis

### **2. Sync Frequency**
- **Auto Sync**: Real-time (sudah optimal)
- **Manual Sync**: 1-2x per hari
- **Scheduled Sync**: Setiap jam (opsional)

### **3. Monitoring**
- **Daily**: Check status badge
- **Weekly**: Review log files
- **Monthly**: Analyze statistics

## 🎯 **Contoh Penggunaan**

### **Scenario 1: Produk Baru**
```bash
# 1. Tambah produk dengan stok 3
# 2. Auto sync akan menambah ke barang habis
# 3. Status: "Auto Sync Aktif" + "1 item perlu sync"
```

### **Scenario 2: Update Stok**
```bash
# 1. Update stok dari 10 → 3
# 2. Auto sync menambah ke barang habis
# 3. Update stok dari 3 → 8
# 4. Auto sync menghapus dari barang habis
```

### **Scenario 3: Manual Sync**
```bash
# 1. Ada 5 produk dengan stok ≤ 3
# 2. Jalankan: php artisan barang-habis:sync --threshold=3
# 3. Hasil: 5 item ditambah ke barang habis
```

## ✅ **Status Implementasi**

- ✅ **Auto Sync**: Aktif dan berfungsi
- ✅ **Manual Sync**: Web interface + Command
- ✅ **Monitoring**: Real-time status + statistics
- ✅ **Logging**: Complete activity logging
- ✅ **Configuration**: Flexible threshold setting
- ✅ **Documentation**: Complete guide

---

**🎉 Sistem barang habis sudah siap digunakan dengan mode otomatis dan manual!**

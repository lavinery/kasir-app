<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Produk;
use App\Models\BarangHabis;

class SyncBarangHabis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'barang-habis:sync {--threshold=5 : Stock threshold untuk barang habis} {--force : Force sync semua produk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync barang habis berdasarkan stok produk';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threshold = (int) $this->option('threshold');
        $force = $this->option('force');
        
        $this->info("🔄 Memulai sinkronisasi barang habis...");
        $this->info("📊 Threshold: {$threshold}");
        $this->info("⚡ Mode: " . ($force ? 'Force Sync' : 'Normal Sync'));
        
        $startTime = microtime(true);
        
        try {
            // Mulai database transaction
            \DB::beginTransaction();
            
            $added = 0;
            $removed = 0;
            $processed = 0;
            $errors = [];
            
            if ($force) {
                // Force sync: proses semua produk
                $this->info("📦 Memproses semua produk...");
                $produks = Produk::with(['kategori'])->get();
            } else {
                // Normal sync: hanya produk yang perlu diupdate
                $this->info("📦 Memproses produk yang perlu sinkronisasi...");
                
                // 1. Produk dengan stok <= threshold yang belum ada di barang habis
                $produksToAdd = Produk::where('stok', '<=', $threshold)
                    ->whereNotIn('id_produk', function ($query) {
                        $query->select('id_produk')->from('barang_habis');
                    })
                    ->with(['kategori'])
                    ->get();
                
                // 2. Produk auto di barang habis dengan stok > threshold
                $produksToRemove = BarangHabis::with('produk.kategori')
                    ->where('tipe', 'auto')
                    ->whereHas('produk', function ($query) use ($threshold) {
                        $query->where('stok', '>', $threshold);
                    })
                    ->get();
                
                $produks = $produksToAdd->merge($produksToRemove);
            }
            
            $progressBar = $this->output->createProgressBar($produks->count());
            $progressBar->start();
            
            foreach ($produks as $produk) {
                try {
                    $processed++;
                    
                    if ($force) {
                        // Force sync: check semua produk
                        $this->processProduct($produk, $threshold, $added, $removed);
                    } else {
                        // Normal sync: berdasarkan kondisi
                        if ($produk instanceof BarangHabis) {
                            // Ini adalah barang habis yang perlu dihapus
                            $this->removeFromBarangHabis($produk, $removed);
                        } else {
                            // Ini adalah produk yang perlu ditambah
                            $this->addToBarangHabis($produk, $threshold, $added);
                        }
                    }
                    
                    $progressBar->advance();
                } catch (\Exception $e) {
                    $errors[] = "Error processing {$produk->nama_produk}: " . $e->getMessage();
                }
            }
            
            $progressBar->finish();
            $this->newLine();
            
            // Commit transaction
            \DB::commit();
            
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);
            
            // Tampilkan hasil
            $this->info("✅ Sinkronisasi selesai!");
            $this->info("📊 Statistik:");
            $this->info("   • Diproses: {$processed} produk");
            $this->info("   • Ditambah: {$added} item");
            $this->info("   • Dihapus: {$removed} item");
            $this->info("   • Durasi: {$duration} detik");
            
            if (count($errors) > 0) {
                $this->warn("⚠️  Error yang terjadi:");
                foreach ($errors as $error) {
                    $this->error("   • {$error}");
                }
            }
            
            // Log aktivitas
            \Log::info('Manual sync barang habis completed via command', [
                'threshold' => $threshold,
                'force' => $force,
                'processed' => $processed,
                'added' => $added,
                'removed' => $removed,
                'errors_count' => count($errors),
                'duration' => $duration,
                'user' => 'console'
            ]);
            
        } catch (\Exception $e) {
            \DB::rollback();
            
            $this->error("❌ Error sinkronisasi: " . $e->getMessage());
            \Log::error('Manual sync barang habis failed via command: ' . $e->getMessage());
            
            return 1;
        }
        
        return 0;
    }
    
    /**
     * Process single product for force sync
     */
    private function processProduct($produk, $threshold, &$added, &$removed)
    {
        $barangHabis = BarangHabis::where('id_produk', $produk->id_produk)->first();
        
        if ($produk->stok <= $threshold) {
            if (!$barangHabis) {
                BarangHabis::create([
                    'id_produk' => $produk->id_produk,
                    'tipe' => 'auto',
                    'keterangan' => "Force sync - stok {$produk->stok} ≤ {$threshold} (" . now()->format('Y-m-d H:i:s') . ")"
                ]);
                $added++;
            }
        } else {
            if ($barangHabis && $barangHabis->tipe === 'auto') {
                $barangHabis->delete();
                $removed++;
            }
        }
    }
    
    /**
     * Add product to barang habis
     */
    private function addToBarangHabis($produk, $threshold, &$added)
    {
        BarangHabis::create([
            'id_produk' => $produk->id_produk,
            'tipe' => 'auto',
            'keterangan' => "Manual sync - stok {$produk->stok} ≤ {$threshold} (" . now()->format('Y-m-d H:i:s') . ")"
        ]);
        $added++;
    }
    
    /**
     * Remove product from barang habis
     */
    private function removeFromBarangHabis($barangHabis, &$removed)
    {
        $barangHabis->delete();
        $removed++;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\FavoriteProduct;
use App\Models\Produk;
use App\Http\Requests\AddFavoriteRequest;
use App\Http\Requests\ReorderFavoritesRequest;
use Illuminate\Http\Request;

class FavoriteProductController extends Controller
{
    // Tambahkan method untuk cek admin
    protected function checkAdmin()
    {
        if (auth()->user()->level != 1) {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }
    }

    public function index()
    {
        $this->checkAdmin(); // Cek admin di awal method

        $favorites = FavoriteProduct::with(['product' => function ($query) {
            $query->select('id_produk', 'nama_produk', 'harga_jual', 'kode_produk');
        }])
            ->ordered()
            ->get();

        $activeCount = $favorites->where('is_active', true)->count();
        $displayedCount = min(10, $activeCount);

        return view('setting.favorites', compact('favorites', 'activeCount', 'displayedCount'));
    }

    public function add(Request $request) // Ganti dari AddFavoriteRequest ke Request biasa
    {
        $this->checkAdmin();

        // Manual validation
        $request->validate([
            'product_id' => [
                'required',
                'integer',
                'exists:produk,id_produk', // sesuaikan nama tabel
                'unique:favorite_products,product_id',
            ],
        ], [
            'product_id.required' => 'Produk harus dipilih.',
            'product_id.exists' => 'Produk tidak ditemukan.',
            'product_id.unique' => 'Produk sudah ada di favorit.',
        ]);

        $maxSortOrder = FavoriteProduct::max('sort_order') ?? 0;

        FavoriteProduct::create([
            'product_id' => $request->product_id,
            'sort_order' => $maxSortOrder + 1,
            'is_active' => true,
        ]);

        $activeCount = FavoriteProduct::where('is_active', true)->count();

        if ($activeCount > 10) {
            return back()->with('success', 'Produk berhasil ditambahkan ke favorit. Yang tampil di Transaksi tetap maksimal 10. Kamu bisa atur urutan di sini.');
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke favorit.');
    }

    public function reorder(Request $request) // Ganti dari ReorderFavoritesRequest
    {
        $this->checkAdmin();

        // Manual validation
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:favorite_products,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            FavoriteProduct::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan berhasil disimpan.']);
    }

    public function toggle(FavoriteProduct $favorite)
    {
        $this->checkAdmin();

        $favorite->update(['is_active' => !$favorite->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $favorite->is_active,
            'message' => $favorite->is_active ? 'Favorit diaktifkan.' : 'Favorit dinonaktifkan.'
        ]);
    }

    public function destroy(FavoriteProduct $favorite)
    {
        $this->checkAdmin();

        $favorite->delete();

        return response()->json(['success' => true, 'message' => 'Favorit berhasil dihapus.']);
    }

    public function forTransaction()
    {
        // Semua user yang login bisa akses ini
        $favorites = FavoriteProduct::with(['product' => function ($query) {
            $query->select('id_produk', 'nama_produk', 'harga_jual', 'kode_produk');
        }])
            ->where('is_active', true)
            ->orderBy('sort_order')->orderBy('id')
            ->take(10)
            ->get()
            ->map(function ($favorite) {
                return [
                    'favorite_id' => $favorite->id,
                    'product_id' => $favorite->product->id_produk,
                    'nama' => $favorite->product->nama_produk,
                    'harga' => $favorite->product->harga_jual,
                    'kode' => $favorite->product->kode_produk,
                ];
            });

        return response()->json($favorites);
    }

    public function searchProducts(Request $request)
    {
        $this->checkAdmin();

        $search = $request->get('q', '');

        $products = Produk::where(function ($query) use ($search) {
            $query->where('nama_produk', 'like', "%{$search}%")
                ->orWhere('kode_produk', 'like', "%{$search}%");
        })
            ->whereNotIn('id_produk', function ($query) {
                $query->select('product_id')->from('favorite_products');
            })
            ->select('id_produk as id', 'nama_produk as text', 'kode_produk', 'harga_jual')
            ->limit(20)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'text' => $product->text . ' (' . $product->kode_produk . ') - Rp. ' . number_format($product->harga_jual),
                    'kode_produk' => $product->kode_produk,
                    'nama_produk' => $product->text,
                    'harga_jual' => $product->harga_jual,
                ];
            });

        return response()->json(['results' => $products]);
    }
}
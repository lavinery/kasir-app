<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Models\Produk;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProdukExport;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Milon\Barcode\DNS1D;
// use Spatie\Browsershot\Browsershot;

class ProdukController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all()->pluck('nama_kategori', 'id_kategori');
        return view('produk.index', compact('kategori'));
    }

    public function data()
    {
        $produk = Produk::leftJoin('kategori', 'kategori.id_kategori', 'produk.id_kategori')
            ->select('produk.*', 'nama_kategori')
            ->get();

        return datatables()
            ->of($produk)
            ->addIndexColumn()
            ->addColumn('select_all', fn($produk) => '<input type="checkbox" name="id_produk[]" value="' . $produk->id_produk . '">')
            ->addColumn('kode_produk', fn($produk) => '<span class="label label-success">' . $produk->kode_produk . '</span>')
            ->addColumn('harga_beli', fn($produk) => format_uang($produk->harga_beli))
            ->addColumn('keuntungan', fn($produk) => format_uang($produk->keuntungan))
            ->addColumn('harga_jual', fn($produk) => format_uang($produk->harga_jual))
            ->addColumn('diskon', fn($produk) => $produk->diskon . '%')
            ->addColumn('stok', fn($produk) => format_uang($produk->stok))
            ->addColumn('aksi', function ($produk) {
                return '<div class="btn-group">
                    <button type="button" onclick="editForm(`' . route('produk.update', $produk->id_produk) . '`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button type="button" onclick="deleteData(`' . route('produk.destroy', $produk->id_produk) . '`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>';
            })
            ->rawColumns(['aksi', 'kode_produk', 'select_all'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'id_kategori' => 'required|integer|exists:kategori,id_kategori',
            'harga_beli' => 'required|numeric|min:0|max:999999999',
            'harga_jual' => 'required|numeric|min:0|max:999999999',
            'stok'       => 'required|integer|min:0|max:999999',
            'diskon'     => 'nullable|integer|min:0|max:100',
            'kode_produk' => 'nullable|string|max:50|unique:produk,kode_produk',
        ]);

        $produkTerakhir = Produk::latest()->first() ?? new Produk();

        if (!$request->filled('kode_produk')) {
            $request['kode_produk'] = 'P' . tambah_nol_didepan((int)$produkTerakhir->id_produk + 1, 6);
        }

        $data = $request->all();
        $data['keuntungan'] = $data['harga_jual'] - $data['harga_beli'];

        Produk::create($data);
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan!'
        ], 200);
    }

    public function show($id)
    {
        return response()->json(Produk::find($id));
    }

    public function edit($id)
    {
        return response()->json(Produk::find($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'id_kategori' => 'required|integer|exists:kategori,id_kategori',
            'harga_beli' => 'required|numeric|min:0|max:999999999',
            'harga_jual' => 'required|numeric|min:0|max:999999999',
            'stok'       => 'required|integer|min:0|max:999999',
            'diskon'     => 'nullable|integer|min:0|max:100',
            'kode_produk' => 'nullable|string|max:50|unique:produk,kode_produk,' . $id . ',id_produk',
        ]);

        $produk = Produk::find($id);
        $data = $request->all();
        $data['keuntungan'] = $data['harga_jual'] - $data['harga_beli'];
        $produk->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui!'
        ], 200);
    }

    public function destroy($id)
    {
        $produk = Produk::find($id);
        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan!'
            ], 404);
        }
        
        $namaProduk = $produk->nama_produk;
        $produk->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Produk '{$namaProduk}' berhasil dihapus!"
        ], 200);
    }

    public function deleteSelected(Request $request)
    {
        if (!$request->id_produk || count($request->id_produk) == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih produk yang akan dihapus!'
            ], 400);
        }

        $deletedCount = 0;
        $deletedNames = [];
        
        foreach ($request->id_produk as $id) {
            $produk = Produk::find($id);
            if ($produk) {
                $deletedNames[] = $produk->nama_produk;
                $produk->delete();
                $deletedCount++;
            }
        }
        
        if ($deletedCount == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada produk yang berhasil dihapus!'
            ], 400);
        }
        
        $message = $deletedCount == 1 
            ? "Produk '{$deletedNames[0]}' berhasil dihapus!"
            : "{$deletedCount} produk berhasil dihapus!";
            
        return response()->json([
            'success' => true,
            'message' => $message
        ], 200);
    }


    public function cetakDaftar(Request $request)
    {
        $produk = Produk::leftJoin('kategori', 'kategori.id_kategori', 'produk.id_kategori')
            ->select('produk.*', 'nama_kategori')
            ->whereIn('id_produk', $request->id_produk)
            ->get();

        $pdf = PDF::loadView('produk.daftar', compact('produk'));
        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('daftar_produk.pdf');
    }


    public function cetakBarcode(Request $request)
    {
        try {
            // Debug: Log request data
            \Log::info('Cetak Barcode called', [
                'id_produk' => $request->id_produk,
                'jumlah_copy_global' => $request->jumlah_copy_global
            ]);

            $ids = $request->id_produk;
            $jumlahCopy = $request->jumlah_copy_global ?? 1;

            if (!$ids || count($ids) < 1) {
                \Log::error('No products selected');
                return back()->with('error', 'Pilih produk terlebih dahulu!');
            }

            if ($jumlahCopy < 1) $jumlahCopy = 1;

            $produkAsli = Produk::whereIn('id_produk', $ids)->get();

            // Debug: Log jumlah produk
            \Log::info('Products found', ['count' => $produkAsli->count()]);

            // Duplikasi produk sesuai jumlah copy
            $dataproduk = collect();
            foreach ($produkAsli as $item) {
                for ($i = 0; $i < $jumlahCopy; $i++) {
                    $dataproduk->push($item);
                }
            }

            // Debug: Log total after duplication
            \Log::info('Total products after duplication', ['count' => $dataproduk->count()]);

            $no = 1;
            $pdf = PDF::loadView('produk.barcode', compact('dataproduk', 'no'));
            $pdf->setPaper('a4', 'portrait');

            \Log::info('PDF generated successfully');
            return $pdf->stream('barcode-produk-terpilih.pdf');
        } catch (\Exception $e) {
            \Log::error('Error in cetakBarcode: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cetakBarcodeLabel(Request $request)
    {
        $produk = Produk::whereIn('id_produk', $request->id_produk)->get();

        // Ambil jumlah copy dari request atau set default
        $jumlahCopy = $request->jumlah_copy_global ?? 1;

        // Tambahkan field jumlah_copy pada setiap produk
        foreach ($produk as $p) {
            $p->jumlah_copy = $jumlahCopy; // Menetapkan jumlah salinan
        }

        // Lebar label sesuai pilihan user
        $lebarLabel = $request->lebar_label ?? 105; // Default lebar 105mm

        $pdf = PDF::loadView('produk.barcode_label', compact('produk', 'lebarLabel'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('barcode-label.pdf');
    }
    public function cetakBarcodeLabel105(Request $request)
    {
        return $this->generateBarcodeLabel($request, 105);
    }

    private function generateBarcodeLabel(Request $request, $ukuranLabel)
    {
        $ids = $request->id_produk;
        $jumlahCopy = $request->jumlah_copy_global ?? 1;

        if (!$ids || count($ids) < 1) {
            return back()->with('error', 'Pilih produk terlebih dahulu!');
        }

        if ($jumlahCopy < 1) $jumlahCopy = 1;

        $produkAsli = Produk::whereIn('id_produk', $ids)->get();

        // Duplikasi produk sesuai jumlah copy
        $produk = collect();
        foreach ($produkAsli as $item) {
            for ($i = 0; $i < $jumlahCopy; $i++) {
                $produk->push($item);
            }
        }

        $viewName = $ukuranLabel == 105 ? 'produk.barcode-label-105' : 'produk.barcode-label-107';

        $pdf = PDF::loadView($viewName, [
            'produk' => $produk,
            'ukuranLabel' => $ukuranLabel
        ]);

        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('barcode-label-' . $ukuranLabel . '.pdf');
    }
    public function cetakBarcodeLabel107(Request $request)
    {
        $ids = $request->id_produk;
        $jumlahCopy = $request->jumlah_copy_global ?? 1;

        if (!$ids || count($ids) < 1) {
            return back()->with('error', 'Pilih produk terlebih dahulu!');
        }

        if ($jumlahCopy < 1) $jumlahCopy = 1;

        $produkAsli = Produk::whereIn('id_produk', $ids)->get();

        // Duplikasi produk sesuai jumlah copy
        $produk = collect();
        foreach ($produkAsli as $item) {
            for ($i = 0; $i < $jumlahCopy; $i++) {
                $produk->push($item);
            }
        }

        $pdf = PDF::loadView('produk.barcode-label-107', [
            'produk' => $produk,
            'ukuranLabel' => 107
        ]);

        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('barcode-label-107.pdf');
    }

    public function cetakBarcodeLabel33x15(Request $request)
    {
        $ids = $request->id_produk;
        $jumlahCopy = $request->jumlah_copy_global ?? 1;

        if (!$ids || count($ids) < 1) {
            return back()->with('error', 'Pilih produk terlebih dahulu!');
        }

        if ($jumlahCopy < 1) $jumlahCopy = 1;

        $produkAsli = Produk::whereIn('id_produk', $ids)->get();

        // Duplikasi produk sesuai jumlah copy
        $produk = collect();
        foreach ($produkAsli as $item) {
            for ($i = 0; $i < $jumlahCopy; $i++) {
                $produk->push($item);
            }
        }

        $pdf = PDF::loadView('produk.barcode-label-33x15', compact('produk'));

        // Ubah paper size untuk continuous printing dengan margin yang lebih kecil
        // Width: 72mm = 204.09 points, Height: auto untuk continuous
        $pdf->setPaper([0, 0, 204.09, 100000], 'portrait');

        return $pdf->stream('barcode-label-33x15.pdf');
    }
    public function exportExcel(Request $request)
    {
        try {
            // Ambil ID produk yang dipilih dari request
            $ids = $request->id_produk;

            // Ambil jumlah copy global dari request (default 1 kalau tidak diisi)
            $jumlahCopy = $request->jumlah_copy_global ?? 1;

            // Validasi: jika tidak ada ID produk yang dipilih
            if (!$ids || count($ids) < 1) {
                return redirect()->back()->with('error', 'Pilih produk terlebih dahulu!');
            }

            // Validasi: minimal jumlah copy = 1
            if ($jumlahCopy < 1) {
                $jumlahCopy = 1;
            }

            // Generate nama file
            $filename = 'data_produk_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Eksekusi download dengan export
            return Excel::download(new ProdukExport($ids, $jumlahCopy), $filename);
        } catch (\Exception $e) {
            \Log::error('Export Excel Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }


    // public function barcodePNG()
    // {
    //     $produk = Produk::all();
    //     $zipFile = public_path('barcode-images.zip');

    //     if (file_exists($zipFile)) {
    //         unlink($zipFile);
    //     }

    //     $zip = new ZipArchive;
    //     if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
    //         foreach ($produk as $item) {
    //             $barcode = DNS1D::getBarcodePNG($item->kode_produk, 'C39');
    //             $image = base64_decode($barcode);
    //             $filename = 'barcode-' . $item->kode_produk . '.png';
    //             $zip->addFromString($filename, $image);
    //         }
    //         $zip->close();
    //     }

    //     return response()->download($zipFile);
    // }

    // public function barcodePNG(Request $request)
    // {
    //     $produkList = Produk::whereIn('id_produk', $request->id_produk)->get();

    //     $zipFile = storage_path('app/barcode-images.zip');
    //     if (file_exists($zipFile)) {
    //         unlink($zipFile);
    //     }

    //     $zip = new \ZipArchive;
    //     if ($zip->open($zipFile, \ZipArchive::CREATE) === TRUE) {
    //         foreach ($produkList as $produk) {
    //             // Generate barcode
    //             $d = new \Milon\Barcode\DNS1D();
    //             $barcodeBase64 = $d->getBarcodePNG($produk->kode_produk, 'C39', 2, 60); // ukuran barcode diperbesar
    //             $barcodeImage = imagecreatefromstring(base64_decode($barcodeBase64));

    //             // Ukuran final gambar
    //             $width = 500;
    //             $height = 220;
    //             $img = imagecreatetruecolor($width, $height);

    //             // Warna
    //             $white = imagecolorallocate($img, 255, 255, 255);
    //             $black = imagecolorallocate($img, 0, 0, 0);
    //             imagefill($img, 0, 0, $white);

    //             // Border tebal
    //             $borderColor = imagecolorallocate($img, 0, 0, 0);
    //             imagerectangle($img, 0, 0, $width - 1, $height - 1, $borderColor);

    //             // Tulis nama produk dan harga
    //             $fontPath = public_path('fonts/arial.ttf'); // Pastikan font tersedia
    //             $text = $produk->nama_produk . ' - Rp ' . format_uang($produk->harga_jual);
    //             imagettftext($img, 12, 0, 20, 30, $black, $fontPath, $text);

    //             // Tempel barcode
    //             $barcodeX = ($width - imagesx($barcodeImage)) / 2; // center align
    //             imagecopy($img, $barcodeImage, $barcodeX, 50, 0, 0, imagesx($barcodeImage), imagesy($barcodeImage));

    //             // Tulis kode barcode di bawahnya
    //             $kodeX = ($width - (strlen($produk->kode_produk) * 12)) / 2;
    //             imagettftext($img, 14, 0, $kodeX, 160, $black, $fontPath, $produk->kode_produk);

    //             // Simpan image ke buffer
    //             ob_start();
    //             imagepng($img);
    //             $imageData = ob_get_clean();

    //             // Tambah ke ZIP
    //             $zip->addFromString('barcode-' . $produk->kode_produk . '.png', $imageData);

    //             // Bersihkan memori
    //             imagedestroy($img);
    //             imagedestroy($barcodeImage);
    //         }

    //         $zip->close();
    //     }

    //     return response()->download($zipFile)->deleteFileAfterSend(true);
    // }

    public function barcodePDF()
    {
        $produk = Produk::all();
        $pdf = PDF::loadView('produk.barcode', compact('produk'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('barcode-produk.pdf');
    }

    public function barcodePNG(Request $request)
    {
        $produkList = Produk::whereIn('id_produk', $request->id_produk)->get();

        $zipFile = storage_path('app/barcode-images.zip');
        if (file_exists($zipFile)) {
            unlink($zipFile);
        }

        $zip = new \ZipArchive;
        if ($zip->open($zipFile, \ZipArchive::CREATE) === TRUE) {
            foreach ($produkList as $produk) {
                // Generate barcode
                $d = new \Milon\Barcode\DNS1D();
                $barcodeBase64 = $d->getBarcodePNG($produk->kode_produk, 'C39', 2, 60);
                $barcodeImage = imagecreatefromstring(base64_decode($barcodeBase64));

                // Ukuran final gambar
                $width = 500;
                $height = 220;
                $img = imagecreatetruecolor($width, $height);

                // Warna
                $white = imagecolorallocate($img, 255, 255, 255);
                $black = imagecolorallocate($img, 0, 0, 0);
                imagefill($img, 0, 0, $white);

                // Border tebal
                $borderColor = imagecolorallocate($img, 0, 0, 0);
                imagerectangle($img, 0, 0, $width - 1, $height - 1, $borderColor);

                // Tulis nama produk dan harga
                $fontPath = public_path('fonts/arial.ttf');
                $text = $produk->nama_produk . ' - Rp ' . format_uang($produk->harga_jual);
                imagettftext($img, 12, 0, 20, 30, $black, $fontPath, $text);

                // Tempel barcode
                $barcodeX = ($width - imagesx($barcodeImage)) / 2;
                imagecopy($img, $barcodeImage, $barcodeX, 50, 0, 0, imagesx($barcodeImage), imagesy($barcodeImage));

                // Tulis kode barcode di bawahnya
                $kodeX = ($width - (strlen($produk->kode_produk) * 12)) / 2;
                imagettftext($img, 14, 0, $kodeX, 160, $black, $fontPath, $produk->kode_produk);

                // Simpan image ke buffer
                ob_start();
                imagepng($img);
                $imageData = ob_get_clean();

                // Tambah ke ZIP
                $zip->addFromString('barcode-' . $produk->kode_produk . '.png', $imageData);

                // Bersihkan memori
                imagedestroy($img);
                imagedestroy($barcodeImage);
            }

            $zip->close();
        }

        return response()->download($zipFile)->deleteFileAfterSend(true);
    }
}

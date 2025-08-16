{{-- resources/views/produk/barcode.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cetak Barcode</title>
    <style>
        .text-center { text-align: center; }
        .small-text { font-size: 0.6em; } /* Kelas untuk mengubah ukuran font */
        .barcode-text { font-size: 0.5em; } /* Kelas khusus untuk angka di bawah barcode */
    </style>
</head>
<body>
    <table width="100%">
        <tr>
            @foreach ($dataproduk as $produk)
                <td class="text-center" style="border: 1px solid #333;">
                    <p class="small-text">{{ $produk->nama_produk }} - Rp. {{ format_uang($produk->harga_jual) }}</p>
                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($produk->kode_produk, 'C39') }}"
                         alt="{{ $produk->kode_produk }}" width="100" height="20">
                    <br>
                    <span class="barcode-text">{{ $produk->kode_produk }}</span> <!-- Menggunakan kelas baru di sini -->
                </td>
                @if ($no++ % 5 == 0)
        </tr><tr>
                @endif
            @endforeach
        </tr>
    </table>
</body>
</html>
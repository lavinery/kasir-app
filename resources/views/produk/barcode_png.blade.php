<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barcode Produk</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            padding: 20px;
            margin: 0;
        }
        .barcode {
            margin-top: 10px;
        }
        .container {
            border: 1px solid #333;
            padding: 10px;
            width: 320px;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <p><strong>{{ $produk->nama_produk }}</strong><br>Rp {{ format_uang($produk->harga_jual) }}</p>
        <div class="barcode">
            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($produk->kode_produk, 'C39') }}" alt="{{ $produk->kode_produk }}" width="200" height="40">
            <div>{{ $produk->kode_produk }}</div>
        </div>
    </div>
</body>
</html>

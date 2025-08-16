<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cetak Barcode Label 105 (25x38mm)</title>
    <style>
        @page { 
            margin: 4mm; 
        }
        body { 
            margin: 0; 
            padding: 0; 
            font-family: Arial, sans-serif; 
        }
        .container {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            align-items: flex-start;
        }
        .label {
            width: 37mm;
            height: 22mm;
            border: 1px solid #ccc;
            margin: 0.5mm;
            padding: 0.5mm;
            display: inline-block;
            vertical-align: top;
            text-align: center;
            position: relative;
            box-sizing: border-box;
            page-break-inside: avoid;
        }
        .nama-harga {
            font-size: 6px;
            margin-top: 1mm;
            font-weight: bold;
            line-height: 2.5;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .barcode-container {
            margin: 0.5mm 0;
            height:3mm;
             padding: 0.5mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .barcode-img {
            max-width: 110%;
            max-height: 110%;
            object-fit: contain;
        }
        .kode-produk {
            font-size: 7px;
            font-weight: bold;
            margin-top: 0.2mm;
            position: absolute;
            bottom: 0.2mm;
            left: 1mm;
            right: 1mm;
            text-align: center;
            position: relative;
            box-sizing: border-box;
            
        }
        .label:nth-child(5n+1) {
            clear: left;
        }
    </style>
</head>
<body>
    <div class="container">
        @foreach($produk as $index => $item)
            {{-- Tambahkan page-break setiap 30 label --}}
            @if($index > 0 && $index % 30 == 0)
                </div>
                <div style="page-break-before: always;"></div>
                <div class="container">
            @endif

            <div class="label">
               <div class="nama-harga">{{ $item->nama_produk }} Rp{{ format_uang($item->harga_jual) }}</div>
               <div class="barcode-container">
                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($item->kode_produk, 'C39', 1, 30) }}" 
                         alt="barcode" class="barcode-img">
                </div>
                <div class="kode-produk">{{ $item->kode_produk }}</div>
            </div>
        @endforeach
    </div>
</body>
</html>


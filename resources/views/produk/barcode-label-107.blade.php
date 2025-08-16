<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cetak Barcode Label 107 (19x50mm)</title>
    <style>
        @page { 
            margin: 3mm;
        }
        body { 
            margin: 0;
            margin-left: 6mm; 
            padding: 0; 
            font-family: Arial, sans-serif; 
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            align-items: flex-start;
            width: auto;
            max-width: 170mm;
            max-height: 210mm;
            /* border: 1px solid #ccc;*/
        }
        .label {
            width: 48mm;
            height: 17mm;
            margin: 0.5mm;
            padding: 1mm;
            display: inline-block;
            vertical-align: top;
            text-align: center;
            position: relative;
            box-sizing: border-box;
            page-break-inside: avoid;
            margin-bottom: 1.4mm;
            /* border: 1px solid #ccc;*/
        }
        .nama-harga {
            font-size: 8px;
            padding: 0.5mm;
            font-weight: bold;
            line-height: 1.2;
            margin: 0;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }
        .barcode-container {
            margin: 0.5mm 0;
            height: 3mm;
            padding: 0.5mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .barcode-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .kode-produk {
            font-size: 7px;
            font-weight: bold;
            margin-top: 1.5mm;
            position: absolute;
            bottom: 0.2mm;
            left: 1mm;
            right: 1mm;
            text-align: center;
            position: relative;
            box-sizing: border-box;
        }
        
        /* CSS untuk pagination */
        .container {
            page-break-after: always;
            min-height: 200mm;
        }
        
        .container:last-child {
            page-break-after: avoid;
        }
    </style>
</head>
<body>
    @php
        $chunks = $produk->chunk(30);
    @endphp
    
    @foreach($chunks as $chunk)
        <div class="container">
            @foreach($chunk as $item)
                <div class="label">
                    <div class="nama-harga">{{ $item->nama_produk }} Rp{{ format_uang($item->harga_jual) }}</div>
                    <div class="barcode-container">
                        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($item->kode_produk, 'C39', 0.7, 20) }}" 
                             alt="barcode" class="barcode-img">
                    </div>
                    <div class="kode-produk">{{ $item->kode_produk }}</div>
                </div>
            @endforeach
        </div>
    @endforeach
</body>
</html>
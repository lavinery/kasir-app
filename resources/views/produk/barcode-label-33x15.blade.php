<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cetak Barcode Label 33x15mm</title>
    <style>
        @page {
            margin: 0;
            size: 72mm auto; /* Auto height untuk continuous printing */
        }
        
        body { 
            margin: 0; 
            padding: 0mm; /* Gap untuk semua sisi */
            font-family: Arial, sans-serif; 
            box-sizing: border-box;
        }

        .container {
            width: 100%;
            padding: 0mm; /* Gap tambahan */
            box-sizing: border-box;
        }

        table {
            width: 98%;
            border-collapse: separate;
            table-layout: fixed;
        }
        
        td {
            width: 40%;
            padding: 1.5%;
            padding-bottom:0.5%;
            padding-top:0.2%;
            box-sizing: border-box;
            vertical-align: top;
            align-items:center;
            
           /*border: 1px solid #000;*/
        }
        
        .label {
            width: 33mm;
            height: 15mm;
            /*border: 1px solid #000;*/
            padding: 0mm;
            text-align: center;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            background-color: white;
            page-break-inside: avoid; /* Hindari label terpotong */
        }
        
        .label-nama {
            font-size: 5px;
            font-weight: bold;
            line-height: 1.1;
            height: 3mm;
            overflow: hidden;
            text-overflow: ellipsis;
            word-wrap: break-word;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            width: 100%;
        }
        
        .label-barcode {
            height: 7mm;
            width: 95%;
            display: flex;
            margin-left:-1%;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            margin-bottom: 0.2mm;
            /*border: 1px solid #000;*/
        }
        
        .label-barcode svg {
            max-width: 30mm !important;
            max-height: 5mm !important;
            width: auto !important;
            height: auto !important;
        }
        
        .label-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-left: 5%;
            height: 2mm;
            font-size: 4px;
            font-weight: bold;
            line-height: 1;
        }
        
        .label-kode {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1;
            text-align: left;
        }
        
        .label-harga {
            margin-left: 2px;
            white-space: nowrap;
        }
        
        /* Hilangkan page break untuk continuous printing */
        tr {
            page-break-inside: avoid;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .label {
                border: 1px solid #000 !important;
                background-color: white !important;
            }
            
            /* Pastikan tidak ada page break yang tidak diinginkan */
            table, tbody, tr, td {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <table>
            @php
                $chunks = $produk->chunk(2);
            @endphp

            @foreach($chunks as $chunk)
                <tr>
                    @foreach($chunk as $item)
                        <td>
                            <div class="label">
                                <div class="label-nama">{{ Str::limit($item->nama_produk, 16) }}</div>
                                <div class="label-barcode">
                                    @php
                                        // Solusi untuk kode panjang: batasi panjang kode untuk barcode
                                        $kode_untuk_barcode = strlen($item->kode_produk) > 12 ? 
                                            substr($item->kode_produk, -12) : $item->kode_produk;
                                        
                                        // Atau gunakan hash jika kode terlalu panjang
                                        if (strlen($item->kode_produk) > 15) {
                                            $kode_untuk_barcode = substr(md5($item->kode_produk), 0, 10);
                                        }
                                    @endphp
                                    {!! DNS1D::getBarcodeHTML($kode_untuk_barcode, 'C39', 1, 20) !!}
                                </div>
                                <div class="label-bottom">
                                    <div class="label-kode">{{ $item->kode_produk }}</div>
                                    <div class="label-harga">{{ number_format($item->harga_jual, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </td>
                    @endforeach

                    @if($chunk->count() < 2)
                        <td></td>
                    @endif
                </tr>
            @endforeach
        </table>
    </div>
</body>
</html>
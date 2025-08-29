<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Barang Habis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        .filter-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .filter-info p {
            margin: 2px 0;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .category-header {
            background-color: #e9ecef;
            font-weight: bold;
            padding: 8px;
            border: 1px solid #ddd;
            margin-top: 10px;
        }
        .auto-badge {
            background-color: #ffc107;
            color: #000;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .manual-badge {
            background-color: #17a2b8;
            color: #fff;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .summary h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        .summary p {
            margin: 3px 0;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DAFTAR BARANG HABIS</h1>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</p>
        <p>Total Item: {{ $totalItems }}</p>
    </div>

    @if(!empty($filterInfo))
    <div class="filter-info">
        <h4>Filter yang Diterapkan:</h4>
        @foreach($filterInfo as $filter)
            <p>• {{ $filter }}</p>
        @endforeach
    </div>
    @endif

    @foreach($groupedData as $category => $items)
        <div class="category-header">
            <i class="fa fa-folder-open"></i> {{ $category }} ({{ $items->count() }} item)
        </div>
        
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="25%">Nama Produk</th>
                    <th width="15%">Merk</th>
                    <th width="8%">Stok</th>
                    <th width="25%">Keterangan</th>
                    <th width="10%">Sumber</th>
                    <th width="12%">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                    <td>{{ $item->produk->merk ?? '-' }}</td>
                    <td style="text-align: center;">{{ $item->produk->stok ?? 0 }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                    <td style="text-align: center;">
                        @if($item->tipe === 'auto')
                            <span class="auto-badge">AUTO</span>
                        @else
                            <span class="manual-badge">MANUAL</span>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="summary">
        <h4>Ringkasan:</h4>
        <p>• Total Kategori: {{ $groupedData->count() }}</p>
        <p>• Total Item: {{ $totalItems }}</p>
        <p>• Auto Sync: {{ $groupedData->flatten()->where('tipe', 'auto')->count() }}</p>
        <p>• Manual Entry: {{ $groupedData->flatten()->where('tipe', 'manual')->count() }}</p>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
        <p>Dokumen ini dibuat otomatis oleh sistem</p>
    </div>
</body>
</html>
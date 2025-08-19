<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang Habis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .filter-info {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .category-group {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .category-header {
            background-color: #e9ecef;
            padding: 8px 10px;
            font-weight: bold;
            font-size: 14px;
            border: 1px solid #dee2e6;
            color: #495057;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            font-size: 11px;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 3px;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-info {
            background-color: #17a2b8;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        .summary-item {
            margin-bottom: 5px;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>DAFTAR BARANG HABIS</h1>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</p>
        <p>Total Items: {{ $totalItems }} barang</p>
    </div>

    {{-- Filter Information --}}
    @if(isset($filterInfo) && count($filterInfo) > 0)
        <div class="filter-info">
            <strong>Filter yang Diterapkan:</strong>
            @foreach($filterInfo as $filter)
                <br>• {{ $filter }}
            @endforeach
        </div>
    @endif

    {{-- Data Content --}}
    @if(isset($groupedData) && $groupedData->count() > 0)
        @foreach($groupedData as $kategoriNama => $items)
            <div class="category-group">
                <div class="category-header">
                    {{ $kategoriNama }} ({{ $items->count() }} item)
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Produk</th>
                            <th width="15%">Merk</th>
                            <th width="8%">Stok</th>
                            <th width="10%">Sumber</th>
                            <th width="27%">Keterangan</th>
                            <th width="10%">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                <td>{{ $item->produk->merk ?? '-' }}</td>
                                <td class="text-center">
                                    @if(isset($item->produk) && $item->produk->stok <= 5)
                                        <strong>{{ $item->produk->stok }}</strong>
                                    @else
                                        {{ $item->produk->stok ?? 0 }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->tipe === 'auto')
                                        <span class="badge badge-warning">Auto</span>
                                    @else
                                        <span class="badge badge-info">Manual</span>
                                    @endif
                                </td>
                                <td>{{ $item->keterangan ?? '-' }}</td>
                                <td class="text-center">{{ $item->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        {{-- Summary --}}
        <div class="summary">
            <h3>Ringkasan:</h3>
            @foreach($groupedData as $kategoriNama => $items)
                <div class="summary-item">
                    <strong>{{ $kategoriNama }}:</strong> {{ $items->count() }} item
                </div>
            @endforeach
            <hr style="margin: 10px 0;">
            <div class="summary-item" style="font-weight: bold;">
                <strong>Total Keseluruhan:</strong> {{ $totalItems }} item
            </div>
        </div>
    @else
        <div class="no-data">
            <h3>Tidak ada data ditemukan</h3>
            <p>Tidak ada barang dalam daftar barang habis sesuai filter yang diterapkan.</p>
        </div>
    @endif

    <div style="position: fixed; bottom: 20px; right: 20px; font-size: 10px; color: #666;">
        Dicetak pada: {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>
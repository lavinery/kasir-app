<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendapatan</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #666;
        }

        .info-section {
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #007bff;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            color: #333;
        }

        .info-value {
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }

        thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            padding: 12px 8px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-size: 10px;
            text-transform: uppercase;
        }

        tbody td {
            padding: 8px;
            border: 1px solid #dee2e6;
            text-align: center;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        tbody tr:hover {
            background-color: #e9ecef;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .total-row {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
            color: white !important;
            font-weight: bold;
            font-size: 12px;
        }

        .total-row td {
            border: 1px solid #28a745 !important;
            padding: 10px 8px;
        }

        .summary-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .summary-box {
            flex: 1;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #28a745;
            text-align: center;
        }

        .summary-box.profit {
            border-left-color: #ffc107;
        }

        .summary-title {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .summary-amount {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px dashed #dee2e6;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }

        .currency {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }

        /* Print specific styles */
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .summary-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Pendapatan</h1>
        <p>Periode: {{ $awal }} sampai {{ $akhir }}</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Tanggal Cetak:</span>
            <span class="info-value">{{ date('d F Y, H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Dicetak oleh:</span>
            <span class="info-value">{{ auth()->user()->name ?? 'System' }}</span>
        </div>
    </div>

    @if (count($data) > 1)
        <table>
            <thead>
                <tr>
                    <th width="8%">No</th>
                    <th width="15%">Tanggal</th>
                    <th width="15%">Penjualan</th>
                    <th width="15%">Pembelian</th>
                    <th width="15%">Pengeluaran</th>
                    <th width="16%">Pendapatan</th>
                    <th width="16%">Keuntungan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $row)
                    @if ($index < count($data) - 1)
                        <tr>
                            <td class="text-center">{{ $row['DT_RowIndex'] }}</td>
                            <td class="text-center">{{ $row['tanggal'] }}</td>
                            <td class="text-right currency">{{ $row['penjualan'] }}</td>
                            <td class="text-right currency">{{ $row['pembelian'] }}</td>
                            <td class="text-right currency">{{ $row['pengeluaran'] }}</td>
                            <td class="text-right currency">{{ $row['pendapatan'] }}</td>
                            <td class="text-right currency">{{ $row['keuntungan'] }}</td>
                        </tr>
                    @else
                        <tr class="total-row">
                            <td colspan="5" class="text-center"><strong>TOTAL KESELURUHAN</strong></td>
                            <td class="text-right currency"><strong>{{ $row['pendapatan'] }}</strong></td>
                            <td class="text-right currency"><strong>{{ $row['keuntungan'] }}</strong></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <div class="summary-section">
            <div class="summary-box">
                <div class="summary-title">Total Pendapatan</div>
                <div class="summary-amount currency">Rp. {{ $total_pendapatan }}</div>
            </div>
            <div class="summary-box profit">
                <div class="summary-title">Total Keuntungan</div>
                <div class="summary-amount currency">Rp. {{ $total_keuntungan }}</div>
            </div>
        </div>
    @else
        <div class="no-data">
            <h3>📊 Tidak Ada Data</h3>
            <p>Tidak ada data transaksi untuk periode <strong>{{ $awal }}</strong> sampai <strong>{{ $akhir }}</strong>.</p>
            <p>Silakan pilih periode yang berbeda atau pastikan ada transaksi pada periode tersebut.</p>
        </div>
    @endif

    <div class="footer">
        <p>
            <strong>Catatan:</strong> 
            Pendapatan = Penjualan - Pembelian - Pengeluaran | 
            Keuntungan = Total keuntungan dari penjualan produk
        </p>
        <p>Laporan ini digenerate secara otomatis oleh sistem pada {{ date('d F Y \p\u\k\u\l H:i:s') }}</p>
    </div>
</body>
</html>
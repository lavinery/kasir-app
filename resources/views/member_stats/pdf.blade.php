<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Total Transaksi Member</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 15px;
        }
        
        .info-row {
            margin-bottom: 3px;
        }
        
        .info-row strong {
            display: inline-block;
            width: 120px;
        }
        
        .summary-section {
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
        }
        
        .summary-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 11px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-cell {
            display: table-cell;
            padding: 3px 10px;
            border-right: 1px solid #ddd;
            text-align: center;
        }
        
        .summary-cell:last-child {
            border-right: none;
        }
        
        .summary-cell strong {
            display: block;
            font-size: 11px;
            color: #333;
        }
        
        .summary-cell span {
            font-size: 9px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table th {
            background-color: #333;
            color: white;
            padding: 6px 4px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #333;
        }
        
        table td {
            padding: 4px 4px;
            border: 1px solid #ddd;
            font-size: 9px;
            text-align: center;
        }
        
        table td.text-left {
            text-align: left;
        }
        
        table td.text-right {
            text-align: right;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        table tbody tr:hover {
            background-color: #f0f0f0;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN TOTAL TRANSAKSI MEMBER</h1>
        <h2>{{ config('app.name', 'Aplikasi Kasir') }}</h2>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-row">
            <strong>Periode:</strong> 
            {{ \Carbon\Carbon::parse($params['start_date'])->format('d/m/Y') }} - 
            {{ \Carbon\Carbon::parse($params['end_date'])->format('d/m/Y') }}
        </div>
        <div class="info-row">
            <strong>Tanggal Cetak:</strong> {{ $generated_at->format('d/m/Y H:i:s') }}
        </div>
        @if($params['min_transactions'] > 0)
        <div class="info-row">
            <strong>Min. Transaksi:</strong> {{ number_format($params['min_transactions']) }}
        </div>
        @endif
        @if($params['min_amount'] > 0)
        <div class="info-row">
            <strong>Min. Belanja:</strong> Rp {{ number_format($params['min_amount']) }}
        </div>
        @endif
        @if(!empty($params['search']))
        <div class="info-row">
            <strong>Pencarian:</strong> "{{ $params['search'] }}"
        </div>
        @endif
    </div>

    <!-- Summary Section -->
    @if($summary)
    <div class="summary-section">
        <div class="summary-title">RINGKASAN TOTAL</div>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">
                    <strong>{{ number_format($summary->total_members) }}</strong>
                    <span>Total Member</span>
                </div>
                <div class="summary-cell">
                    <strong>{{ number_format($summary->grand_total_transaksi) }}</strong>
                    <span>Total Transaksi</span>
                </div>
                <div class="summary-cell">
                    <strong>Rp {{ number_format($summary->grand_total_belanja) }}</strong>
                    <span>Total Belanja</span>
                </div>
                <div class="summary-cell">
                    <strong>{{ number_format($summary->grand_total_item) }}</strong>
                    <span>Total Item</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="12%">Kode Member</th>
                <th width="20%">Nama Member</th>
                <th width="12%">Telepon</th>
                <th width="8%">Total Trx</th>
                <th width="15%">Total Belanja</th>
                <th width="13%">Rata-rata Order</th>
                <th width="8%">Total Item</th>
                <th width="12%">Trx Terakhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $item->kode_member }}</td>
                <td class="text-left">{{ $item->nama }}</td>
                <td class="text-left">{{ $item->telepon ?: '-' }}</td>
                <td>{{ number_format($item->total_transaksi) }}</td>
                <td class="text-right">Rp {{ number_format($item->total_belanja) }}</td>
                <td class="text-right">Rp {{ number_format($item->avg_order_value) }}</td>
                <td>{{ number_format($item->total_item) }}</td>
                <td>
                    {{ $item->last_transaction_date ? \Carbon\Carbon::parse($item->last_transaction_date)->format('d/m/Y') : '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; font-style: italic; color: #666;">
                    Tidak ada data dalam periode yang dipilih
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini digenerate secara otomatis pada {{ $generated_at->format('d/m/Y H:i:s') }}</p>
        <p>{{ config('app.name', 'Aplikasi Kasir') }} - Sistem Manajemen Penjualan</p>
    </div>
</body>
</html>
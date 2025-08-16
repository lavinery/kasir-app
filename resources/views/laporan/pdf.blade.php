<!DOCTYPE html>
<html>

<head>
    <title>Laporan Pendapatan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        p {
            text-align: center;
        }

        .total {
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>

<body>
    <h1>Laporan Pendapatan</h1>
    <p>Periode: {{ $awal }} sampai {{ $akhir }}</p>

    @if (count($data) > 0)
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Penjualan</th>
                    <th>Pembelian</th>
                    <th>Pengeluaran</th>
                    <th>Pendapatan</th>
                    <th>Keuntungan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td>{{ $row['DT_RowIndex'] }}</td>
                        <td>{{ $row['tanggal'] }}</td>
                        <td>{{ $row['penjualan'] }}</td>
                        <td>{{ $row['pembelian'] }}</td>
                        <td>{{ $row['pengeluaran'] }}</td>
                        <td>{{ $row['pendapatan'] }}</td>
                        <td>{{ $row['keuntungan'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada data untuk periode ini.</p>
    @endif

    <p class="total">Total Pendapatan: RP. {{ $total }}</p>
</body>

</html>

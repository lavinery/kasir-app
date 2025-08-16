@extends('layouts.master')

@section('title')
    Laporan Kasir - {{ $kasir->name }}
@endsection

@section('breadcrumb')
    @parent
    <li><a href="{{ route('laporan.kasir.index') }}">Laporan Kasir</a></li>
    <li class="active">Report</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Laporan Kasir - {{ $kasir->name }}</h3>
                    <div class="box-tools pull-right">
                        <button onclick="window.print()" class="btn btn-sm btn-primary"><i class="fa fa-print"></i>
                            Print</button>
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px">Nama Kasir</th>
                            <td>{{ $kasir->name }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ $tanggal->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Total Penjualan</th>
                            <td>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Transaksi</th>
                            <td>{{ $jumlahTransaksi }}</td>
                        </tr>
                    </table>

                    <h4>Detail Penjualan</h4>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Waktu</th>
                                <th>Total Harga</th>
                                <th>Shift</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penjualan as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->created_at->format('H:i:s') }}</td>
                                    <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($item->created_at->format('H:i:s') >= '07:00:00' && $item->created_at->format('H:i:s') < '15:00:00')
                                            Shift 1
                                        @elseif ($item->created_at->format('H:i:s') >= '15:00:00' && $item->created_at->format('H:i:s') < '23:00:00')
                                            Shift 2
                                        @else
                                            Shift 3
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

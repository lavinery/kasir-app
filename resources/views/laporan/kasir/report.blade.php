@extends('layouts.master')

@section('title')
    Laporan Kasir - {{ $kasir->name }}
@endsection

@section('breadcrumb')
    @parent
    <li><a href="{{ route('laporan.kasir.index') }}">Laporan Kasir</a></li>
    <li class="active">Report</li>
@endsection

@push('styles')
<style>
@media print {
    .box-tools, .breadcrumb, .main-header, .main-sidebar, .content-header, .no-print {
        display: none !important;
    }
    .content-wrapper {
        margin-left: 0 !important;
    }
    .box {
        border: none !important;
        box-shadow: none !important;
    }
}
.stat-box {
    background: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: 3px;
    padding: 10px;
    margin-bottom: 10px;
}
.daily-summary {
    background: #f5f5f5;
    border-left: 4px solid #3c8dbc;
    padding: 10px;
    margin: 10px 0;
}
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Laporan Kasir - {{ $kasir->name }}
                        <small>
                            ({{ $tanggalDari->format('d/m/Y') }} - {{ $tanggalSampai->format('d/m/Y') }})
                        </small>
                    </h3>
                    <div class="box-tools pull-right no-print">
                        <a href="{{ route('laporan.kasir.index') }}" class="btn btn-sm btn-default">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                        <button onclick="window.print()" class="btn btn-sm btn-primary">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <!-- Summary Information -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-box text-center">
                                <h4 class="text-primary">{{ $kasir->name }}</h4>
                                <small>Kasir</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box text-center">
                                <h4 class="text-success">{{ $jumlahTransaksi }}</h4>
                                <small>Total Transaksi</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box text-center">
                                <h4 class="text-warning">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h4>
                                <small>Total Penjualan</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box text-center">
                                <h4 class="text-info">
                                    @if($jumlahTransaksi > 0)
                                        Rp {{ number_format($totalPenjualan / $jumlahTransaksi, 0, ',', '.') }}
                                    @else
                                        Rp 0
                                    @endif
                                </h4>
                                <small>Rata-rata per Transaksi</small>
                            </div>
                        </div>
                    </div>

                    <!-- Period and Shift Info -->
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 150px">Periode</th>
                                    <td>{{ $tanggalDari->format('d F Y') }} - {{ $tanggalSampai->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Shift</th>
                                    <td>
                                        @if($shift == 'all')
                                            Semua Shift
                                        @elseif($shift == 1)
                                            Shift 1 (07:00 - 16:00)
                                        @elseif($shift == 2)
                                            Shift 2 (16:00 - 22:00)
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Jumlah Hari</th>
                                    <td>{{ $tanggalDari->diffInDays($tanggalSampai) + 1 }} hari</td>
                                </tr>
                                @if($statistikShift)
                                <tr>
                                    <th>Rata-rata Harian</th>
                                    <td>Rp {{ number_format($statistikShift['rata_rata_harian'], 0, ',', '.') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Daily Statistics -->
                    @if(count($statistikHarian) > 1)
                    <h4>Ringkasan Harian</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah Transaksi</th>
                                    <th>Total Penjualan</th>
                                    <th>Rata-rata per Transaksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statistikHarian as $stat)
                                <tr>
                                    <td>{{ $stat['tanggal'] }}</td>
                                    <td>{{ $stat['jumlah_transaksi'] }}</td>
                                    <td>Rp {{ number_format($stat['total_penjualan'], 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($stat['rata_rata'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light-blue">
                                    <th>Total</th>
                                    <th>{{ $jumlahTransaksi }}</th>
                                    <th>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</th>
                                    <th>
                                        @if($jumlahTransaksi > 0)
                                            Rp {{ number_format($totalPenjualan / $jumlahTransaksi, 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <hr>
                    @endif

                    <!-- Detail Penjualan -->
                    <h4>Detail Transaksi</h4>
                    
                    @if($penjualan->count() > 0)
                        @if(count($statistikHarian) > 1)
                            {{-- Group by date if multiple days --}}
                            @foreach($statistikHarian as $tanggal => $dataHarian)
                                <div class="daily-summary">
                                    <h5>
                                        <i class="fa fa-calendar"></i> {{ $dataHarian['tanggal'] }}
                                        <small class="pull-right">
                                            {{ $dataHarian['jumlah_transaksi'] }} transaksi - 
                                            Rp {{ number_format($dataHarian['total_penjualan'], 0, ',', '.') }}
                                        </small>
                                    </h5>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="15%">Waktu</th>
                                                <th width="20%">Total Harga</th>
                                                <th width="15%">Shift</th>
                                                <th width="45%">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dataHarian['transaksi'] as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('H:i:s') }}</td>
                                                    <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                                    <td>
                                                        @php
                                                            $waktu = \Carbon\Carbon::parse($item->created_at)->format('H:i:s');
                                                        @endphp
                                                        @if ($waktu >= '07:00:00' && $waktu < '16:00:00')
                                                            <span class="label label-success">Shift 1</span>
                                                        @elseif ($waktu >= '16:00:00' && $waktu < '22:00:00')
                                                            <span class="label label-info">Shift 2</span>
                                                        @else
                                                            <span class="label label-warning">Luar Shift</span>
                                                        @endif
                                                    </td>
                                                    <td>Transaksi #{{ $item->id ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        @else
                            {{-- Single day view --}}
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="15%">Waktu</th>
                                            <th width="20%">Total Harga</th>
                                            <th width="15%">Shift</th>
                                            <th width="45%">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($penjualan as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('H:i:s') }}</td>
                                                <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                                <td>
                                                    @php
                                                        $waktu = \Carbon\Carbon::parse($item->created_at)->format('H:i:s');
                                                    @endphp
                                                    @if ($waktu >= '07:00:00' && $waktu < '16:00:00')
                                                        <span class="label label-success">Shift 1</span>
                                                    @elseif ($waktu >= '16:00:00' && $waktu < '22:00:00')
                                                        <span class="label label-info">Shift 2</span>
                                                    @else
                                                        <span class="label label-warning">Luar Shift</span>
                                                    @endif
                                                </td>
                                                <td>Transaksi #{{ $item->id ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-light-blue">
                                            <th colspan="2">Total</th>
                                            <th>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</th>
                                            <th>{{ $jumlahTransaksi }} transaksi</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Tidak ada data penjualan untuk kasir <strong>{{ $kasir->name }}</strong> 
                            pada periode <strong>{{ $tanggalDari->format('d F Y') }} - {{ $tanggalSampai->format('d F Y') }}</strong>
                            @if($shift !== 'all')
                                untuk shift <strong>{{ $shift }}</strong>
                            @endif
                            .
                        </div>
                    @endif

                    <!-- Report Generation Info -->
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-12">
                            <small class="text-muted">
                                <i class="fa fa-info-circle"></i>
                                Laporan ini dibuat pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
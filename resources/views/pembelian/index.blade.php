@extends('layouts.master')

@section('title')
    Daftar Pembelian
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Pembelian</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-md-6">
                        <button onclick="addForm()" class="btn btn-success btn-sm btn-flat"><i class="fa fa-plus-circle"></i> Transaksi Baru</button>
                        @empty(! session('id_pembelian'))
                        <a href="{{ route('pembelian_detail.index') }}" class="btn btn-info btn-sm btn-flat"><i class="fa fa-pencil"></i> Transaksi Aktif</a>
                        @endempty
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary active btn-sm" id="btn-daily-summary">
                                <i class="fa fa-calendar"></i> Ringkasan Harian
                            </button>
                            <button type="button" class="btn btn-default btn-sm" id="btn-all-data">
                                <i class="fa fa-list"></i> Semua Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body table-responsive">
                <!-- Tabel Ringkasan Harian -->
                <div id="daily-summary-view">
                    <table class="table table-striped table-bordered table-daily-summary">
                        <thead>
                            <th width="5%">No</th>
                            <th>Tanggal</th>
                            <th>Total Transaksi</th>
                            <th>Total Item</th>
                            <th>Total Pembelian</th>
                            <th width="10%">Aksi</th>
                        </thead>
                    </table>
                </div>

                <!-- Tabel Detail Harian (akan muncul saat tanggal diklik) -->
                <div id="daily-details-view" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>Detail Pembelian:</strong> <span id="selected-date"></span>
                        <button type="button" class="btn btn-xs btn-default pull-right" id="btn-back-to-summary">
                            <i class="fa fa-arrow-left"></i> Kembali ke Ringkasan
                        </button>
                        <div class="clearfix"></div>
                    </div>
                    <table class="table table-striped table-bordered table-daily-details">
                        <thead>
                            <th width="5%">No</th>
                            <th>Waktu</th>
                            <th>Supplier</th>
                            <th>Total Item</th>
                            <th>Total Harga</th>
                            <th>Diskon</th>
                            <th>Total Bayar</th>
                            <th width="15%">Aksi</th>
                        </thead>
                    </table>
                </div>

                <!-- Tabel Semua Data (tampilan lama) -->
                <div id="all-data-view" style="display: none;">
                    <table class="table table-stiped table-bordered table-pembelian">
                        <thead>
                            <th width="5%">No</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Total Item</th>
                            <th>Total Harga</th>
                            <th>Diskon</th>
                            <th>Total Bayar</th>
                            <th width="15%"><i class="fa fa-cog"></i></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@includeIf('pembelian.supplier')
@includeIf('pembelian.detail')
@endsection

@push('scripts')
<script>
    let table, table1, tableDailySummary, tableDailyDetails = null;
    let dailyDetailsColumns = [];

    $(function () {
        // Initialize Daily Summary Table
        let dailySummaryColumns = [
            { data: 'DT_RowIndex', searchable: false, sortable: false },
            { data: 'tanggal' },
            { data: 'total_transaksi' },
            { data: 'total_item' },
            { data: 'total_pembelian' },
            { data: 'aksi', searchable: false, sortable: false }
        ];

        tableDailySummary = $('.table-daily-summary').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('pembelian.daily_summary') }}',
            },
            columns: dailySummaryColumns
        });

        // Setup Daily Details Columns
        dailyDetailsColumns = [
            { data: 'DT_RowIndex', searchable: false, sortable: false },
            { data: 'waktu' },
            { data: 'supplier' },
            { data: 'total_item' },
            { data: 'total_harga' },
            { data: 'diskon' },
            { data: 'bayar' },
            { data: 'aksi', searchable: false, sortable: false }
        ];

        // Initialize Original All Data Table
        table = $('.table-pembelian').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('pembelian.data') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'tanggal'},
                {data: 'supplier'},
                {data: 'total_item'},
                {data: 'total_harga'},
                {data: 'diskon'},
                {data: 'bayar'},
                {data: 'aksi', searchable: false, sortable: false},
            ]
        });

        $('.table-supplier').DataTable();
        table1 = $('.table-detail').DataTable({
            processing: true,
            bSort: false,
            dom: 'Brt',
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'harga_beli'},
                {data: 'jumlah'},
                {data: 'subtotal'},
            ]
        });

        // View Toggle Buttons
        $('#btn-daily-summary').click(function() {
            $('#btn-daily-summary').addClass('btn-primary active').removeClass('btn-default');
            $('#btn-all-data').addClass('btn-default').removeClass('btn-primary active');
            $('#daily-summary-view').show();
            $('#all-data-view').hide();
            $('#daily-details-view').hide();
            tableDailySummary.ajax.reload();
        });

        $('#btn-all-data').click(function() {
            $('#btn-all-data').addClass('btn-primary active').removeClass('btn-default');
            $('#btn-daily-summary').addClass('btn-default').removeClass('btn-primary active');
            $('#all-data-view').show();
            $('#daily-summary-view').hide();
            $('#daily-details-view').hide();
            table.ajax.reload();
        });

        $('#btn-back-to-summary').click(function() {
            $('#daily-details-view').hide();
            $('#daily-summary-view').show();
        });
    });

    function loadDailyDetails(date) {
        $('#selected-date').text(date);
        $('#daily-summary-view').hide();
        $('#daily-details-view').show();

        // Destroy existing table if it exists
        if (tableDailyDetails) {
            tableDailyDetails.destroy();
        }

        // Initialize table with data
        tableDailyDetails = $('.table-daily-details').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ url('pembelian/daily-details') }}/' + date,
            },
            columns: dailyDetailsColumns
        });
    }

    function addForm() {
        $('#modal-supplier').modal('show');
    }

    function showDetail(url) {
        $('#modal-detail').modal('show');

        table1.ajax.url(url);
        table1.ajax.reload();
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    // Reload semua tabel yang aktif
                    if (table) {
                        table.ajax.reload();
                    }
                    if (tableDailySummary) {
                        tableDailySummary.ajax.reload();
                    }
                    if (tableDailyDetails) {
                        tableDailyDetails.ajax.reload();
                    }

                    alert('Data berhasil dihapus');
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }
</script>
@endpush
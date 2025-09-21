@extends('layouts.master')

@section('title')
    Daftar Pengeluaran
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Pengeluaran</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-md-6">
                        <button onclick="addForm('{{ route('pengeluaran.store') }}')" class="btn btn-success btn-sm btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
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
            <div class="box-body">
                <!-- Tabel Ringkasan Harian -->
                <div id="daily-summary-view">
                    <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <table class="table table-striped table-bordered table-daily-summary" style="min-width: 500px;">
                            <thead>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Total Transaksi</th>
                                <th>Total Pengeluaran</th>
                                <th width="10%">Aksi</th>
                            </thead>
                        </table>
                    </div>
                </div>

                <!-- Tabel Detail Harian (akan muncul saat tanggal diklik) -->
                <div id="daily-details-view" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>Detail Pengeluaran:</strong> <span id="selected-date"></span>
                        <button type="button" class="btn btn-xs btn-default pull-right" id="btn-back-to-summary">
                            <i class="fa fa-arrow-left"></i> Kembali ke Ringkasan
                        </button>
                        <div class="clearfix"></div>
                    </div>
                    <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <table class="table table-striped table-bordered table-daily-details" style="min-width: 500px;">
                            <thead>
                                <th width="5%">No</th>
                                <th>Waktu</th>
                                <th>Deskripsi</th>
                                <th>Nominal</th>
                                <th width="15%">Aksi</th>
                            </thead>
                        </table>
                    </div>
                </div>

                <!-- Tabel Semua Data (tampilan lama) -->
                <div id="all-data-view" style="display: none;">
                    <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <table class="table table-stiped table-bordered table-pengeluaran" style="min-width: 500px;">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Tanggal</th>
                                    <th>Deskripsi</th>
                                    <th>Nominal</th>
                                    <th width="15%"><i class="fa fa-cog"></i></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@includeIf('pengeluaran.form')
@endsection

@push('scripts')
<script>
    let table, tableDailySummary, tableDailyDetails = null;
    let dailyDetailsColumns = [];

    $(function () {
        // Initialize Daily Summary Table
        let dailySummaryColumns = [
            { data: 'DT_RowIndex', searchable: false, sortable: false },
            { data: 'tanggal' },
            { data: 'total_transaksi' },
            { data: 'total_pengeluaran' },
            { data: 'aksi', searchable: false, sortable: false }
        ];

        tableDailySummary = $('.table-daily-summary').DataTable({
            responsive: false,
            processing: true,
            serverSide: true,
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true,
            ajax: {
                url: '{{ route('pengeluaran.daily_summary') }}',
            },
            columns: dailySummaryColumns
        });

        // Setup Daily Details Columns
        dailyDetailsColumns = [
            { data: 'DT_RowIndex', searchable: false, sortable: false },
            { data: 'waktu' },
            { data: 'deskripsi' },
            { data: 'nominal' },
            { data: 'aksi', searchable: false, sortable: false }
        ];

        // Initialize Original All Data Table
        table = $('.table-pengeluaran').DataTable({
            responsive: false,
            processing: true,
            serverSide: true,
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true,
            ajax: {
                url: '{{ route('pengeluaran.data') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false, width: '50px'},
                {data: 'created_at', width: '120px'},
                {data: 'deskripsi', width: '200px'},
                {data: 'nominal', width: '120px'},
                {data: 'aksi', searchable: false, sortable: false, width: '120px'},
            ]
        });

        // Initialize form validation
        $('#modal-form form').validate();

        $('#modal-form form').on('submit', function (e) {
            e.preventDefault();
            if ($(this).valid()) {
                $.post($(this).attr('action'), $(this).serialize())
                    .done((response) => {
                        $('#modal-form').modal('hide');

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
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menyimpan data');
                        return;
                    });
            }
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
            responsive: false,
            processing: true,
            serverSide: true,
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true,
            ajax: {
                url: '{{ url('pengeluaran/daily-details') }}/' + date,
            },
            columns: dailyDetailsColumns
        });
    }

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Pengeluaran');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=deskripsi]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Pengeluaran');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=deskripsi]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=deskripsi]').val(response.deskripsi);
                $('#modal-form [name=nominal]').val(response.nominal);
            })
            .fail((errors) => {
                alert('Tidak dapat menampilkan data');
                return;
            });
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
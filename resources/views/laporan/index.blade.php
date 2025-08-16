@extends('layouts.master')

@section('title')
    Laporan Pendapatan
@endsection

@push('css')
    <link rel="stylesheet"
        href="{{ asset('/AdminLTE-2/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/select2/dist/css/select2.min.css') }}">
    <style>
        .select2-container--default .select2-selection--single {
            height: 34px;
            padding: 6px 12px;
        }
    </style>
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Laporan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title" id="report-title">Laporan Pendapatan</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Filter Bulan</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="bulan-picker">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Filter Tahun</label>
                                <select id="tahun-picker" class="form-control select2">
                                    @for ($i = date('Y'); $i >= 2000; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button id="filter-btn" class="btn btn-primary">Filter</button>
                            <a id="export-pdf" href="#" target="_blank" class="btn btn-success">
                                <i class="fa fa-file-pdf-o"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Penjualan</th>
                                <th>Pembelian</th>
                                <th>Pengeluaran</th>
                                <th>Pendapatan</th>
                                <th>Keuntungan</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('/AdminLTE-2/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('/AdminLTE-2/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        let table;

        $(function () {
            $('.select2').select2();

            table = $('.table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('laporan.data', ['awal' => 'START_DATE', 'akhir' => 'END_DATE']) }}'
                        .replace('START_DATE', '{{ $tanggalAwal }}')
                        .replace('END_DATE', '{{ $tanggalAkhir }}'),
                },
                columns: [
                    { data: 'DT_RowIndex', searchable: false, sortable: false },
                    { data: 'tanggal' },
                    { data: 'penjualan' },
                    { data: 'pembelian' },
                    { data: 'pengeluaran' },
                    { data: 'pendapatan' },
                    { data: 'keuntungan' }
                ],
                dom: 'Brt',
                bSort: false,
                bPaginate: false,
            });

            $('#bulan-picker').datepicker({
                format: 'yyyy-mm',
                viewMode: 'months',
                minViewMode: 'months',
                autoclose: true
            });

            $('#filter-btn').click(function () {
                let bulan = $('#bulan-picker').val();
                let tahun = $('#tahun-picker').val();

                if (bulan) {
                    let [year, month] = bulan.split('-');
                    let tanggalAwal = `${year}-${month}-01`;
                    let tanggalAkhir = `${year}-${month}-${new Date(year, month, 0).getDate()}`;
                    updateReport(tanggalAwal, tanggalAkhir, `Laporan Pendapatan Bulan ${getMonthName(month)} ${year}`);
                } else if (tahun) {
                    let tanggalAwal = `${tahun}-01-01`;
                    let tanggalAkhir = `${tahun}-12-31`;
                    updateReport(tanggalAwal, tanggalAkhir, `Laporan Pendapatan Tahun ${tahun}`);
                }
            });
        });

        function updateReport(tanggalAwal, tanggalAkhir, title) {
            updateTable(tanggalAwal, tanggalAkhir);
            updateExportUrl(tanggalAwal, tanggalAkhir);
            updateTitle(title);
        }

        function updateTable(tanggalAwal, tanggalAkhir) {
            table.ajax.url(
                '{{ route('laporan.data', ['awal' => 'START_DATE', 'akhir' => 'END_DATE']) }}'
                    .replace('START_DATE', tanggalAwal)
                    .replace('END_DATE', tanggalAkhir)
            ).load();
        }

        function updateExportUrl(tanggalAwal, tanggalAkhir) {
            $('#export-pdf').attr('href',
                '{{ route('laporan.export_pdf', ['awal' => 'START_DATE', 'akhir' => 'END_DATE']) }}'
                    .replace('START_DATE', tanggalAwal)
                    .replace('END_DATE', tanggalAkhir)
            );
        }

        function updateTitle(title) {
            $('#report-title').text(title);
            $('title').text(title);
        }

        function getMonthName(month) {
            const monthNames = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];
            return monthNames[parseInt(month) - 1];
        }
    </script>
@endpush

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
        
        /* Gen Z styling untuk laporan */
        .box {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.1);
            border: 1px solid rgba(102, 126, 234, 0.2);
        }
        
        .box-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 15px 20px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
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
                    <h3 class="box-title" id="report-title">📊 Laporan Pendapatan Bulan {{ date('F Y') }}</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-info btn-sm" onclick="updatePeriode()">
                            <i class="fa fa-calendar"></i> Ubah Periode
                        </button>
                    </div>
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
                                    <input type="text" class="form-control pull-right" id="bulan-picker" 
                                           value="{{ date('Y-m') }}" placeholder="Pilih bulan...">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Filter Tahun</label>
                                <select id="tahun-picker" class="form-control select2">
                                    <option value="">Pilih tahun...</option>
                                    @for ($i = date('Y'); $i >= 2000; $i--)
                                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button id="filter-btn" class="btn btn-primary">
                                <i class="fa fa-filter"></i> Filter Data
                            </button>
                            <a id="export-pdf" href="{{ route('laporan.export_pdf', [$tanggalAwal, $tanggalAkhir]) }}" 
                               target="_blank" class="btn btn-success">
                                <i class="fa fa-file-pdf-o"></i> Export PDF
                            </a>
                            <button id="reset-filter" class="btn btn-warning">
                                <i class="fa fa-refresh"></i> Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Penjualan</th>
                                <th class="text-center">Pembelian</th>
                                <th class="text-center">Pengeluaran</th>
                                <th class="text-center">Pendapatan</th>
                                <th class="text-center">Keuntungan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Custom Periode -->
    <div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('laporan.index') }}" method="get" data-toggle="validator" class="form-horizontal" id="form-periode">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">📅 Ubah Periode Laporan</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="tanggal_awal" class="col-lg-2 col-lg-offset-1 control-label">Tanggal Awal</label>
                            <div class="col-lg-6">
                                <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" required autofocus
                                    value="{{ $tanggalAwal }}"
                                    style="border-radius: 8px !important;">
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tanggal_akhir" class="col-lg-2 col-lg-offset-1 control-label">Tanggal Akhir</label>
                            <div class="col-lg-6">
                                <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" required
                                    value="{{ $tanggalAkhir }}"
                                    style="border-radius: 8px !important;">
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Terapkan Filter
                        </button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <i class="fa fa-times"></i> Batal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('/AdminLTE-2/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('/AdminLTE-2/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        let table;
        let currentStartDate = '{{ $tanggalAwal }}';
        let currentEndDate = '{{ $tanggalAkhir }}';

        $(function () {
            // Initialize plugins
            $('.select2').select2({
                placeholder: "Pilih tahun...",
                allowClear: true
            });

            // Initialize DataTable dengan route yang benar
            table = $('.table').DataTable({
                responsive: true,
                processing: true,
                serverSide: false,
                autoWidth: false,
                ajax: {
                    url: '{{ route('laporan.data', [$tanggalAwal, $tanggalAkhir]) }}',
                    dataSrc: function(json) {
                        // Log untuk debugging
                        console.log('Data received:', json);
                        return json.data || json;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', searchable: false, sortable: false, className: 'text-center' },
                    { data: 'tanggal', className: 'text-center' },
                    { data: 'penjualan', className: 'text-right' },
                    { data: 'pembelian', className: 'text-right' },
                    { data: 'pengeluaran', className: 'text-right' },
                    { data: 'pendapatan', className: 'text-right' },
                    { data: 'keuntungan', className: 'text-right' }
                ],
                dom: 'Brt',
                bSort: false,
                bPaginate: false,
                language: {
                    processing: "⏳ Memuat data...",
                    emptyTable: "📊 Tidak ada data untuk periode ini",
                    zeroRecords: "🔍 Data tidak ditemukan",
                    loadingRecords: "⏳ Memuat..."
                },
                drawCallback: function(settings) {
                    // Add styling to total row if exists
                    $('tbody tr:last-child').addClass('info');
                },
                error: function(xhr, error, thrown) {
                    console.log('DataTables error:', error, thrown);
                    alert('Error loading data: ' + error);
                }
            });

            // Initialize month picker
            $('#bulan-picker').datepicker({
                format: 'yyyy-mm',
                viewMode: 'months',
                minViewMode: 'months',
                autoclose: true,
                language: 'id',
                todayHighlight: true
            });

            // Filter button event
            $('#filter-btn').click(function () {
                applyFilter();
            });

            // Reset filter button event
            $('#reset-filter').click(function () {
                resetFilter();
            });

            // Enter key support for inputs
            $('#bulan-picker, #tahun-picker').on('keypress', function(e) {
                if (e.which === 13) {
                    applyFilter();
                }
            });

            // Form periode submit handler
            $('#form-periode').on('submit', function(e) {
                e.preventDefault();
                let tanggalAwal = $('#tanggal_awal').val();
                let tanggalAkhir = $('#tanggal_akhir').val();
                
                if (tanggalAwal && tanggalAkhir) {
                    let title = `📊 Laporan Pendapatan ${formatDate(tanggalAwal)} - ${formatDate(tanggalAkhir)}`;
                    updateReport(tanggalAwal, tanggalAkhir, title);
                    $('#modal-form').modal('hide');
                }
            });
        });

        function updatePeriode() {
            $('#modal-form').modal('show');
        }

        function applyFilter() {
            let bulan = $('#bulan-picker').val();
            let tahun = $('#tahun-picker').val();
            let tanggalAwal, tanggalAkhir, title;

            if (bulan) {
                // Filter berdasarkan bulan
                let [year, month] = bulan.split('-');
                tanggalAwal = `${year}-${month}-01`;
                tanggalAkhir = `${year}-${month}-${new Date(year, month, 0).getDate()}`;
                title = `📊 Laporan Pendapatan Bulan ${getMonthName(month)} ${year}`;
            } else if (tahun) {
                // Filter berdasarkan tahun
                tanggalAwal = `${tahun}-01-01`;
                tanggalAkhir = `${tahun}-12-31`;
                title = `📊 Laporan Pendapatan Tahun ${tahun}`;
            } else {
                // Default periode (bulan ini)
                let today = new Date();
                let year = today.getFullYear();
                let month = String(today.getMonth() + 1).padStart(2, '0');
                tanggalAwal = `${year}-${month}-01`;
                tanggalAkhir = `${year}-${month}-${new Date(year, today.getMonth() + 1, 0).getDate()}`;
                title = `📊 Laporan Pendapatan Bulan ${getMonthName(month)} ${year}`;
            }

            updateReport(tanggalAwal, tanggalAkhir, title);
        }

        function resetFilter() {
            $('#bulan-picker').val('');
            $('#tahun-picker').val('').trigger('change');
            
            // Reset ke periode default (bulan ini)
            let today = new Date();
            let year = today.getFullYear();
            let month = String(today.getMonth() + 1).padStart(2, '0');
            let tanggalAwal = `${year}-${month}-01`;
            let tanggalAkhir = `${year}-${month}-${new Date(year, today.getMonth() + 1, 0).getDate()}`;
            let title = `📊 Laporan Pendapatan Bulan ${getMonthName(month)} ${year}`;
            
            updateReport(tanggalAwal, tanggalAkhir, title);
        }

        function updateReport(tanggalAwal, tanggalAkhir, title) {
            currentStartDate = tanggalAwal;
            currentEndDate = tanggalAkhir;
            
            // Show loading
            $('#filter-btn').html('<i class="fa fa-spinner fa-spin"></i> Memuat...');
            
            updateTable(tanggalAwal, tanggalAkhir);
            updateExportUrl(tanggalAwal, tanggalAkhir);
            updateTitle(title);
            
            // Reset button text
            setTimeout(() => {
                $('#filter-btn').html('<i class="fa fa-filter"></i> Filter Data');
            }, 1000);
        }

        function updateTable(tanggalAwal, tanggalAkhir) {
            // PERBAIKAN: Gunakan route yang benar sesuai dengan controller
            let newUrl = '{{ route('laporan.data', ['awal' => 'START_DATE', 'akhir' => 'END_DATE']) }}'
                .replace('START_DATE', tanggalAwal)
                .replace('END_DATE', tanggalAkhir);
            
            console.log('Updating table with URL:', newUrl);
            
            table.ajax.url(newUrl).load(function(json) {
                console.log('Table data loaded:', json);
            }, function(xhr, error, thrown) {
                console.log('Error loading table:', error, thrown);
            });
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
            document.title = title;
        }

        function getMonthName(month) {
            const monthNames = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];
            return monthNames[parseInt(month) - 1];
        }

        function formatDate(dateString) {
            let date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long', 
                year: 'numeric'
            });
        }
    </script>
@endpush
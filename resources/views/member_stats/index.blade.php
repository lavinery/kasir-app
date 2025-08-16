@extends('layouts.master')

@section('title')
    Total Transaksi Member
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Total Transaksi Member</li>
@endsection

@push('css')
<!-- AdminLTE CSS sudah loaded, tambahan styling -->
<style>
    .filter-box {
        background: #f9f9f9;
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .filter-row {
        margin-bottom: 15px;
    }
    
    .filter-row:last-child {
        margin-bottom: 0;
    }
    
    .filter-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
        display: block;
    }
    
    .summary-cards {
        margin-bottom: 20px;
    }
    
    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        transition: transform 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .summary-card.members { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .summary-card.transactions { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .summary-card.revenue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .summary-card.items { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    
    .summary-card .number {
        font-size: 28px;
        font-weight: bold;
        margin: 0;
        line-height: 1.2;
    }
    
    .summary-card .label {
        font-size: 14px;
        margin: 5px 0 0 0;
        opacity: 0.9;
    }
    
    .summary-card .icon {
        float: right;
        font-size: 40px;
        opacity: 0.3;
        margin-top: -10px;
    }
    
    .export-section {
        background: #fff;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .btn-export {
        margin-right: 10px;
        margin-bottom: 5px;
    }
    
    .table-container {
        background: #fff;
        border-radius: 5px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .table-responsive {
        border: none;
    }
    
    #member-stats-table thead th {
        background: #f4f4f4;
        border: 1px solid #ddd;
        font-weight: 600;
        color: #333;
        text-align: center;
        vertical-align: middle;
        padding: 12px 8px;
        font-size: 12px;
    }
    
    #member-stats-table tbody td {
        border: 1px solid #eee;
        padding: 10px 8px;
        vertical-align: middle;
        font-size: 13px;
    }
    
    #member-stats-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .member-code {
        background: #3c8dbc;
        color: white;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: bold;
        display: inline-block;
    }
    
    .member-name {
        font-weight: 600;
        color: #333;
    }
    
    .amount {
        font-weight: 600;
        color: #00a65a;
    }
    
    .date-text {
        font-size: 12px;
        color: #666;
    }
    
    .btn-detail {
        padding: 4px 10px;
        font-size: 11px;
    }
    
    .checkbox-container {
        border: none !important;
        background: transparent !important;
        height: auto !important;
        padding: 8px 0 !important;
    }
    
    .checkbox-container label {
        font-weight: normal !important;
        margin: 0 !important;
        color: #555 !important;
        cursor: pointer;
    }
    
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        display: none;
        z-index: 1000;
    }
    
    .loading-spinner {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 24px;
        color: #3c8dbc;
    }
    
    .alert-custom {
        margin-bottom: 20px;
        border-radius: 5px;
    }
    
    .period-info {
        background: #e8f4f8;
        border: 1px solid #b8daff;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 15px;
        font-size: 13px;
        color: #004085;
    }
    
    .quick-filters {
        margin-bottom: 15px;
    }
    
    .quick-filter-btn {
        margin-right: 5px;
        margin-bottom: 5px;
        font-size: 12px;
        padding: 5px 12px;
    }
    
    /* Modal styling */
    .modal-lg {
        width: 95%;
        max-width: 1200px;
    }
    
    #detail-table thead th {
        background: #f8f9fa;
        font-weight: 600;
        text-align: center;
        padding: 10px 5px;
        font-size: 12px;
        border: 1px solid #dee2e6;
    }
    
    #detail-table tbody td {
        padding: 8px 5px;
        font-size: 12px;
        border: 1px solid #dee2e6;
        text-align: center;
    }
    
    .member-detail-header {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 15px;
    }
    
    .detail-summary {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .detail-summary-item {
        text-align: center;
        min-width: 120px;
    }
    
    .detail-summary-item .value {
        font-size: 18px;
        font-weight: bold;
        color: #3c8dbc;
        margin: 0;
    }
    
    .detail-summary-item .label {
        font-size: 12px;
        color: #666;
        margin: 5px 0 0 0;
    }
    
    /* 🔄 NEW SYNC STYLES */
    .sync-status-bar {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        padding: 10px 15px;
        border-radius: 4px;
        margin-bottom: 15px;
    }
    
    .data-status-info {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 4px;
        border: 1px solid #e9ecef;
    }
    
    .data-status-info h6 {
        color: #333;
        font-weight: 600;
        margin: 0;
    }
    
    #data-status {
        font-size: 12px;
    }
    
    #last-sync-info {
        display: block;
        margin-top: 3px;
        font-size: 11px;
    }
    
    /* Enhanced Button Styles */
    #btn-refresh {
        position: relative;
        overflow: hidden;
    }
    
    #btn-refresh:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    #btn-refresh.syncing {
        background: linear-gradient(-45deg, #28a745, #20c997, #28a745, #20c997);
        background-size: 400% 400%;
        animation: syncGradient 2s ease infinite;
    }
    
    @keyframes syncGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    /* Enhanced Summary Cards dengan Sync Status */
    .summary-card.syncing {
        position: relative;
        overflow: hidden;
    }
    
    .summary-card.syncing::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        animation: shimmer 1.5s infinite;
    }
    
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    /* Enhanced Alert Styles */
    .alert-custom.sync-alert {
        border-left: 4px solid #28a745;
        background: linear-gradient(to right, #d4edda, #ffffff);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .filter-row .col-md-3,
        .filter-row .col-md-2 {
            margin-bottom: 15px;
        }
        
        .summary-card {
            margin-bottom: 10px;
        }
        
        .btn-export {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .table-responsive {
            font-size: 11px;
        }
        
        .modal-lg {
            width: 95%;
        }
        
        .box-tools .btn {
            margin-bottom: 5px;
        }
        
        .data-status-info {
            margin-top: 15px;
            text-align: center;
        }
        
        .sync-status-bar {
            text-align: center;
        }
    }
    
    /* DataTables custom styling */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        color: #333;
        margin-bottom: 10px;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 5px 10px;
        margin: 0 2px;
        border-radius: 3px;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #3c8dbc;
        color: white !important;
        border: 1px solid #3c8dbc;
    }
    
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #ddd;
        border-radius: 3px;
        padding: 5px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            {{-- 🔄 ENHANCED BOX HEADER DENGAN SYNC BUTTON --}}
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-line-chart"></i> Total Transaksi Member
                </h3>
                <div class="box-tools pull-right">
                    {{-- Enhanced Sync Button --}}
                    <button type="button" class="btn btn-sm btn-success" id="btn-refresh" 
                            title="Sinkronisasi Data (Ctrl+R)" data-toggle="tooltip">
                        <i class="fa fa-refresh"></i> Sync Data
                    </button>
                    
                    {{-- System Status Button --}}
                    <button type="button" class="btn btn-sm btn-info" onclick="checkSystemStatus()" 
                            title="Cek Status System" data-toggle="tooltip">
                        <i class="fa fa-heartbeat"></i>
                    </button>
                    
                    {{-- Collapse Button --}}
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            
            <div class="box-body">
                
                {{-- 🔄 ENHANCED ALERT CONTAINER --}}
                <div id="alert-container"></div>
                
                {{-- 🔄 SYNC STATUS BAR (NEW) --}}
                <div class="sync-status-bar" id="sync-status" style="display: none;">
                    <div class="progress progress-xs">
                        <div class="progress-bar progress-bar-success progress-bar-striped active" 
                             style="width: 100%"></div>
                    </div>
                    <small class="text-muted">
                        <i class="fa fa-clock-o"></i> 
                        <span id="sync-status-text">Mempersiapkan sinkronisasi...</span>
                    </small>
                </div>
                
                <!-- Filter Section -->
                <div class="filter-box">
                    <h4 style="margin-top: 0;"><i class="fa fa-filter"></i> Filter & Pencarian</h4>
                    
                    <!-- Quick Filters -->
                    <div class="quick-filters">
                        <button type="button" class="btn btn-sm btn-default quick-filter-btn" data-period="7">7 Hari Terakhir</button>
                        <button type="button" class="btn btn-sm btn-default quick-filter-btn" data-period="30">30 Hari Terakhir</button>
                        <button type="button" class="btn btn-sm btn-default quick-filter-btn" data-period="90">3 Bulan Terakhir</button>
                        <button type="button" class="btn btn-sm btn-default quick-filter-btn" data-period="365">1 Tahun Terakhir</button>
                    </div>
                    
                    <form id="filter-form">
                        <div class="row filter-row">
                            <div class="col-md-3">
                                <label class="filter-label">
                                    <i class="fa fa-calendar"></i> Tanggal Mulai
                                </label>
                                <input type="date" name="start_date" id="start_date" class="form-control" 
                                       value="{{ \Carbon\Carbon::now()->subDays(30)->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="filter-label">
                                    <i class="fa fa-calendar"></i> Tanggal Akhir
                                </label>
                                <input type="date" name="end_date" id="end_date" class="form-control" 
                                       value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="filter-label">
                                    <i class="fa fa-shopping-cart"></i> Min. Transaksi
                                </label>
                                <input type="number" name="min_transactions" id="min_transactions" 
                                       class="form-control" placeholder="Contoh: 5" min="0" step="1">
                            </div>
                            <div class="col-md-2">
                                <label class="filter-label">
                                    <i class="fa fa-money"></i> Min. Belanja
                                </label>
                                <input type="number" name="min_amount" id="min_amount" 
                                       class="form-control" placeholder="Contoh: 100000" min="0" step="1000">
                            </div>
                            <div class="col-md-2">
                                <label class="filter-label">
                                    <i class="fa fa-cog"></i> Opsi Tampilan
                                </label>
                                <div class="checkbox-container form-control">
                                    <label>
                                        <input type="checkbox" name="show_all_members" id="show_all_members"> 
                                        Tampilkan Semua Member
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <hr style="margin: 15px 0;">
                                <button type="button" id="btn-filter" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Terapkan Filter
                                </button>
                                <button type="button" id="btn-reset" class="btn btn-default">
                                    <i class="fa fa-refresh"></i> Reset Filter
                                </button>
                                <button type="button" id="btn-help" class="btn btn-info pull-right">
                                    <i class="fa fa-question-circle"></i> Bantuan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Period Info -->
                <div class="period-info" id="period-info" style="display: none;">
                    <i class="fa fa-info-circle"></i> 
                    <span id="period-text"></span>
                </div>

                <!-- Summary Cards -->
                <div class="row summary-cards" id="summary-cards" style="display: none;">
                    <div class="col-lg-3 col-md-6">
                        <div class="summary-card members">
                            <div class="icon">
                                <i class="fa fa-users"></i>
                            </div>
                            <h3 class="number" id="total-members">0</h3>
                            <p class="label">Total Member Aktif</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="summary-card transactions">
                            <div class="icon">
                                <i class="fa fa-shopping-cart"></i>
                            </div>
                            <h3 class="number" id="total-transactions">0</h3>
                            <p class="label">Total Transaksi</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="summary-card revenue">
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                            <h3 class="number" id="grand-total">Rp 0</h3>
                            <p class="label">Total Pendapatan</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="summary-card items">
                            <div class="icon">
                                <i class="fa fa-cubes"></i>
                            </div>
                            <h3 class="number" id="total-items">0</h3>
                            <p class="label">Total Item Terjual</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let table;
    let detailTable;
    let currentFilters = {};
    let memberData = {};
    let syncInProgress = false;

    $(function () {
        // Initialize DataTable
        initializeDataTable();
        
        // Initialize detail table
        initializeDetailTable();
        
        // Bind events
        bindEvents();
        
        // Load initial data
        applyFilter();
        
        // Start auto-refresh
        startAutoRefresh();
    });
    
    function initializeDataTable() {
        table = $('#member-stats-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('member_stats.data') }}',
                data: function (d) {
                    // Add filter parameters
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.min_transactions = $('#min_transactions').val() || 0;
                    d.min_amount = $('#min_amount').val() || 0;
                    d.show_all_members = $('#show_all_members').is(':checked');
                    
                    // Store current filters
                    currentFilters = {
                        start_date: d.start_date,
                        end_date: d.end_date,
                        min_transactions: d.min_transactions,
                        min_amount: d.min_amount,
                        show_all_members: d.show_all_members,
                        search: d.search.value
                    };
                },
                beforeSend: function() {
                    showTableLoading();
                },
                complete: function() {
                    hideTableLoading();
                },
                error: function(xhr, error, thrown) {
                    hideTableLoading();
                    showAlert('error', 'Gagal memuat data: ' + (xhr.responseJSON?.error || 'Terjadi kesalahan server'));
                }
            },
            columns: [
                { 
                    data: 'DT_RowIndex', 
                    name: 'DT_RowIndex', 
                    orderable: false, 
                    searchable: false,
                    className: 'text-center'
                },
                { 
                    data: 'kode_member', 
                    name: 'kode_member',
                    render: function(data, type, row) {
                        return '<span class="member-code">' + data + '</span>';
                    }
                },
                { 
                    data: 'nama', 
                    name: 'nama',
                    render: function(data, type, row) {
                        return '<span class="member-name">' + data + '</span>';
                    }
                },
                { 
                    data: 'telepon', 
                    name: 'telepon', 
                    orderable: false,
                    className: 'text-center' 
                },
                { 
                    data: 'total_transaksi', 
                    name: 'total_transaksi',
                    className: 'text-center'
                },
                { 
                    data: 'total_belanja', 
                    name: 'total_belanja',
                    className: 'text-right',
                    render: function(data, type, row) {
                        return '<span class="amount">' + data + '</span>';
                    }
                },
                { 
                    data: 'avg_order_value', 
                    name: 'avg_order_value',
                    className: 'text-right',
                    render: function(data, type, row) {
                        return '<span class="amount">' + data + '</span>';
                    }
                },
                { 
                    data: 'total_item', 
                    name: 'total_item',
                    className: 'text-center'
                },
                { 
                    data: 'last_transaction_date', 
                    name: 'last_transaction_date',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return '<span class="date-text">' + data + '</span>';
                    }
                },
                { 
                    data: null,
                    orderable: false, 
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return '<button class="btn btn-info btn-xs btn-detail" onclick="showMemberDetail(' + row.id_member + ', \'' + row.kode_member + '\', \'' + row.nama + '\', \'' + (row.telepon || '') + '\')">' +
                               '<i class="fa fa-eye"></i> Detail</button>';
                    }
                }
            ],
            order: [[5, 'desc']], // Sort by total_belanja desc
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            responsive: true,
            dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-5"i><"col-sm-7"p>>',
            language: {
                processing: '<i class="fa fa-spinner fa-spin"></i> Memuat data...',
                lengthMenu: 'Tampilkan _MENU_ data per halaman',
                zeroRecords: 'Tidak ada data yang ditemukan sesuai filter',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                infoFiltered: '(disaring dari _MAX_ total data)',
                search: 'Cari Member:',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Selanjutnya',
                    previous: 'Sebelumnya'
                },
                emptyTable: 'Tidak ada data member yang tersedia'
            },
            drawCallback: function(settings) {
                // Update period info
                updatePeriodInfo();
                
                // Store member data for detail modal
                let apiData = settings.json;
                if (apiData && apiData.data) {
                    apiData.data.forEach(function(item) {
                        memberData[item.id_member] = item;
                    });
                }
            }
        });
    }
    
    function initializeDetailTable() {
        detailTable = $('#detail-table').DataTable({
            paging: true,
            searching: false,
            info: true,
            pageLength: 10,
            order: [[1, 'desc']], // Sort by date desc
            language: {
                emptyTable: 'Tidak ada transaksi dalam periode ini',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ transaksi',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir', 
                    next: 'Selanjutnya',
                    previous: 'Sebelumnya'
                }
            },
            columnDefs: [
                { className: 'text-center', targets: [0, 3, 7] },
                { className: 'text-right', targets: [4, 5, 6] }
            ]
        });
    }
    
    function bindEvents() {
        // Filter button
        $('#btn-filter').on('click', function() {
            applyFilter();
        });

        // Reset button
        $('#btn-reset').on('click', function() {
            resetFilter();
        });
        
        // 🔄 Enhanced Refresh button with sync capability
        $('#btn-refresh').on('click', function() {
            performSync();
        });

        // Quick filter buttons
        $('.quick-filter-btn').on('click', function() {
            let days = $(this).data('period');
            let endDate = new Date();
            let startDate = new Date();
            startDate.setDate(endDate.getDate() - days);
            
            $('#start_date').val(formatDate(startDate));
            $('#end_date').val(formatDate(endDate));
            
            // Remove active class from all buttons
            $('.quick-filter-btn').removeClass('btn-primary').addClass('btn-default');
            // Add active class to clicked button
            $(this).removeClass('btn-default').addClass('btn-primary');
            
            applyFilter();
        });

        // Export CSV
        $('#btn-export-csv').on('click', function() {
            if (Object.keys(currentFilters).length === 0) {
                showAlert('warning', 'Silakan terapkan filter terlebih dahulu');
                return;
            }
            
            showAlert('info', 'Sedang memproses export CSV...');
            let params = new URLSearchParams(currentFilters);
            window.open('{{ route('member_stats.export_csv') }}?' + params.toString());
        });

        // Export PDF
        $('#btn-export-pdf').on('click', function() {
            if (Object.keys(currentFilters).length === 0) {
                showAlert('warning', 'Silakan terapkan filter terlebih dahulu');
                return;
            }
            
            showAlert('info', 'Sedang memproses export PDF...');
            let params = new URLSearchParams(currentFilters);
            window.open('{{ route('member_stats.export_pdf') }}?' + params.toString());
        });

        // Help button
        $('#btn-help').on('click', function() {
            $('#modal-help').modal('show');
        });

        // Auto-filter on enter key
        $('#filter-form input').on('keypress', function(e) {
            if (e.which === 13) {
                applyFilter();
            }
        });

        // Date validation
        $('#start_date, #end_date').on('change', function() {
            validateDateRange();
        });
        
        // Print detail button
        $('#btn-print-detail').on('click', function() {
            printDetailTransactions();
        });
        
        // 🔄 Keyboard shortcut for sync (Ctrl+R or F5)
        $(document).keydown(function(e) {
            // Ctrl+R or F5 untuk sync
            if ((e.ctrlKey && e.keyCode === 82) || e.keyCode === 116) {
                e.preventDefault();
                performSync();
                return false;
            }
        });
    }

    // 🔄 ENHANCED SYNC FUNCTIONS
    function performSync() {
        if (syncInProgress) {
            showAlert('warning', 'Sinkronisasi sedang berlangsung, mohon tunggu...');
            return;
        }

        syncInProgress = true;
        showSyncProgress();
        
        // Update status steps
        setTimeout(() => updateSyncStatus('Memvalidasi parameter...'), 100);
        setTimeout(() => updateSyncStatus('Mengambil data dari database...'), 500);
        setTimeout(() => updateSyncStatus('Memproses agregasi...'), 1000);

        $.ajax({
            url: '{{ route('member_stats.sync') }}',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                min_transactions: $('#min_transactions').val() || 0,
                min_amount: $('#min_amount').val() || 0,
                show_all_members: $('#show_all_members').is(':checked')
            },
            success: function(response) {
                updateSyncStatus('Menyelesaikan sinkronisasi...');
                
                setTimeout(() => {
                    if (response.success) {
                        // Success handling
                        showAlert('success', 
                            `✅ Sinkronisasi berhasil! ` +
                            `${response.stats.total_records} record diproses dalam ${response.stats.process_time_ms}ms`
                        );
                        
                        // Update UI
                        if (response.summary) {
                            updateSummaryCards(response.summary);
                        }
                        
                        updateLastSyncDisplay(response.stats);
                        table.ajax.reload();
                        
                    } else {
                        showAlert('error', 'Sinkronisasi gagal: ' + (response.error || 'Terjadi kesalahan'));
                        showSyncError();
                    }
                }, 500);
            },
            error: function(xhr, status, error) {
                updateSyncStatus('Sync gagal...');
                
                setTimeout(() => {
                    let errorMsg = 'Sinkronisasi gagal: ';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg += xhr.responseJSON.error;
                    } else {
                        errorMsg += 'Terjadi kesalahan server';
                    }
                    showAlert('error', errorMsg);
                    showSyncError();
                }, 500);
            },
            complete: function() {
                setTimeout(() => {
                    syncInProgress = false;
                    hideSyncProgress();
                }, 1000);
            }
        });
    }

    // 🔄 SYNC UI FUNCTIONS
    function showSyncProgress() {
        $('#sync-status').fadeIn();
        $('#sync-status-text').text('Memulai sinkronisasi data...');
        
        // Add syncing class to summary cards
        $('.summary-card').addClass('syncing');
        
        // Update button state
        $('#btn-refresh').addClass('syncing')
                         .prop('disabled', true)
                         .html('<i class="fa fa-spinner fa-spin"></i> Syncing...')
                         .attr('title', 'Sinkronisasi sedang berlangsung...');
    }

    function hideSyncProgress() {
        $('#sync-status').fadeOut();
        
        // Remove syncing class
        $('.summary-card').removeClass('syncing');
        
        // Reset button state
        $('#btn-refresh').removeClass('syncing')
                         .prop('disabled', false)
                         .html('<i class="fa fa-refresh"></i> Sync Data')
                         .attr('title', 'Sinkronisasi Data (Ctrl+R)');
    }

    function updateSyncStatus(message) {
        $('#sync-status-text').text(message);
    }

    function updateLastSyncDisplay(stats) {
        let syncText = `Terakhir sync: ${stats.synced_at}`;
        if (stats.total_records) {
            syncText += ` (${stats.total_records} records)`;
        }
        
        $('#last-sync-info').text(syncText);
        $('#data-status .fa-circle').removeClass('text-warning text-danger')
                                    .addClass('text-success');
    }

    function showSyncError() {
        $('#data-status .fa-circle').removeClass('text-success text-warning')
                                    .addClass('text-danger');
        $('#last-sync-info').text('Sync gagal - coba lagi');
    }

    // 🔄 REAL-TIME SUMMARY FUNCTIONS
    function loadRealTimeSummary() {
        if (Object.keys(currentFilters).length === 0) return;
        
        $.ajax({
            url: '{{ route('member_stats.get_summary') }}',
            data: currentFilters,
            success: function(response) {
                if (response.success) {
                    updateSummaryCards(response.summary);
                }
            },
            error: function() {
                // Silent fail untuk auto-refresh
            }
        });
    }

    function updateSummaryCards(summary) {
        $('#total-members').text(number_format(summary.total_members || 0));
        $('#total-transactions').text(number_format(summary.total_transactions || 0));
        $('#grand-total').text('Rp ' + (summary.grand_total || '0'));
        $('#total-items').text(number_format(summary.total_items || 0));
        
        // Show summary cards dengan animasi
        $('#summary-cards').fadeIn();
    }

    // AUTO-REFRESH FUNCTIONS
    let autoRefreshInterval;
    function startAutoRefresh() {
        autoRefreshInterval = setInterval(() => {
            if (!syncInProgress && Object.keys(currentFilters).length > 0) {
                loadRealTimeSummary();
                
                // Show subtle indicator
                $('#data-status .fa-circle').removeClass('text-success')
                                           .addClass('text-warning');
                setTimeout(() => {
                    $('#data-status .fa-circle').removeClass('text-warning')
                                               .addClass('text-success');
                }, 1000);
            }
        }, 30000); // Every 30 seconds
    }

    function stopAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
    }

    // Stop auto-refresh when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoRefresh();
        } else {
            startAutoRefresh();
        }
    });

    // EXISTING FUNCTIONS (Enhanced)
    function applyFilter() {
        if (!validateDateRange()) {
            return;
        }
        
        // Remove active class from quick filter buttons
        $('.quick-filter-btn').removeClass('btn-primary').addClass('btn-default');
        
        showTableLoading();
        table.ajax.reload(function() {
            hideTableLoading();
            loadRealTimeSummary();
        });
    }
    
    function resetFilter() {
        $('#filter-form')[0].reset();
        $('#start_date').val('{{ \Carbon\Carbon::now()->subDays(30)->format('Y-m-d') }}');
        $('#end_date').val('{{ \Carbon\Carbon::now()->format('Y-m-d') }}');
        $('#show_all_members').prop('checked', false);
        
        // Reset quick filter buttons
        $('.quick-filter-btn').removeClass('btn-primary').addClass('btn-default');
        
        currentFilters = {};
        $('#summary-cards').hide();
        $('#period-info').hide();
        
        table.ajax.reload();
        showAlert('success', 'Filter berhasil direset');
    }

    function validateDateRange() {
        let startDate = new Date($('#start_date').val());
        let endDate = new Date($('#end_date').val());
        
        if (startDate > endDate) {
            showAlert('error', 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
            return false;
        }
        
        // Check if date range is more than 1 year
        let diffTime = Math.abs(endDate - startDate);
        let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays > 365) {
            showAlert('error', 'Periode maksimal 1 tahun (365 hari)');
            return false;
        }
        
        return true;
    }

    function showMemberDetail(memberId, memberCode, memberName, memberPhone) {
        $('#modal-detail').modal('show');
        
        // Update member info in modal
        $('#member-name').text(memberName);
        $('#member-code').text('Kode: ' + memberCode);
        $('#member-phone').text('Telepon: ' + (memberPhone || 'Tidak ada'));
        $('#filter-period').text('Periode: ' + formatDateDisplay($('#start_date').val()) + ' - ' + formatDateDisplay($('#end_date').val()));
        
        // Clear previous data
        detailTable.clear().draw();
        
        // Reset detail summary
        $('#detail-total-transactions').text('0');
        $('#detail-total-amount').text('Rp 0');
        $('#detail-avg-amount').text('Rp 0');
        $('#detail-total-items').text('0');
        
        // Load member transaction details
        $.ajax({
            url: '{{ route('member_stats.detail', ':id') }}'.replace(':id', memberId),
            data: {
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val()
            },
            beforeSend: function() {
                $('#modal-detail .modal-body').append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
            },
            success: function(response) {
                $('.overlay').remove();
                
                if (response.success && response.data.length > 0) {
                    detailTable.clear();
                    
                    let totalTransactions = response.data.length;
                    let totalAmount = 0;
                    let totalItems = 0;
                    
                    let no = 1;
                    response.data.forEach(function(item) {
                        // Calculate totals
                        let amountValue = parseFloat(item.total_harga.replace(/[^\d]/g, '')) || 0;
                        let itemValue = parseInt(item.total_item.replace(/[^\d]/g, '')) || 0;
                        
                        totalAmount += amountValue;
                        totalItems += itemValue;
                        
                        detailTable.row.add([
                            no++,
                            item.tanggal,
                            item.id_penjualan,
                            item.total_item,
                            item.total_harga,
                            item.diskon,
                            item.bayar,
                            item.kasir
                        ]);
                    });
                    
                    detailTable.draw();
                    
                    // Update detail summary
                    $('#detail-total-transactions').text(number_format(totalTransactions));
                    $('#detail-total-amount').text('Rp ' + number_format(totalAmount));
                    $('#detail-avg-amount').text('Rp ' + number_format(totalAmount / totalTransactions));
                    $('#detail-total-items').text(number_format(totalItems));
                    
                } else {
                    showAlert('info', 'Tidak ada transaksi untuk member ini dalam periode yang dipilih');
                    detailTable.clear().draw();
                }
            },
            error: function(xhr) {
                $('.overlay').remove();
                showAlert('error', 'Gagal memuat detail transaksi: ' + (xhr.responseJSON?.error || 'Terjadi kesalahan'));
            }
        });
    }
    
    function updatePeriodInfo() {
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();
        
        if (startDate && endDate) {
            let start = new Date(startDate);
            let end = new Date(endDate);
            let diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
            
            let periodText = 'Menampilkan data periode ' + formatDateDisplay(startDate) + ' sampai ' + formatDateDisplay(endDate) + ' (' + diffDays + ' hari)';
            
            $('#period-text').text(periodText);
            $('#period-info').fadeIn();
        }
    }
    
    function showTableLoading() {
        $('#table-loading').show();
    }
    
    function hideTableLoading() {
        $('#table-loading').hide();
    }
    
    function showAlert(type, message) {
        let alertClass = 'alert-info';
        let icon = 'fa-info-circle';
        
        switch(type) {
            case 'success':
                alertClass = 'alert-success';
                icon = 'fa-check-circle';
                break;
            case 'error':
                alertClass = 'alert-danger';
                icon = 'fa-exclamation-circle';
                break;
            case 'warning':
                alertClass = 'alert-warning';
                icon = 'fa-exclamation-triangle';
                break;
        }
        
        let alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible alert-custom">' +
                       '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                       '<i class="fa ' + icon + '"></i> ' + message +
                       '</div>';
        
        $('#alert-container').html(alertHtml);
        
        // Auto hide after 5 seconds for success/info
        if (type === 'success' || type === 'info') {
            setTimeout(function() {
                $('.alert-custom').fadeOut();
            }, 5000);
        }
    }
    
    function printDetailTransactions() {
        showAlert('info', 'Fitur cetak detail akan segera tersedia');
    }
    
    function formatDate(date) {
        let year = date.getFullYear();
        let month = String(date.getMonth() + 1).padStart(2, '0');
        let day = String(date.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }
    
    function formatDateDisplay(dateString) {
        let date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    function number_format(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    // 🔧 System status check (untuk debugging)
    function checkSystemStatus() {
        $.ajax({
            url: '{{ route('member_stats.system_status') }}',
            success: function(response) {
                console.log('System Status:', response);
                
                let statusMessage = `Database: ${response.database_connection} | ` +
                                   `Members: ${response.member_table_count} | ` +
                                   `Penjualan: ${response.penjualan_table_count}`;
                
                if (response.database_connection === 'ERROR') {
                    showAlert('error', 'Database error: ' + response.error_message);
                } else {
                    showAlert('success', 'System OK - ' + statusMessage);
                }
            },
            error: function() {
                showAlert('error', 'Tidak dapat mengakses system status');
            }
        });
    }

    // Global functions for onclick events
    window.showMemberDetail = showMemberDetail;
    window.checkSystemStatus = checkSystemStatus;

    // Initialize tooltips for better UX
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush>

                {{-- 🔄 ENHANCED EXPORT SECTION DENGAN SYNC INFO --}}
                <div class="export-section">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 style="margin-top: 0;">
                                <i class="fa fa-download"></i> Export Data
                            </h5>
                            <button type="button" id="btn-export-csv" class="btn btn-success btn-export" 
                                    data-toggle="tooltip" title="Export ke format CSV/Excel">
                                <i class="fa fa-file-excel-o"></i> Export ke CSV
                            </button>
                            <button type="button" id="btn-export-pdf" class="btn btn-danger btn-export"
                                    data-toggle="tooltip" title="Export ke format PDF">
                                <i class="fa fa-file-pdf-o"></i> Export ke PDF
                            </button>
                            <small class="text-muted">
                                <i class="fa fa-info-circle"></i> 
                                Export akan mengunduh data sesuai dengan filter yang sedang aktif
                            </small>
                        </div>
                        <div class="col-md-4 text-right">
                            {{-- Data Status Info --}}
                            <div class="data-status-info">
                                <h6 style="margin-bottom: 5px;">Status Data</h6>
                                <div id="data-status" class="text-muted">
                                    <i class="fa fa-circle text-success"></i> Real-time
                                    <br>
                                    <small id="last-sync-info">Belum ada sinkronisasi</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table Container -->
                <div class="table-container">
                    <div class="loading-overlay" id="table-loading">
                        <div class="loading-spinner">
                            <i class="fa fa-spinner fa-spin"></i> Memuat data...
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="member-stats-table">
                            <thead>
                                <tr>
                                    <th width="4%">No</th>
                                    <th width="12%">Kode Member</th>
                                    <th width="18%">Nama Member</th>
                                    <th width="12%">Telepon</th>
                                    <th width="10%">Total Transaksi</th>
                                    <th width="15%">Total Belanja</th>
                                    <th width="13%">Rata-rata Order (AOV)</th>
                                    <th width="8%">Total Item</th>
                                    <th width="12%">Transaksi Terakhir</th>
                                    <th width="8%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan dimuat via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="modal-detail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    <i class="fa fa-list-alt"></i> Detail Transaksi Member
                </h4>
            </div>
            <div class="modal-body">
                <!-- Member Info Header -->
                <div class="member-detail-header" id="member-info">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 id="member-name" style="margin: 0; color: #333;"></h5>
                            <p id="member-code" style="margin: 5px 0 0 0; color: #666;"></p>
                        </div>
                        <div class="col-md-6 text-right">
                            <p id="member-phone" style="margin: 0; color: #666;"></p>
                            <p id="filter-period" style="margin: 5px 0 0 0; color: #666; font-size: 12px;"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Detail Summary -->
                <div class="detail-summary" id="detail-summary">
                    <div class="detail-summary-item">
                        <h4 class="value" id="detail-total-transactions">0</h4>
                        <p class="label">Total Transaksi</p>
                    </div>
                    <div class="detail-summary-item">
                        <h4 class="value" id="detail-total-amount">Rp 0</h4>
                        <p class="label">Total Belanja</p>
                    </div>
                    <div class="detail-summary-item">
                        <h4 class="value" id="detail-avg-amount">Rp 0</h4>
                        <p class="label">Rata-rata Order</p>
                    </div>
                    <div class="detail-summary-item">
                        <h4 class="value" id="detail-total-items">0</h4>
                        <p class="label">Total Item</p>
                    </div>
                </div>
                
                <hr>
                
                <!-- Transaction Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="detail-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Tanggal</th>
                                <th width="12%">No. Transaksi</th>
                                <th width="10%">Total Item</th>
                                <th width="15%">Total Belanja</th>
                                <th width="12%">Diskon</th>
                                <th width="15%">Total Bayar</th>
                                <th width="16%">Kasir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data detail akan dimuat via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Tutup
                </button>
                <button type="button" class="btn btn-primary" id="btn-print-detail">
                    <i class="fa fa-print"></i> Cetak Detail
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Help -->
<div class="modal fade" id="modal-help" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    <i class="fa fa-question-circle"></i> Panduan Penggunaan
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5><i class="fa fa-filter"></i> Filter Data</h5>
                        <ul>
                            <li><strong>Periode:</strong> Pilih rentang tanggal untuk analisis (maksimal 1 tahun)</li>
                            <li><strong>Min. Transaksi:</strong> Tampilkan member dengan minimal N transaksi</li>
                            <li><strong>Min. Belanja:</strong> Tampilkan member dengan minimal total belanja tertentu</li>
                            <li><strong>Tampilkan Semua Member:</strong> Termasuk member tanpa transaksi dalam periode</li>
                        </ul>
                        
                        <h5><i class="fa fa-table"></i> Kolom Data</h5>
                        <ul>
                            <li><strong>Total Transaksi:</strong> Jumlah transaksi PAID dalam periode</li>
                            <li><strong>Total Belanja:</strong> Total nilai pembelian</li>
                            <li><strong>AOV:</strong> Average Order Value (rata-rata nilai per transaksi)</li>
                            <li><strong>Total Item:</strong> Total kuantitas barang dibeli</li>
                        </ul>
                        
                        <h5><i class="fa fa-download"></i> Export</h5>
                        <ul>
                            <li><strong>CSV:</strong> Format spreadsheet untuk analisis lanjutan</li>
                            <li><strong>PDF:</strong> Format laporan siap cetak</li>
                        </ul>
                        
                        <h5><i class="fa fa-refresh"></i> Sync Data</h5>
                        <ul>
                            <li><strong>Sync Button:</strong> Klik untuk memperbarui data secara manual</li>
                            <li><strong>Keyboard:</strong> Tekan Ctrl+R atau F5 untuk sync cepat</li>
                            <li><strong>Auto-refresh:</strong> Summary ter-update otomatis setiap 30 detik</li>
                        </ul>
                    </div>
                </div>
            </div>  
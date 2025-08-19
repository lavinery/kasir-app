@extends('layouts.master')

@section('title')
    Daftar Barang Habis
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Barang Habis</li>
@endsection

@push('css')
<style>
    .group-header {
        background-color: #f4f4f4 !important;
        font-weight: bold;
        color: #333;
    }
    
    .select2-container--bootstrap .select2-selection {
        border: 1px solid #d2d6de;
        border-radius: 0;
    }
    
    .mb-3 {
        margin-bottom: 15px;
    }
    
    .btn-group .btn {
        margin-right: 5px;
    }
    
    .alert {
        margin-top: 10px;
    }
    
    .badge {
        font-size: 11px;
    }
    
    .table th {
        background-color: #f4f4f4;
        font-weight: bold;
    }
    
    .filter-section {
        background-color: #f9f9f9;
        padding: 15px;
        border: 1px solid #d2d6de;
        border-radius: 3px;
        margin-bottom: 20px;
    }

    .bulk-actions {
        background-color: #fff3cd;
        border: 1px solid #ffeaa7;
        padding: 10px 15px;
        margin-bottom: 15px;
        border-radius: 4px;
        display: none;
    }

    .bulk-actions.show {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="btn-group">
                    <button onclick="addManual()" class="btn btn-success btn-sm">
                        <i class="fa fa-plus-circle"></i> Tambah Manual
                    </button>
                    <button onclick="exportPdf()" class="btn btn-info btn-sm">
                        <i class="fa fa-download"></i> Cetak PDF
                    </button>
                    
                    <!-- Enhanced Refresh Button with Dropdown -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-warning btn-sm" onclick="refreshTable()">
                            <i class="fa fa-refresh"></i> Refresh
                        </button>
                        <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="javascript:void(0)" onclick="refreshTable()">
                                    <i class="fa fa-refresh"></i> Refresh Biasa
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="syncAndRefresh()">
                                    <i class="fa fa-sync"></i> Refresh + Sinkronisasi
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="javascript:void(0)" onclick="showSyncStats()">
                                    <i class="fa fa-info-circle"></i> Status Sinkronisasi
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Sync Status Badge -->
                <div class="pull-right">
                    <span id="sync-status" class="label label-default">
                        <i class="fa fa-clock-o"></i> Belum pernah sync
                    </span>
                </div>
            </div>
            <div class="box-body">
                <!-- Bulk Actions Bar -->
                <div class="bulk-actions" id="bulk-actions">
                    <div class="row">
                        <div class="col-md-8">
                            <span id="selected-count">0</span> item dipilih
                        </div>
                        <div class="col-md-4 text-right">
                            <button type="button" class="btn btn-danger btn-sm" onclick="bulkDelete()">
                                <i class="fa fa-trash"></i> Hapus Terpilih
                            </button>
                            <button type="button" class="btn btn-info btn-sm" onclick="bulkExportPdf()">
                                <i class="fa fa-download"></i> Export Terpilih
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="filter-section">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Filter Kategori:</label>
                            <select name="filter_kategori" id="filter_kategori" class="form-control">
                                <option value="">Semua Kategori</option>
                                @foreach($kategori as $kat)
                                    <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Filter Tipe:</label>
                            <select name="filter_tipe" id="filter_tipe" class="form-control">
                                <option value="">Semua Tipe</option>
                                <option value="auto">Auto</option>
                                <option value="manual">Manual</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Pencarian:</label>
                            <input type="text" name="filter_search" id="filter_search" class="form-control" placeholder="Cari nama produk atau merk...">
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label><br>
                            <button type="button" class="btn btn-primary btn-block" onclick="applyFilters()">
                                <i class="fa fa-search"></i> Filter
                            </button>
                            <button type="button" class="btn btn-default btn-block" onclick="clearFilters()" style="margin-top: 5px;">
                                <i class="fa fa-eraser"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <table class="table table-striped table-bordered table-hover" id="table-barang-habis">
                    <thead>
                        <tr>
                            <th width="3%">
                                <input type="checkbox" id="select-all">
                            </th>
                            <th width="5%">No</th>
                            <th>Kategori</th>
                            <th>Nama Produk</th>
                            <th>Merk</th>
                            <th width="8%">Stok</th>
                            <th>Keterangan</th>
                            <th width="8%">Sumber</th>
                            <th width="12%">Tanggal</th>
                            <th width="10%"><i class="fa fa-cog"></i></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Manual -->
<div class="modal fade" id="modal-manual" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="form-manual">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">
                        <i class="fa fa-plus-circle"></i> Tambah Barang ke Daftar Habis
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="id_produk">Pilih Produk <span class="text-red">*</span></label>
                        <select name="id_produk" id="id_produk" class="form-control select2-produk" required style="width: 100%;">
                            <option value="">Ketik untuk mencari produk...</option>
                        </select>
                        <small class="text-muted">
                            <i class="fa fa-info-circle"></i> 
                            Hanya produk yang belum ada di daftar yang bisa dipilih
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3" 
                                  placeholder="Contoh: Banyak permintaan, menunggu restock, produk populer, dll..."></textarea>
                        <small class="text-muted">Opsional - berikan alasan mengapa produk ini ditambahkan manual</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Keterangan -->
<div class="modal fade" id="modal-edit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="form-edit">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">
                        <i class="fa fa-edit"></i> Edit Keterangan
                    </h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="form-group">
                        <label for="edit_keterangan">Keterangan <span class="text-red">*</span></label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let table;
let selectedItems = [];

$(function () {
    // Initialize DataTable
    table = $('#table-barang-habis').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        responsive: true,
        ajax: {
            url: '{{ route("barang_habis.data") }}',
            data: function (d) {
                d.kategori = $('#filter_kategori').val();
                d.tipe = $('#filter_tipe').val();
                d.search = $('#filter_search').val();
            }
        },
        columns: [
            {
                data: 'id',
                name: 'checkbox',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<input type="checkbox" class="item-checkbox" value="' + data + '">';
                }
            },
            {data: 'DT_RowIndex', searchable: false, sortable: false},
            {data: 'kategori'},
            {data: 'nama_produk'},
            {data: 'merk'},
            {data: 'stok', className: 'text-center'},
            {data: 'keterangan'},
            {data: 'tipe', className: 'text-center'},
            {data: 'created_at', className: 'text-center'},
            {data: 'action', searchable: false, sortable: false, className: 'text-center'},
        ],
        order: [[8, 'desc']], // Sort by created_at
        pageLength: 25,
        language: {
            processing: "Memuat...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            loadingRecords: "Memuat...",
            zeroRecords: "Tidak ada data yang ditemukan",
            emptyTable: "Tidak ada data dalam tabel",
            paginate: {
                first: "Pertama",
                previous: "Sebelumnya",
                next: "Selanjutnya",
                last: "Terakhir"
            }
        },
        rowGroup: {
            dataSrc: 'kategori',
            startRender: function (rows, group) {
                return $('<tr/>')
                    .append('<td colspan="10" class="group-header" style="background-color: #f4f4f4; font-weight: bold; padding: 10px;">' + 
                            '<i class="fa fa-folder-open text-primary"></i> ' + group + ' <span class="badge badge-info">' + rows.count() + ' item</span></td>');
            }
        }
    });

    // Checkbox functionality
    $('#select-all').on('click', function() {
        let isChecked = $(this).is(':checked');
        $('.item-checkbox:visible').prop('checked', isChecked);
        updateSelectedItems();
    });

    $(document).on('change', '.item-checkbox', function() {
        updateSelectedItems();
        
        // Update select all checkbox
        let totalCheckboxes = $('.item-checkbox:visible').length;
        let checkedCheckboxes = $('.item-checkbox:visible:checked').length;
        
        $('#select-all').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#select-all').prop('checked', checkedCheckboxes === totalCheckboxes && totalCheckboxes > 0);
    });

    // Initialize Select2 for product selection
    $('#id_produk').select2({
        placeholder: 'Ketik untuk mencari produk...',
        allowClear: true,
        theme: 'bootstrap',
        width: '100%',
        ajax: {
            url: '{{ route("barang_habis.products") }}',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: data.results.length === 50
                    }
                };
            },
            cache: true
        },
        minimumInputLength: 2,
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    // Form submission handlers
    $('#form-manual').on('submit', function(e) {
        e.preventDefault();
        let submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
        
        let formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("barang_habis.store_manual") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    $('#modal-manual').modal('hide');
                    $('#form-manual')[0].reset();
                    $('#id_produk').val(null).trigger('change');
                    table.ajax.reload();
                    showAlert('success', response.message);
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                if(errors) {
                    let errorMessage = Object.values(errors).join('<br>');
                    showAlert('error', errorMessage);
                } else {
                    showAlert('error', xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan');
            }
        });
    });

    $('#form-edit').on('submit', function(e) {
        e.preventDefault();
        let submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
        
        let id = $('#edit_id').val();
        let formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("barang_habis.update", ":id") }}'.replace(':id', id),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    $('#modal-edit').modal('hide');
                    table.ajax.reload();
                    showAlert('success', response.message);
                }
            },
            error: function(xhr) {
                showAlert('error', xhr.responseJSON?.message || 'Terjadi kesalahan');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Update');
            }
        });
    });

    // Enter key untuk filter search
    $('#filter_search').on('keypress', function(e) {
        if(e.which === 13) {
            applyFilters();
        }
    });

    // Reset checkboxes when table reloads
    table.on('draw', function() {
        $('#select-all').prop('checked', false).prop('indeterminate', false);
        selectedItems = [];
        updateBulkActions();
    });
});

function updateSelectedItems() {
    selectedItems = [];
    $('.item-checkbox:checked').each(function() {
        selectedItems.push($(this).val());
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    $('#selected-count').text(selectedItems.length);
    
    if(selectedItems.length > 0) {
        $('#bulk-actions').addClass('show');
    } else {
        $('#bulk-actions').removeClass('show');
    }
}

function addManual() {
    $('#modal-manual').modal('show');
    $('#form-manual')[0].reset();
    $('#id_produk').val(null).trigger('change');
}

function editItem(id, keterangan) {
    $('#edit_id').val(id);
    $('#edit_keterangan').val(keterangan);
    $('#modal-edit').modal('show');
}

function deleteItem(id) {
    if(confirm('Yakin ingin menghapus item ini dari daftar barang habis?')) {
        $.ajax({
            url: '{{ route("barang_habis.destroy", ":id") }}'.replace(':id', id),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success) {
                    table.ajax.reload();
                    showAlert('success', response.message);
                }
            },
            error: function(xhr) {
                showAlert('error', xhr.responseJSON?.message || 'Terjadi kesalahan');
            }
        });
    }
}

function bulkDelete() {
    if(selectedItems.length === 0) {
        showAlert('error', 'Tidak ada item yang dipilih');
        return;
    }
    
    if(confirm(`Yakin ingin menghapus ${selectedItems.length} item terpilih dari daftar barang habis?`)) {
        $.ajax({
            url: '{{ route("barang_habis.bulk_destroy") }}',
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                ids: selectedItems
            },
            success: function(response) {
                if(response.success) {
                    table.ajax.reload();
                    showAlert('success', response.message);
                    selectedItems = [];
                    updateBulkActions();
                }
            },
            error: function(xhr) {
                showAlert('error', xhr.responseJSON?.message || 'Terjadi kesalahan');
            }
        });
    }
}

function bulkExportPdf() {
    if(selectedItems.length === 0) {
        showAlert('error', 'Tidak ada item yang dipilih');
        return;
    }
    
    let params = new URLSearchParams();
    params.append('ids', selectedItems.join(','));
    
    let url = '{{ route("barang_habis.export_pdf_by_ids") }}?' + params.toString();
    window.open(url, '_blank');
}

function applyFilters() {
    table.ajax.reload();
}

function clearFilters() {
    $('#filter_kategori').val('');
    $('#filter_tipe').val('');
    $('#filter_search').val('');
    table.ajax.reload();
}

function refreshTable() {
    table.ajax.reload(null, false); // false = keep current page
    showAlert('info', 'Data berhasil di-refresh');
}

function exportPdf() {
    let params = new URLSearchParams();
    
    let kategori = $('#filter_kategori').val();
    let tipe = $('#filter_tipe').val();
    let search = $('#filter_search').val();
    
    if(kategori) params.append('kategori', kategori);
    if(tipe) params.append('tipe', tipe);
    if(search) params.append('search', search);
    
    let url = '{{ route("barang_habis.export_pdf") }}';
    if(params.toString()) {
        url += '?' + params.toString();
    }
    
    window.open(url, '_blank');
}

function showAlert(type, message) {
    // Remove existing alerts
    $('.alert').remove();
    
    let alertClass = type === 'success' ? 'alert-success' : 
                    type === 'info' ? 'alert-info' : 'alert-danger';
    let alertIcon = type === 'success' ? 'fa-check-circle' : 
                   type === 'info' ? 'fa-info-circle' : 'fa-exclamation-triangle';
    
    let alertHtml = `
        <div class="alert ${alertClass} alert-dismissible" style="margin-bottom: 15px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa ${alertIcon}"></i> ${message}
        </div>
    `;
    
    $('.box-body').prepend(alertHtml);
    
    // Auto hide after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);

    // Tambahkan functions ini di bagian script

function refreshTable() {
    table.ajax.reload(null, false); // false = keep current page
    updateSyncStatus('refresh', 'Tabel di-refresh');
    showAlert('info', 'Data berhasil di-refresh');
}

function syncAndRefresh() {
    // Konfirmasi user
    if (!confirm('Sinkronisasi akan:\n• Menambah produk dengan stok ≤ 5 ke daftar\n• Menghapus produk auto dengan stok > 5 dari daftar\n\nLanjutkan?')) {
        return;
    }

    // Show loading
    $('#sync-status').removeClass().addClass('label label-warning')
        .html('<i class="fa fa-spinner fa-spin"></i> Sinkronisasi...');

    $.ajax({
        url: '{{ route("barang_habis.sync") }}',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                // Update status badge
                updateSyncStatus('success', `Terakhir sync: ${getCurrentTime()}`);
                
                // Reload table
                table.ajax.reload();
                
                // Show detailed message
                showAlert('success', response.message);
                
                // Show stats in console for debugging
                console.log('Sync Stats:', response.stats);
            } else {
                updateSyncStatus('error', 'Sync gagal');
                showAlert('error', response.message);
            }
        },
        error: function(xhr) {
            updateSyncStatus('error', 'Sync error');
            showAlert('error', xhr.responseJSON?.message || 'Terjadi kesalahan saat sinkronisasi');
        }
    });
}

function showSyncStats() {
    $.ajax({
        url: '{{ route("barang_habis.sync_stats") }}',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                let stats = response.stats;
                let message = `
                    <strong>Status Sinkronisasi:</strong><br>
                    • Total Barang Habis: ${stats.total_barang_habis}<br>
                    • Entry Auto: ${stats.auto_entries}<br>
                    • Entry Manual: ${stats.manual_entries}<br>
                    • Produk Stok Rendah: ${stats.produk_stok_rendah}<br>
                    <br>
                    <strong>Yang Perlu Disinkronisasi:</strong><br>
                    • Perlu Ditambah: ${stats.perlu_ditambah}<br>
                    • Perlu Dihapus: ${stats.perlu_dihapus}
                `;
                
                showAlert('info', message);
            }
        },
        error: function(xhr) {
            showAlert('error', 'Error getting sync stats');
        }
    });
}

function updateSyncStatus(type, message) {
    let badgeClass = 'label-default';
    let icon = 'fa-clock-o';
    
    switch(type) {
        case 'success':
            badgeClass = 'label-success';
            icon = 'fa-check';
            break;
        case 'error':
            badgeClass = 'label-danger';
            icon = 'fa-exclamation-triangle';
            break;
        case 'refresh':
            badgeClass = 'label-info';
            icon = 'fa-refresh';
            break;
    }
    
    $('#sync-status').removeClass().addClass(`label ${badgeClass}`)
        .html(`<i class="fa ${icon}"></i> ${message}`);
}

function getCurrentTime() {
    let now = new Date();
    return now.toLocaleString('id-ID', {
        day: '2-digit',
        month: '2-digit', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Auto-check sync status on page load
$(document).ready(function() {
    // Check if sync is needed
    setTimeout(function() {
        $.get('{{ route("barang_habis.sync_stats") }}', function(response) {
            if (response.success) {
                let stats = response.stats;
                let needsSync = stats.perlu_ditambah + stats.perlu_dihapus;
                
                if (needsSync > 0) {
                    updateSyncStatus('warning', `${needsSync} item perlu sync`);
                    $('#sync-status').removeClass().addClass('label label-warning')
                        .html(`<i class="fa fa-exclamation-triangle"></i> ${needsSync} item perlu sync`);
                } else {
                    updateSyncStatus('success', 'Sudah sinkron');
                }
            }
        });
    }, 1000);
});
}
</script>
@endpush
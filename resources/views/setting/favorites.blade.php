{{-- resources/views/setting/favorites.blade.php --}}

@extends('layouts.master')

@section('title')
    Favorit Produk
@endsection

@push('css')
<style>
    .favorite-card {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 10px;
        background: #f9f9f9;
        cursor: move;
        transition: all 0.3s ease;
    }
    .favorite-card:hover {
        background: #f0f0f0;
        border-color: #bbb;
    }
    .favorite-card.inactive {
        opacity: 0.6;
        background: #f5f5f5;
    }
    .drag-handle {
        color: #999;
        margin-right: 10px;
        cursor: move;
    }
    .favorite-actions {
        display: flex;
        gap: 5px;
    }
    .favorite-info {
        flex: 1;
    }
    .counter-info {
        background: #d9edf7;
        border: 1px solid #bce8f1;
        border-radius: 4px;
        padding: 10px;
        margin-bottom: 20px;
        color: #31708f;
    }
    .box-header {
        border-bottom: 1px solid #f4f4f4;
    }
    {{-- Tambahan styling untuk Select2 agar sesuai dengan AdminLTE --}}
    .select2-container--bootstrap .select2-selection--single {
        height: 34px;
        line-height: 1.42857143;
        padding: 6px 12px;
        border: 1px solid #d2d6de;
    }
    .select2-container--bootstrap .select2-selection--single .select2-selection__arrow {
        height: 32px;
        right: 3px;
    }
</style>
@endpush

@section('breadcrumb')
    @parent
    <li><a href="{{ route('setting.index') }}">Pengaturan</a></li>
    <li class="active">Favorit Produk</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Favorit Produk</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                {{-- Alert untuk notifikasi --}}
                <div class="alert alert-success alert-dismissible" style="display: none;" id="success-alert">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="icon fa fa-check"></i> <span id="success-message"></span>
                </div>

                {{-- Info Counter --}}
                <div class="counter-info">
                    <strong><i class="fa fa-info-circle"></i> Ditampilkan di Transaksi: {{ $displayedCount }} / 10</strong>
                    <br><small>Total favorit aktif: {{ $activeCount }}</small>
                    @if($activeCount > 10)
                        <br><small class="text-warning"><i class="fa fa-warning"></i> Hanya 10 favorit pertama yang akan ditampilkan di halaman transaksi</small>
                    @endif
                </div>

                {{-- Form Tambah Favorit --}}
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-plus"></i> Tambah Produk ke Favorit</h3>
                    </div>
                    <div class="box-body">
                        <form id="add-favorite-form">
                            @csrf
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="product-search">Cari Produk</label>
                                        <select id="product-search" name="product_id" class="form-control" required>
                                            <option value="">Ketik untuk mencari produk...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fa fa-plus"></i> Tambah ke Favorit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Daftar Favorit --}}
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-list"></i> Daftar Favorit</h3>
                        <div class="box-tools pull-right">
                            <button id="save-order" class="btn btn-success btn-sm" style="display: none;">
                                <i class="fa fa-save"></i> Simpan Urutan
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        @if($favorites->count() > 0)
                            <div class="callout callout-info">
                                <h4><i class="fa fa-info"></i> Tips:</h4>
                                Seret kartu favorit untuk mengubah urutan. Urutan ini akan mempengaruhi tampilan di halaman transaksi.
                            </div>
                        @endif

                        <div id="favorites-list">
                            @forelse($favorites as $favorite)
                                <div class="favorite-card {{ !$favorite->is_active ? 'inactive' : '' }}" 
                                     data-id="{{ $favorite->id }}" 
                                     data-sort="{{ $favorite->sort_order }}">
                                    <div class="row">
                                        <div class="col-md-1">
                                            <i class="fa fa-bars drag-handle"></i>
                                        </div>
                                        <div class="col-md-7 favorite-info">
                                            <strong>{{ $favorite->product->nama_produk }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fa fa-barcode"></i> {{ $favorite->product->kode_produk }} | 
                                                <i class="fa fa-money"></i> Rp. {{ number_format($favorite->product->harga_jual, 0, ',', '.') }}
                                            </small>
                                            @if(!$favorite->is_active)
                                                <br><small class="text-danger"><i class="fa fa-eye-slash"></i> Tidak aktif</small>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <div class="favorite-actions">
                                                <button class="btn btn-sm toggle-favorite {{ $favorite->is_active ? 'btn-warning' : 'btn-success' }}" 
                                                        data-id="{{ $favorite->id }}"
                                                        data-active="{{ $favorite->is_active ? 1 : 0 }}"
                                                        title="{{ $favorite->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i class="fa fa-{{ $favorite->is_active ? 'eye-slash' : 'eye' }}"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger delete-favorite" 
                                                        data-id="{{ $favorite->id }}"
                                                        title="Hapus dari favorit">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="callout callout-info">
                                    <h4><i class="fa fa-star-o"></i> Belum ada produk favorit</h4>
                                    <p>Tambahkan produk menggunakan form pencarian di atas untuk membuat shortcut di halaman transaksi.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    // Initialize Select2 untuk pencarian produk
    $('#product-search').select2({
        placeholder: 'Ketik untuk mencari produk...',
        allowClear: true,
        theme: 'bootstrap',
        width: '100%',
        ajax: {
            url: '{{ route("api.products.search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page
                };
            },
            processResults: function (data, params) {
                return {
                    results: data.results
                };
            },
            cache: true
        },
        minimumInputLength: 2,
        templateResult: formatProduct,
        templateSelection: formatProductSelection
    });

    function formatProduct(product) {
        if (product.loading) return product.text;
        return $('<span>' + product.text + '</span>');
    }

    function formatProductSelection(product) {
        return product.text || product.nama_produk;
    }

    // Initialize SortableJS - pastikan element ada sebelum inisialisasi
    if (document.getElementById('favorites-list')) {
        const sortable = Sortable.create(document.getElementById('favorites-list'), {
            animation: 150,
            handle: '.drag-handle',
            onEnd: function() {
                updateSortOrder();
                $('#save-order').show();
            }
        });
    }

    // Form tambah favorit
    $('#add-favorite-form').on('submit', function(e) {
        e.preventDefault();
        
        const productId = $('#product-search').val();
        if (!productId) {
            showAlert('Silakan pilih produk terlebih dahulu.', 'warning');
            return;
        }

        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menambahkan...');

        $.post('{{ route("setting.favorites.add") }}', {
            _token: csrfToken,
            product_id: productId
        })
        .done(function(response) {
            showAlert('Produk berhasil ditambahkan ke favorit.', 'success');
            setTimeout(() => location.reload(), 1000);
        })
        .fail(function(xhr) {
            let message = 'Terjadi kesalahan.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                message = Object.values(xhr.responseJSON.errors).flat().join('\n');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showAlert(message, 'danger');
        })
        .always(function() {
            $submitBtn.prop('disabled', false).html(originalText);
        });
    });

    // Toggle aktif/nonaktif
    $(document).on('click', '.toggle-favorite', function() {
        const id = $(this).data('id');
        const $card = $(this).closest('.favorite-card');
        const $button = $(this);
        const originalHtml = $button.html();
        
        $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: `{{ url('/setting/favorites/toggle') }}/${id}`,
            method: 'PATCH',
            data: {
                _token: csrfToken
            }
        })
        .done(function(response) {
            if (response.is_active) {
                $card.removeClass('inactive');
                $button.removeClass('btn-success').addClass('btn-warning')
                       .html('<i class="fa fa-eye-slash"></i>')
                       .attr('title', 'Nonaktifkan');
            } else {
                $card.addClass('inactive');
                $button.removeClass('btn-warning').addClass('btn-success')
                       .html('<i class="fa fa-eye"></i>')
                       .attr('title', 'Aktifkan');
            }
            showAlert(response.message, 'success');
        })
        .fail(function() {
            showAlert('Gagal mengubah status favorit.', 'danger');
        })
        .always(function() {
            $button.prop('disabled', false);
        });
    });

    // Hapus favorit
    $(document).on('click', '.delete-favorite', function() {
        if (!confirm('Yakin ingin menghapus favorit ini?')) return;
        
        const id = $(this).data('id');
        const $card = $(this).closest('.favorite-card');
        const $button = $(this);
        const originalHtml = $button.html();
        
        $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: `{{ url('/setting/favorites') }}/${id}`,
            method: 'DELETE',
            data: {
                _token: csrfToken
            }
        })
        .done(function(response) {
            $card.fadeOut(300, function() {
                $(this).remove();
                checkEmptyState();
            });
            showAlert(response.message, 'success');
        })
        .fail(function() {
            showAlert('Gagal menghapus favorit.', 'danger');
            $button.prop('disabled', false).html(originalHtml);
        });
    });

    // Simpan urutan
    $('#save-order').on('click', function() {
        const items = [];
        $('#favorites-list .favorite-card').each(function(index) {
            items.push({
                id: parseInt($(this).data('id')),
                sort_order: index + 1
            });
        });

        const $button = $(this);
        const originalText = $button.html();
        $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: '{{ route("setting.favorites.reorder") }}',
            method: 'PATCH',
            data: {
                _token: csrfToken,
                items: items
            }
        })
        .done(function(response) {
            $button.hide();
            showAlert(response.message, 'success');
        })
        .fail(function() {
            showAlert('Gagal menyimpan urutan.', 'danger');
        })
        .always(function() {
            $button.prop('disabled', false).html(originalText);
        });
    });

    function updateSortOrder() {
        $('#favorites-list .favorite-card').each(function(index) {
            $(this).data('sort', index + 1);
        });
    }

    function checkEmptyState() {
        if ($('#favorites-list .favorite-card').length === 0) {
            $('#favorites-list').html(`
                <div class="callout callout-info">
                    <h4><i class="fa fa-star-o"></i> Belum ada produk favorit</h4>
                    <p>Tambahkan produk menggunakan form pencarian di atas untuk membuat shortcut di halaman transaksi.</p>
                </div>
            `);
        }
    }

    function showAlert(message, type = 'info') {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'warning' ? 'alert-warning' : 
                          type === 'danger' ? 'alert-danger' : 'alert-info';
        
        const icon = type === 'success' ? 'fa-check' : 
                    type === 'warning' ? 'fa-warning' : 
                    type === 'danger' ? 'fa-times' : 'fa-info';

        $('#success-alert')
            .removeClass('alert-success alert-warning alert-danger alert-info')
            .addClass(alertClass)
            .find('i')
            .removeClass('fa-check fa-warning fa-times fa-info')
            .addClass(icon);
        
        $('#success-message').text(message);
        $('#success-alert').fadeIn();
        
        setTimeout(() => {
            $('#success-alert').fadeOut();
        }, 3000);
    }
});
</script>
@endpushsubmit', function(e) {
        e.preventDefault();
        
        const productId = $('#product-search').val();
        if (!productId) {
            showAlert('Silakan pilih produk terlebih dahulu.', 'warning');
            return;
        }

        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menambahkan...');

        $.post('{{ route("setting.favorites.add") }}', {
            _token: csrfToken,
            product_id: productId
        })
        .done(function(response) {
            showAlert('Produk berhasil ditambahkan ke favorit.', 'success');
            setTimeout(() => location.reload(), 1000);
        })
        .fail(function(xhr) {
            let message = 'Terjadi kesalahan.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                message = Object.values(xhr.responseJSON.errors).flat().join('\n');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showAlert(message, 'danger');
        })
        .always(function() {
            $submitBtn.prop('disabled', false).html(originalText);
        });
    });

    // Toggle aktif/nonaktif
    $(document).on('click', '.toggle-favorite', function() {
        const id = $(this).data('id');
        const $card = $(this).closest('.favorite-card');
        const $button = $(this);
        const originalHtml = $button.html();
        
        $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: `{{ url('/setting/favorites/toggle') }}/${id}`,
            method: 'PATCH',
            data: {
                _token: csrfToken
            }
        })
        .done(function(response) {
            if (response.is_active) {
                $card.removeClass('inactive');
                $button.removeClass('btn-success').addClass('btn-warning')
                       .html('<i class="fa fa-eye-slash"></i>')
                       .attr('title', 'Nonaktifkan');
            } else {
                $card.addClass('inactive');
                $button.removeClass('btn-warning').addClass('btn-success')
                       .html('<i class="fa fa-eye"></i>')
                       .attr('title', 'Aktifkan');
            }
            showAlert(response.message, 'success');
        })
        .fail(function() {
            showAlert('Gagal mengubah status favorit.', 'danger');
        })
        .always(function() {
            $button.prop('disabled', false);
        });
    });

    // Hapus favorit
    $(document).on('click', '.delete-favorite', function() {
        if (!confirm('Yakin ingin menghapus favorit ini?')) return;
        
        const id = $(this).data('id');
        const $card = $(this).closest('.favorite-card');
        const $button = $(this);
        const originalHtml = $button.html();
        
        $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: `{{ url('/setting/favorites') }}/${id}`,
            method: 'DELETE',
            data: {
                _token: csrfToken
            }
        })
        .done(function(response) {
            $card.fadeOut(300, function() {
                $(this).remove();
                checkEmptyState();
            });
            showAlert(response.message, 'success');
        })
        .fail(function() {
            showAlert('Gagal menghapus favorit.', 'danger');
            $button.prop('disabled', false).html(originalHtml);
        });
    });

    // Simpan urutan
    $('#save-order').on('click', function() {
        const items = [];
        $('#favorites-list .favorite-card').each(function(index) {
            items.push({
                id: parseInt($(this).data('id')),
                sort_order: index + 1
            });
        });

        const $button = $(this);
        const originalText = $button.html();
        $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: '{{ route("setting.favorites.reorder") }}',
            method: 'PATCH',
            data: {
                _token: csrfToken,
                items: items
            }
        })
        .done(function(response) {
            $button.hide();
            showAlert(response.message, 'success');
        })
        .fail(function() {
            showAlert('Gagal menyimpan urutan.', 'danger');
        })
        .always(function() {
            $button.prop('disabled', false).html(originalText);
        });
    });

    function updateSortOrder() {
        $('#favorites-list .favorite-card').each(function(index) {
            $(this).data('sort', index + 1);
        });
    }

    function checkEmptyState() {
        if ($('#favorites-list .favorite-card').length === 0) {
            $('#favorites-list').html(`
                <div class="callout callout-info">
                    <h4><i class="fa fa-star-o"></i> Belum ada produk favorit</h4>
                    <p>Tambahkan produk menggunakan form pencarian di atas untuk membuat shortcut di halaman transaksi.</p>
                </div>
            `);
        }
    }

    function showAlert(message, type = 'info') {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'warning' ? 'alert-warning' : 
                          type === 'danger' ? 'alert-danger' : 'alert-info';
        
        const icon = type === 'success' ? 'fa-check' : 
                    type === 'warning' ? 'fa-warning' : 
                    type === 'danger' ? 'fa-times' : 'fa-info';

        $('#success-alert')
            .removeClass('alert-success alert-warning alert-danger alert-info')
            .addClass(alertClass)
            .find('i')
            .removeClass('fa-check fa-warning fa-times fa-info')
            .addClass(icon);
        
        $('#success-message').text(message);
        $('#success-alert').fadeIn();
        
        setTimeout(() => {
            $('#success-alert').fadeOut();
        }, 3000);
    }
});
</script>
@endpush
@extends('layouts.master')

@section('title')
    Transaksi Penjualan
@endsection

@push('css')
    <style>
        /* Compact Layout Styles */
        .transaction-container {
            padding: 10px;
        }

        /* Compact form input */
        .form-compact {
            margin-bottom: 8px;
        }

        .form-compact .form-group {
            margin-bottom: 5px;
        }

        .form-compact label {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .form-compact .form-control {
            height: 30px;
            padding: 4px 8px;
            font-size: 12px;
        }

        /* Horizontal favorites panel */
        /* COMMENTED OUT - Favorites feature hidden
        .favorites-horizontal-container {
            background: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px;
            margin-bottom: 10px;
        }

        .favorites-title {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
        }

        .favorites-scroll {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            padding: 2px 0;
            white-space: nowrap;
        }

        .favorites-scroll::-webkit-scrollbar {
            height: 3px;
        }

        .favorites-scroll::-webkit-scrollbar-track {
            background: #e8e8e8;
            border-radius: 2px;
        }

        .favorites-scroll::-webkit-scrollbar-thumb {
            background: #3c8dbc;
            border-radius: 2px;
        }

        .fav-btn {
            min-width: 100px;
            height: 35px;
            padding: 3px 6px;
            font-size: 10px;
            line-height: 1.1;
            border: 1px solid #3c8dbc;
            background: #3c8dbc;
            color: white;
            border-radius: 3px;
            cursor: pointer;
            flex-shrink: 0;
            text-align: left;
            transition: all 0.2s ease;
        }

        .fav-btn:hover {
            background: #367fa9;
            transform: translateY(-1px);
            color: white;
        }

        .fav-btn:focus {
            outline: none;
            color: white;
        }

        .fav-btn .name {
            font-weight: bold;
            font-size: 9px;
            line-height: 1;
            margin-bottom: 1px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .fav-btn .price {
            font-size: 8px;
            opacity: 0.9;
        }
        */

        /* Compact table */
        .table-compact {
            font-size: 11px;
            margin-bottom: 8px;
        }

        .table-compact th {
            padding: 6px 4px;
            background: #f8f9fa;
            font-size: 11px;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .table-compact td {
            padding: 4px;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
        }

        /* Two column layout for better space usage */
        .transaction-layout {
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }

        .left-section {
            flex: 2;
        }

        .right-section {
            flex: 1;
            min-width: 300px;
        }

        /* Compact payment display */
        .payment-display {
            background: linear-gradient(135deg, #3c8dbc 0%, #367fa9 100%);
            color: white;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
            margin-bottom: 8px;
        }

        .payment-amount {
            font-size: 2.5em;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 5px;
        }

        .payment-text {
            font-size: 11px;
            background: rgba(255,255,255,0.1);
            padding: 4px 8px;
            border-radius: 3px;
            margin-top: 5px;
        }

        /* Compact form sections */
        .form-section {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 8px;
        }

        .form-section-title {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #eee;
        }

        .form-row-compact {
            display: flex;
            gap: 8px;
            margin-bottom: 6px;
            align-items: center;
        }

        .form-row-compact label {
            min-width: 70px;
            font-size: 11px;
            font-weight: 600;
            margin: 0;
        }

        .form-row-compact .form-control {
            height: 28px;
            font-size: 11px;
            padding: 3px 6px;
        }

        .form-row-compact .input-group-btn .btn {
            height: 28px;
            padding: 3px 8px;
            font-size: 11px;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .transaction-layout {
                flex-direction: column;
            }
            
            .right-section {
                min-width: auto;
            }
            
            .payment-amount {
                font-size: 2em;
            }
        }

        @media (max-width: 768px) {
            .fav-btn {
                min-width: 80px;
                height: 30px;
            }
            
            .payment-amount {
                font-size: 1.8em;
            }
            
            .form-row-compact {
                flex-direction: column;
                align-items: stretch;
                gap: 4px;
            }
            
            .form-row-compact label {
                min-width: auto;
            }
        }

        /* Hide empty state for cleaner look */
        /* COMMENTED OUT - Favorites feature hidden
        .favorites-empty {
            text-align: center;
            padding: 10px;
            color: #999;
            font-size: 11px;
        }
        */

        /* Compact button styles */
        .btn-compact {
            padding: 4px 8px;
            font-size: 11px;
            height: 28px;
        }

        /* Table responsive improvements */
        .table-responsive {
            border: none;
            margin-bottom: 8px;
        }

        /* Compact box styling */
        .box-compact {
            margin-bottom: 10px;
        }

        .box-compact .box-body {
            padding: 10px;
        }
    </style>
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Transaksi Penjualan</li>
@endsection

@section('content')
<div class="transaction-container">
    <div class="box box-compact">
        <div class="box-body">
            <!-- Compact Product Input Form -->
            <div class="form-compact">
                <form class="form-produk">
                    @csrf
                    <div class="form-row-compact">
                        <label for="kode_produk">Kode Produk</label>
                        <div class="input-group" style="flex: 1;">
                            <input type="hidden" name="id_penjualan" id="id_penjualan" value="{{ $id_penjualan }}">
                            <input type="hidden" name="id_produk" id="id_produk">
                            <input type="text" class="form-control" name="kode_produk" id="kode_produk" placeholder="Scan/ketik kode produk">
                            <span class="input-group-btn">
                                <button onclick="tampilProduk()" class="btn btn-info btn-compact" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Horizontal Favorites Panel -->
            <!-- COMMENTED OUT - Favorites feature hidden
            <div class="favorites-horizontal-container" id="favorites-container">
                <div class="favorites-title">
                    <i class="fa fa-star text-warning"></i>
                    <span style="margin-left: 5px;">Produk Favorit</span>
                    <span id="fav-count" class="label label-primary" style="margin-left: 8px; display: none;">0</span>
                </div>
                <div class="favorites-scroll" id="favorites-scroll">
                    <div class="favorites-empty">
                        <i class="fa fa-spinner fa-spin"></i> Memuat...
                    </div>
                </div>
            </div>
            -->

            <!-- Two Column Layout -->
            <div class="transaction-layout">
                <!-- Left Section: Table -->
                <div class="left-section">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-penjualan table-compact">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Kode</th>
                                    <th>Nama</th>
                                    <th width="12%">Harga</th>
                                    <th width="10%">Qty</th>
                                    <th width="8%">Disc</th>
                                    <th width="12%">Subtotal</th>
                                    <th width="8%"><i class="fa fa-cog"></i></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <!-- Right Section: Payment & Controls -->
                <div class="right-section">
                    <!-- Payment Display -->
                    <div class="payment-display">
                        <div class="payment-amount tampil-bayar">Rp. 0</div>
                        <div class="payment-text tampil-terbilang">Rupiah</div>
                    </div>

                    <!-- Transaction Form -->
                    <form action="{{ route('transaksi.simpan') }}" class="form-penjualan" method="post">
                        @csrf
                        <input type="hidden" name="id_penjualan" value="{{ $id_penjualan }}">
                        <input type="hidden" name="total" id="total">
                        <input type="hidden" name="total_item" id="total_item">
                        <input type="hidden" name="bayar" id="bayar">
                        <input type="hidden" name="id_member" id="id_member" value="{{ $memberSelected->id_member }}">

                        <!-- Summary Section -->
                        <div class="form-section">
                            <div class="form-section-title">Ringkasan</div>
                            <div class="form-row-compact">
                                <label>Total</label>
                                <input type="text" id="totalrp" class="form-control" readonly>
                            </div>
                            @if(auth()->user()->level == 1)
                            <div class="form-row-compact">
                                <label>Keuntungan</label>
                                <input type="text" id="totalkeuntungan" class="form-control" readonly>
                            </div>
                            @endif
                        </div>

                        <!-- Member & Discount Section -->
                        <div class="form-section">
                            <div class="form-section-title">Member & Diskon</div>
                            <div class="form-row-compact">
                                <label>Member</label>
                                <div class="input-group" style="flex: 1;">
                                    <input type="text" class="form-control" id="kode_member" value="{{ $memberSelected->kode_member }}" placeholder="Kode member">
                                    <span class="input-group-btn">
                                        <button onclick="tampilMember()" class="btn btn-info btn-compact" type="button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-row-compact">
                                <label>Diskon</label>
                                <div class="input-group" style="flex: 1;">
                                    <input type="number" name="diskon" id="diskon" class="form-control" value="{{ !empty($memberSelected->id_member) ? $diskon : 0 }}" readonly>
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Section -->
                        <div class="form-section">
                            <div class="form-section-title">Pembayaran</div>
                            <div class="form-row-compact">
                                <label>Bayar</label>
                                <input type="text" id="bayarrp" class="form-control" readonly>
                            </div>
                            <div class="form-row-compact">
                                <label>Diterima</label>
                                <input type="number" id="diterima" class="form-control" name="diterima" value="{{ $penjualan->diterima ?? 0 }}" placeholder="0">
                            </div>
                            <div class="form-row-compact">
                                <label>Kembali</label>
                                <input type="text" id="kembali" name="kembali" class="form-control" value="0" readonly>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <button type="submit" class="btn btn-primary btn-block btn-simpan">
                            <i class="fa fa-save"></i> Simpan Transaksi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@includeIf('penjualan_detail.produk')
@includeIf('penjualan_detail.member')
@endsection

@push('scripts')
<script>
let table, table2;

$(function() {
    $('body').addClass('sidebar-collapse');

    // Load favorites
    // COMMENTED OUT - Favorites feature hidden
    // loadFavorites();

    table = $('.table-penjualan').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: '{{ route('transaksi.data', $id_penjualan) }}',
        },
        columns: [
            { data: 'DT_RowIndex', searchable: false, sortable: false },
            { data: 'kode_produk' },
            { data: 'nama_produk' },
            { data: 'harga_jual' },
            { data: 'jumlah' },
            { data: 'diskon' },
            { data: 'subtotal' },
            { data: 'aksi', searchable: false, sortable: false },
        ],
        dom: 'Brt',
        bSort: false,
        paginate: false,
        scrollX: true
    })
    .on('draw.dt', function() {
        loadForm($('#diskon').val());
        setTimeout(() => $('#diterima').trigger('input'), 300);
    });

    table2 = $('.table-produk').DataTable();

    // Event handlers (unchanged)
    $(document).on('input', '.quantity', function() {
        let id = $(this).data('id');
        let jumlah = parseInt($(this).val());

        if (jumlah < 1) {
            $(this).val(1);
            alert('Jumlah tidak boleh kurang dari 1');
            return;
        }
        if (jumlah > 10000) {
            $(this).val(10000);
            alert('Jumlah tidak boleh lebih dari 10000');
            return;
        }

        $.post(`{{ url('/transaksi') }}/${id}`, {
            '_token': $('[name=csrf-token]').attr('content'),
            '_method': 'put',
            'jumlah': jumlah
        })
        .done(response => {
            $(this).on('mouseout', function() {
                table.ajax.reload(() => loadForm($('#diskon').val()));
            });
        })
        .fail(() => alert('Tidak dapat menyimpan data'));
    });

    $(document).on('input', '#diskon', function() {
        if ($(this).val() == "") {
            $(this).val(0).select();
        }
        loadForm($(this).val());
    });

    $('#diterima').on('input', function() {
        if ($(this).val() == "") {
            $(this).val(0).select();
        }
        loadForm($('#diskon').val(), $(this).val());
    }).focus(function() {
        $(this).select();
    });

    $('.btn-simpan').on('click', function() {
        $('.form-penjualan').submit();
    });

    $('#kode_produk').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            var kode_produk = $(this).val();
            $.post('{{ route('transaksi.store') }}', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                kode_produk: kode_produk,
                id_penjualan: $('#id_penjualan').val()
            })
            .done(response => {
                $('#kode_produk').val('').focus();
                table.ajax.reload(() => loadForm($('#diskon').val()));
            })
            .fail(() => alert('Tidak dapat menyimpan data'));
        }
    });
});

// Compact favorites functions
// COMMENTED OUT - Favorites feature hidden
/*
function loadFavorites() {
    $.getJSON('{{ route("transactions.favorites") }}')
        .done(function(favorites) {
            renderCompactFavorites(favorites);
        })
        .fail(function() {
            $('#favorites-scroll').html('<div class="favorites-empty"><i class="fa fa-exclamation-triangle"></i> Gagal memuat</div>');
        });
}

function renderCompactFavorites(favorites) {
    const $scroll = $('#favorites-scroll').empty();
    const $count = $('#fav-count');

    if (favorites.length === 0) {
        $scroll.html('<div class="favorites-empty"><i class="fa fa-star-o"></i> Belum ada favorit</div>');
        $count.hide();
        return;
    }

    $count.text(favorites.length).show();

    favorites.forEach(function(item) {
        const $btn = $(`
            <button class="fav-btn" data-id="${item.product_id}" data-code="${item.kode}" title="${item.nama}">
                <div class="name">${truncateText(item.nama, 14)}</div>
                <div class="price">Rp. ${formatNumber(item.harga)}</div>
            </button>
        `);

        $btn.on('click', function() {
            addToCart(item);
        });

        $scroll.append($btn);
    });
}

function addToCart(product) {
    const $btn = $(`button[data-id="${product.product_id}"]`);
    const originalHtml = $btn.html();

    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

    $.post('{{ route("transaksi.store") }}', {
        _token: $('meta[name="csrf-token"]').attr('content'),
        kode_produk: product.kode,
        id_penjualan: $('#id_penjualan').val()
    })
    .done(function() {
        $btn.html('<i class="fa fa-check"></i>');
        table.ajax.reload(() => loadForm($('#diskon').val()));
        $('#kode_produk').focus();

        setTimeout(() => {
            $btn.prop('disabled', false).html(originalHtml);
        }, 1000);
    })
    .fail(function() {
        $btn.html('<i class="fa fa-times"></i>');
        setTimeout(() => {
            $btn.prop('disabled', false).html(originalHtml);
        }, 1500);
    });
}

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

function truncateText(text, maxLength) {
    return text.length <= maxLength ? text : text.substring(0, maxLength - 3) + '...';
}
*/

// Original functions (unchanged)
function tampilProduk() {
    $('#modal-produk').modal('show');
}

function hideProduk() {
    $('#modal-produk').modal('hide');
}

function pilihProduk(id, kode) {
    $('#id_produk').val(id);
    $('#kode_produk').val(kode);
    hideProduk();
    tambahProduk();
}

function tambahProduk() {
    $.post('{{ route('transaksi.store') }}', $('.form-produk').serialize())
        .done(response => {
            $('#kode_produk').val('').focus();
            table.ajax.reload(() => loadForm($('#diskon').val()));
        })
        .fail(() => alert('Tidak dapat menyimpan data'));
}

function tampilMember() {
    $('#modal-member').modal('show');
}

function pilihMember(id, kode) {
    $('#id_member').val(id);
    $('#kode_member').val(kode);
    $('#diskon').val('{{ $diskon }}');
    loadForm($('#diskon').val());
    $('#diterima').val(0).focus().select();
    hideMember();
}

function hideMember() {
    $('#modal-member').modal('hide');
}

function deleteData(url) {
    if (confirm('Yakin ingin menghapus data terpilih?')) {
        $.post(url, {
            '_token': $('[name=csrf-token]').attr('content'),
            '_method': 'delete'
        })
        .done(() => table.ajax.reload(() => loadForm($('#diskon').val())))
        .fail(() => alert('Tidak dapat menghapus data'));
    }
}

function loadForm(diskon = 0, diterima = 0) {
    $('#total').val($('.total').text());
    $('#total_item').val($('.total_item').text());

    $.get(`{{ url('/transaksi/loadform') }}/${diskon}/${$('.total').text()}/${diterima}`)
        .done(response => {
            $('#totalrp').val('Rp. ' + response.totalrp);
            $('#bayarrp').val('Rp. ' + response.bayarrp);
            $('#bayar').val(response.bayar);
            $('.tampil-bayar').text('Rp. ' + response.bayarrp);
            $('.tampil-terbilang').text(response.terbilang);
            $('#totalkeuntungan').val('Rp. ' + response.keuntunganrp);
            $('#kembali').val('Rp.' + response.kembalirp);
            
            if ($('#diterima').val() != 0) {
                $('.tampil-bayar').text('Rp. ' + response.kembalirp);
                $('.tampil-terbilang').text(response.kembali_terbilang);
            }
        })
        .fail(() => alert('Tidak dapat menampilkan data'));
}
</script>
@endpush
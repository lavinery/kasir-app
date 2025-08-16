@extends('layouts.master')

@section('title')
    Daftar Produk
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Produk</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="btn-group">
                    <button onclick="addForm('{{ route('produk.store') }}')" class="btn btn-success btn-xs btn-flat">
                        <i class="fa fa-plus-circle"></i> Tambah
                    </button>

                    <button onclick="deleteSelected('{{ route('produk.delete_selected') }}')" class="btn btn-danger btn-xs btn-flat">
                        <i class="fa fa-trash"></i> Hapus
                    </button>
    <div class="btn-group">
        <button type="button" class="btn btn-info btn-xs btn-flat dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-barcode"></i> Cetak Barcode <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><a href="#" onclick="cetakBarcode('{{ route('produk.cetak_barcode') }}')">PDF (Semua)</a></li>
            <li><a href="#" onclick="barcodePNG('{{ route('produk.barcode_png') }}')">PNG (Semua)</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#" onclick="cetakBarcodeLabel('{{ route('produk.cetak_barcode_label_105') }}', 105)">PDF Label 105 mm</a></li>
            <li><a href="#" onclick="cetakBarcodeLabel('{{ route('produk.cetak_barcode_label_107') }}', 107)">PDF Label 107 mm</a></li>
            <li><a href="#" onclick="cetakBarcodeLabel33x15('{{ route('produk.cetak_barcode_label_33x15') }}')">PDF Label 33x15 mm</a></li>
        </ul>
    </div>

    <button onclick="cetakDaftar('{{ route('produk.cetak_daftar') }}')" class="btn btn-warning btn-xs btn-flat">
        <i class="fa fa-print"></i> Cetak Daftar Produk
    </button>

    <!-- Button Export Excel yang baru -->
    <div class="btn-group">
    <button type="button" class="btn btn-primary btn-xs btn-flat dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-download"></i> Export Data <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <li><a href="#" onclick="exportExcel('{{ route('produk.export_excel') }}')">
            <i class="fa fa-file-excel-o"></i> Export Excel (Terpilih)
        </a></li>
        <li><a href="#" onclick="exportAllExcel('{{ route('produk.export_excel') }}')">
            <i class="fa fa-file-excel-o"></i> Export Excel (Semua)
        </a></li>
        <li role="separator" class="divider"></li>
        <li><a href="#" onclick="cetakDaftar('{{ route('produk.cetak_daftar') }}')">
            <i class="fa fa-print"></i> Export PDF
        </a></li>
    </ul>
</div>
</div>


                <div style="margin-top: 10px;">
                    <label>Jumlah copy per barcode</label>
                    <input type="number" id="jumlah_copy_global" value="1" min="1" class="form-control" style="width: 80px; display: inline-block;">
                </div>
            </div>

            <div class="box-body table-responsive">
                <form action="" method="post" class="form-produk">
                    @csrf
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="5%"><input type="checkbox" name="select_all" id="select_all"></th>
                                <th width="5%">No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Merk</th>
                                @if(auth()->user()->level == 1)
                                    <th>Harga Beli</th>
                                    <th>Keuntungan</th>
                                @endif
                                <th>Harga Jual</th>
                                <th>Diskon</th>
                                <th>Stok</th>
                                <th width="15%"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

@includeIf('produk.form')
@endsection

@push('scripts')
<script>
let table;

$(function () {
    table = $('.table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: '{{ route('produk.data') }}'
        },
        columns: [
            { data: 'select_all', searchable: false, sortable: false },
            { data: 'DT_RowIndex', searchable: false, sortable: false },
            { data: 'kode_produk' },
            { data: 'nama_produk' },
            { data: 'nama_kategori' },
            { data: 'merk' },
            @if(auth()->user()->level == 1)
                { data: 'harga_beli' },
                { data: 'keuntungan' },
            @endif
            { data: 'harga_jual' },
            { data: 'diskon' },
            { data: 'stok' },
            { data: 'aksi', searchable: false, sortable: false }
        ]
    });

    $('#modal-form').validator().on('submit', function (e) {
        if (!e.preventDefault()) {
            $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                .done(() => {
                    $('#modal-form').modal('hide');
                    table.ajax.reload();
                })
                .fail(() => {
                    alert('Tidak dapat menyimpan data');
                });
        }
    });

    $('[name=select_all]').on('click', function () {
        $(':checkbox').prop('checked', this.checked);
    });

    $('#harga_beli, #harga_jual').on('input', function () {
        const beli = parseFloat($('#harga_beli').val()) || 0;
        const jual = parseFloat($('#harga_jual').val()) || 0;
        $('#keuntungan').val(jual - beli);
    });
});

function addForm(url) {
    $('#modal-form').modal('show');
    $('#modal-form .modal-title').text('Tambah Produk');
    $('#modal-form form')[0].reset();
    $('#modal-form form').attr('action', url);
    $('#modal-form [name=_method]').val('post');
    $('#modal-form [name=nama_produk]').focus();
}

function editForm(url) {
    $('#modal-form').modal('show');
    $('#modal-form .modal-title').text('Edit Produk');
    $('#modal-form form')[0].reset();
    $('#modal-form form').attr('action', url);
    $('#modal-form [name=_method]').val('put');
    $('#modal-form [name=nama_produk]').focus();

    $.get(url)
        .done((response) => {
            $('#modal-form [name=kode_produk]').val(response.kode_produk);
            $('#modal-form [name=nama_produk]').val(response.nama_produk);
            $('#modal-form [name=id_kategori]').val(response.id_kategori);
            $('#modal-form [name=merk]').val(response.merk);
            $('#modal-form [name=harga_beli]').val(response.harga_beli);
            $('#modal-form [name=harga_jual]').val(response.harga_jual);
            $('#modal-form [name=keuntungan]').val(response.keuntungan);
            $('#modal-form [name=diskon]').val(response.diskon);
            $('#modal-form [name=stok]').val(response.stok);
        })
        .fail(() => {
            alert('Tidak dapat menampilkan data');
        });
}

function deleteData(url) {
    if (confirm('Yakin ingin menghapus data terpilih?')) {
        $.post(url, {
            '_token': $('[name=csrf-token]').attr('content'),
            '_method': 'delete'
        })
        .done(() => {
            table.ajax.reload();
        })
        .fail(() => {
            alert('Tidak dapat menghapus data');
        });
    }
}

function deleteSelected(url) {
    if ($('input:checked').length > 1) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, $('.form-produk').serialize())
                .done(() => {
                    table.ajax.reload();
                })
                .fail(() => {
                    alert('Tidak dapat menghapus data');
                });
        }
    } else {
        alert('Pilih data yang akan dihapus');
    }
}

function cetakDaftar(url) {
    if ($('input:checked').length < 1) {
        alert('Pilih data yang akan dicetak');
        return;
    }
    $('.form-produk')
        .attr('target', '_blank')
        .attr('action', url)
        .submit();
}

function cetakBarcode(url) {
    if ($('input:checked').length < 1) {
        alert('Pilih data yang akan dicetak');
        return;
    }
    
    // Ambil nilai jumlah copy
    let jumlahCopy = $('#jumlah_copy_global').val() || 1;
    
    // Tambahkan field jumlah_copy_global ke form
    $('<input>').attr({
        type: 'hidden',
        name: 'jumlah_copy_global',
        value: jumlahCopy
    }).appendTo('.form-produk');
    
    $('.form-produk')
        .attr('target', '_blank')
        .attr('action', url)
        .submit();
}

function barcodePNG(url) {
    if ($('input:checked').length < 1) {
        alert('Pilih data yang akan dicetak');
        return;
    }
    $('.form-produk')
        .attr('target', '_blank')
        .attr('action', url)
        .submit();
}

function cetakBarcodeLabel(url, ukuran) {
    if ($('input:checked').length < 1) {
        alert('Pilih data yang akan dicetak');
        return;
    }
    let jumlahCopy = $('#jumlah_copy_global').val() || 1;
    $('<input>').attr({
        type: 'hidden',
        name: 'jumlah_copy_global',
        value: jumlahCopy
    }).appendTo('.form-produk');

    $('.form-produk')
        .attr('target', '_blank')
        .attr('action', url)
        .submit();
}
function cetakBarcodeLabel33x15(url) {
    if ($('input:checked').length < 1) {
        alert('Pilih data yang akan dicetak');
        return;
    }
    
    let jumlahCopy = $('#jumlah_copy_global').val() || 1;
    
    // Hapus input hidden yang mungkin sudah ada sebelumnya
    $('input[name="jumlah_copy_global"]').remove();
    
    // Tambahkan field jumlah_copy_global ke form
    $('<input>').attr({
        type: 'hidden',
        name: 'jumlah_copy_global',
        value: jumlahCopy
    }).appendTo('.form-produk');

    $('.form-produk')
        .attr('target', '_blank')
        .attr('action', url)
        .submit();
}
function exportExcel(url) {
    if ($('input:checked').length < 1) {
        alert('Pilih data yang akan di export');
        return;
    }
    
    // Tampilkan loading
    Swal.fire({
        title: 'Mengexport data...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Submit form untuk export
    $('.form-produk')
        .attr('target', '_blank')
        .attr('action', url)
        .submit();
    
    // Tutup loading setelah 2 detik
    setTimeout(() => {
        Swal.close();
    }, 2000);
}

// Alternatif function exportExcel tanpa SweetAlert (jika tidak menggunakan SweetAlert)
function exportExcel(url) {
    if ($('input:checked').length < 1) {
        alert('Pilih data yang akan di export');
        return;
    }
    
    $('.form-produk')
        .attr('target', '_blank')
        .attr('action', url)
        .submit();
}

// Function untuk export semua data (opsional)
function exportAllExcel(url) {
    // Centang semua checkbox terlebih dahulu
    $('input[type="checkbox"]').prop('checked', true);
    
    if ($('input:checked').length < 1) {
        alert('Tidak ada data untuk di export');
        return;
    }
    
    $('.form-produk')
        .attr('target', '_blank')
        .attr('action', url)
        .submit();
}
</script>
@endpush

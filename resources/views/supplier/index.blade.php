@extends('layouts.master')

@section('title')
    Daftar Supplier
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Supplier</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="addForm('{{ route('supplier.store') }}')" class="btn btn-success btn-xs btn-flat">
                    <i class="fa fa-plus-circle"></i> Tambah
                </button>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th width="15%"><i class="fa fa-cog"></i></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('supplier.form')
@endsection

@push('scripts')
<script>
    let table;

    $(function () {
        // Setup CSRF untuk semua request AJAX
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // DataTable
        table = $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: { url: '{{ route('supplier.data') }}' },
            columns: [
                {data: 'DT_RowIndex', searchable: false, orderable: false},
                {data: 'nama'},
                {data: 'telepon'},
                {data: 'alamat'},
                {data: 'aksi', searchable: false, orderable: false},
            ],
            language: {
                processing: "Memuat data...",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data supplier",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                search: "Cari:",
                paginate: { first: "Pertama", last: "Terakhir", next: "Selanjutnya", previous: "Sebelumnya" }
            }
        });

        // Submit form modal (create & update) via AJAX
        const $form = $('#modal-form form');
        $form.on('submit', function (e) {
            e.preventDefault();

            $.post($form.attr('action'), $form.serialize())
                .done(function () {
                    $('#modal-form').modal('hide');
                    table.ajax.reload(null, false);
                    alert('Data berhasil disimpan!');
                })
                .fail(function (xhr) {
                    let msg = 'Tidak dapat menyimpan data';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const firstErr = Object.values(xhr.responseJSON.errors)[0][0];
                        msg = firstErr || msg;
                    }
                    alert(msg);
                });

            return false;
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Supplier');

        const $f = $('#modal-form form');
        $f[0].reset();
        $f.attr('action', url);
        $f.find('[name=_method]').val('post');
        $('#modal-form [name=nama]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Supplier');

        const $f = $('#modal-form form');
        $f[0].reset();
        $f.attr('action', url.replace('/show', '')); // jaga-jaga kalau route('supplier.show', id) dipakai; action tetap ke /supplier/{id}
        $f.find('[name=_method]').val('put');

        // Ambil data untuk prefill (harus GET ke supplier.show)
        $.get(url)
            .done((response) => {
                $('#modal-form [name=nama]').val(response.nama);
                $('#modal-form [name=telepon]').val(response.telepon);
                $('#modal-form [name=alamat]').val(response.alamat);
            })
            .fail(() => {
                alert('Tidak dapat menampilkan data');
            });
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, { _method: 'delete' })
                .done(() => {
                    table.ajax.reload(null, false);
                    alert('Data berhasil dihapus!');
                })
                .fail(() => {
                    alert('Tidak dapat menghapus data');
                });
        }
    }
</script>
@endpush

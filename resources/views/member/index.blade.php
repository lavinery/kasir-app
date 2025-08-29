@extends('layouts.master')

@section('title')
    Daftar Member
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Member</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-id-card"></i> Daftar Member
                </h3>
                <div class="box-tools pull-right">
                    {{-- Collapse Button --}}
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            
            {{-- Action Buttons Section --}}
            <div class="box-body" style="border-bottom: 1px solid #f4f4f4; padding-bottom: 15px; margin-bottom: 0;">
                <div class="row">
                    <div class="col-md-6">
                        {{-- Tombol Tambah Member --}}
                        <button onclick="addForm('{{ route('member.store') }}')" class="btn btn-success btn-sm">
                            <i class="fa fa-plus-circle"></i> Tambah Member
                        </button>
                        
                        {{-- Tombol Hapus Yang Dipilih --}}
                        <button onclick="deleteSelected('{{ route('member.destroy', '') }}')" class="btn btn-danger btn-sm">
                            <i class="fa fa-trash"></i> Hapus Yang Dipilih
                        </button>
                        
                        {{-- Tombol Cetak Kartu --}}
                        <button onclick="cetakMember('{{ route('member.cetak_member') }}')" class="btn btn-info btn-sm">
                            <i class="fa fa-id-card-o"></i> Cetak Kartu Member
                        </button>
                    </div>
                    <div class="col-md-6 text-right">
                        {{-- Tombol Laporan Member (khusus admin) --}}
                        @if(auth()->user()->level == 1)
                            <a href="{{ route('member_stats.index') }}" class="btn btn-primary btn-sm" 
                               title="Lihat Laporan Transaksi Member" data-toggle="tooltip">
                                <i class="fa fa-line-chart"></i> Laporan Member
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="box-body" style="padding-top: 0;">
                <form action="" method="post" class="form-member">
                    @csrf
                    <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <table class="table table-striped table-bordered" id="member-table" style="min-width: 700px;">
                            <thead>
                                <tr>
                                    <th width="5%">
                                        <input type="checkbox" name="select_all" id="select_all">
                                    </th>
                                    <th width="5%">No</th>
                                    <th width="15%">Kode Member</th>
                                    <th width="25%">Nama</th>
                                    <th width="15%">Telepon</th>
                                    <th width="20%">Alamat</th>
                                    <th width="15%"><i class="fa fa-cog"></i> Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data akan dimuat via DataTables AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@includeIf('member.form')
@endsection

@push('scripts')
<script>
    let table;

    $(function () {
        // DataTable
        table = $('#member-table').DataTable({
            responsive: false, // Disable responsive untuk scroll horizontal
            processing: true,
            serverSide: true,
            autoWidth: false,
            scrollX: true, // Enable horizontal scroll
            scrollCollapse: true,
            ajax: {
                url: '{{ route('member.data') }}',
            },
            columns: [
                {data: 'select_all', searchable: false, orderable: false, width: '50px'},
                {data: 'DT_RowIndex', searchable: false, orderable: false, width: '50px'},
                {data: 'kode_member', width: '120px'},
                {data: 'nama', width: '150px'},
                {data: 'telepon', width: '120px'},
                {data: 'alamat', width: '200px'},
                {data: 'aksi', searchable: false, orderable: false, width: '120px'},
            ],
            language: {
                processing: "Memuat data...",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data member",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                search: "Cari:",
                paginate: { first: "Pertama", last: "Terakhir", next: "Selanjutnya", previous: "Sebelumnya" }
            }
        });

        // Submit form modal via AJAX (create & update)
        const $form = $('#modal-form form');
        $form.on('submit', function (e) {
            e.preventDefault(); // cegah submit normal

            $.post($form.attr('action'), $form.serialize())
                .done(function () {
                    $('#modal-form').modal('hide');
                    table.ajax.reload(null, false);
                    alert('Data berhasil disimpan!');
                })
                .fail(function (xhr) {
                    // kalau ada pesan validasi Laravel, tampilkan simple alert
                    let msg = 'Tidak dapat menyimpan data';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const firstErr = Object.values(xhr.responseJSON.errors)[0][0];
                        msg = firstErr || msg;
                    }
                    alert(msg);
                });

            return false;
        });

        // Tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Checkbox Select All
        $(document).on('change', '#select_all', function () {
            $('input[name="id_member[]"]').prop('checked', this.checked);
        });
        $(document).on('change', 'input[name="id_member[]"]', function() {
            let total = $('input[name="id_member[]"]').length;
            let checked = $('input[name="id_member[]"]:checked').length;
            $('#select_all').prop('checked', total === checked);
        });

        // Setup CSRF untuk semua AJAX (pastikan ada <meta name="csrf-token"> di layout)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Member');

        const $f = $('#modal-form form');
        $f[0].reset();
        $f.attr('action', url);
        $f.find('[name=_method]').val('post');
        $('#modal-form [name=nama]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Member');

        const $f = $('#modal-form form');
        $f[0].reset();
        $f.attr('action',  url);
        $f.find('[name=_method]').val('put');

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
        if (confirm('Apakah Anda yakin ingin menghapus member ini?\n\nData yang sudah dihapus tidak dapat dikembalikan.')) {
            $.post(url, { _method: 'delete' })
                .done(() => {
                    table.ajax.reload(null, false);
                    alert('Data berhasil dihapus!');
                })
                .fail(() => {
                    alert('Tidak dapat menghapus data. Silakan coba lagi.');
                });
        }
    }

    function deleteSelected() {
        let checked = $('input[name="id_member[]"]:checked');

        if (checked.length < 1) {
            alert('Pilih data yang akan dihapus terlebih dahulu');
            return;
        }
        
        if (confirm('Apakah Anda yakin ingin menghapus ' + checked.length + ' member yang dipilih?\n\nData yang sudah dihapus tidak dapat dikembalikan.')) {
            // kirim ke endpoint bulk (POST + _method=delete)
            const ids = checked.map(function(){ return $(this).val(); }).get();

            $.post('{{ url()->current() }}/bulk-destroy', { // sesuaikan dengan route di bawah
                _method: 'delete',
                id_member: ids
            })
            .done(() => {
                table.ajax.reload(null, false);
                $('#select_all').prop('checked', false);
                alert('Data berhasil dihapus!');
            })
            .fail((xhr) => {
                let msg = 'Tidak dapat menghapus data. Silakan coba lagi.';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
            });
        }
    }

    function cetakMember(url) {
        let checkedCount = $('input[name="id_member[]"]:checked').length;
        
        if (checkedCount < 1) {
            alert('Pilih member yang akan dicetak kartu terlebih dahulu');
            return;
        }
        
        if (confirm('Cetak kartu untuk ' + checkedCount + ' member yang dipilih?')) {
            $('.form-member')
                .attr('target', '_blank')
                .attr('action', url)
                .submit();
        }
    }
</script>
@endpush

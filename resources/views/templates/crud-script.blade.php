{{-- Template JavaScript untuk CRUD dengan Notifikasi --}}
<script>
let table;

$(function () {
    // Inisialisasi DataTable
    table = $('.table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: '{{ $dataUrl ?? "" }}'
        },
        columns: {!! $columns ?? "[]" !!}
    });

    // Handle form submit dengan notifikasi
    $('#modal-form').validator().on('submit', function (e) {
        if (!e.preventDefault()) {
            $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                .done((response) => {
                    $('#modal-form').modal('hide');
                    table.ajax.reload();
                    
                    // Tampilkan notifikasi sukses
                    if (response.success) {
                        showNotification('success', response.message);
                    } else {
                        showNotification('error', response.message || 'Terjadi kesalahan!');
                    }
                })
                .fail((xhr) => {
                    handleAjaxError(xhr, 'Tidak dapat menyimpan data');
                });
        }
    });
});

// Function untuk menambah data
function addForm(url) {
    $('#modal-form').modal('show');
    $('#modal-form .modal-title').text('Tambah {{ $itemName ?? "Data" }}');
    $('#modal-form form')[0].reset();
    $('#modal-form form').attr('action', url);
    $('#modal-form [name=_method]').val('post');
    $('#modal-form [name={{ $firstField ?? "nama" }}]').focus();
}

// Function untuk edit data
function editForm(url) {
    $('#modal-form').modal('show');
    $('#modal-form .modal-title').text('Edit {{ $itemName ?? "Data" }}');
    $('#modal-form form')[0].reset();
    $('#modal-form form').attr('action', url);
    $('#modal-form [name=_method]').val('put');
    $('#modal-form [name={{ $firstField ?? "nama" }}]').focus();

    $.get(url)
        .done((response) => {
            if (response) {
                // Fill form fields - customize sesuai kebutuhan
                Object.keys(response).forEach(key => {
                    $(`#modal-form [name=${key}]`).val(response[key]);
                });
            } else {
                showNotification('error', 'Data tidak ditemukan!');
                $('#modal-form').modal('hide');
            }
        })
        .fail((xhr) => {
            handleAjaxError(xhr, 'Tidak dapat menampilkan data');
            $('#modal-form').modal('hide');
        });
}

// Function untuk hapus data
function deleteData(url) {
    if (confirmDelete('Yakin ingin menghapus {{ $itemName ?? "data" }} ini?')) {
        $.post(url, {
            '_token': $('[name=csrf-token]').attr('content'),
            '_method': 'delete'
        })
        .done((response) => {
            table.ajax.reload();
            
            if (response.success) {
                showNotification('success', response.message);
            } else {
                showNotification('error', response.message || 'Terjadi kesalahan!');
            }
        })
        .fail((xhr) => {
            handleAjaxError(xhr, 'Tidak dapat menghapus data');
        });
    }
}

// Function untuk hapus multiple data
function deleteSelected(url) {
    const checkedCount = $('input[name="{{ $checkboxName ?? "id[]" }}"]:checked').length;
    
    if (checkedCount === 0) {
        showNotification('warning', 'Pilih {{ $itemName ?? "data" }} yang akan dihapus!');
        return;
    }
    
    const message = checkedCount === 1 
        ? `Yakin ingin menghapus {{ $itemName ?? "data" }} yang dipilih?` 
        : `Yakin ingin menghapus ${checkedCount} {{ $itemName ?? "data" }} yang dipilih?`;
    
    if (confirmDelete(message)) {
        $.post(url, $('.form-{{ $formClass ?? "data" }}').serialize())
            .done((response) => {
                table.ajax.reload();
                
                if (response.success) {
                    showNotification('success', response.message);
                } else {
                    showNotification('error', response.message || 'Terjadi kesalahan!');
                }
            })
            .fail((xhr) => {
                handleAjaxError(xhr, 'Tidak dapat menghapus data');
            });
    }
}

// Function untuk cetak data
function cetakData(url) {
    const checkedCount = $('input[name="{{ $checkboxName ?? "id[]" }}"]:checked').length;
    
    if (checkedCount < 1) {
        showNotification('warning', 'Pilih {{ $itemName ?? "data" }} yang akan dicetak!');
        return;
    }
    
    showNotification('info', `Mencetak ${checkedCount} {{ $itemName ?? "data" }}...`);
    
    $('.form-{{ $formClass ?? "data" }}')
        .attr('target', '_blank')
        .attr('action', url)
        .submit();
}

// Function untuk export data
function exportData(url) {
    const checkedCount = $('input[name="{{ $checkboxName ?? "id[]" }}"]:checked').length;
    
    if (checkedCount < 1) {
        showNotification('warning', 'Pilih {{ $itemName ?? "data" }} yang akan di export!');
        return;
    }
    
    showNotification('info', `Mengexport ${checkedCount} {{ $itemName ?? "data" }}...`);
    
    $('.form-{{ $formClass ?? "data" }}')
        .attr('target', '_blank')
        .attr('action', url)
        .submit();
}

// Function untuk export semua data
function exportAllData(url) {
    $('input[name="{{ $checkboxName ?? "id[]" }}"]').prop('checked', true);
    
    const totalCount = $('input[name="{{ $checkboxName ?? "id[]" }}"]').length;
    
    if (totalCount < 1) {
        showNotification('warning', 'Tidak ada data untuk di export!');
        return;
    }
    
    showNotification('info', `Mengexport semua {{ $itemName ?? "data" }} (${totalCount} data)...`);
    
    $('.form-{{ $formClass ?? "data" }}')
        .attr('target', '_blank')
        .attr('action', url)
        .submit();
}

// Handle select all checkbox
$('[name=select_all]').on('click', function () {
    $('input[name="{{ $checkboxName ?? "id[]" }}"]').prop('checked', this.checked);
    
    const checkedCount = $('input[name="{{ $checkboxName ?? "id[]" }}"]:checked').length;
    if (checkedCount > 0) {
        showNotification('info', `${checkedCount} {{ $itemName ?? "data" }} dipilih`);
    }
});

// Event listener untuk checkbox individual
$(document).on('change', 'input[name="{{ $checkboxName ?? "id[]" }}"]', function() {
    const checkedCount = $('input[name="{{ $checkboxName ?? "id[]" }}"]:checked').length;
    const totalCount = $('input[name="{{ $checkboxName ?? "id[]" }}"]').length;
    
    // Update select all checkbox
    if (checkedCount === 0) {
        $('[name=select_all]').prop('checked', false).prop('indeterminate', false);
    } else if (checkedCount === totalCount) {
        $('[name=select_all]').prop('checked', true).prop('indeterminate', false);
    } else {
        $('[name=select_all]').prop('checked', false).prop('indeterminate', true);
    }
    
    // Tampilkan notifikasi jika ada yang dipilih
    if (checkedCount > 0) {
        showNotification('info', `${checkedCount} {{ $itemName ?? "data" }} dipilih`);
    }
});
</script>

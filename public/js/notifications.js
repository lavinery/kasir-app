// Global Notification System menggunakan Alert Sederhana
// File ini berisi fungsi notifikasi yang bisa digunakan di semua halaman

// Function untuk menampilkan notifikasi sukses
function showSuccessNotification(message = 'Data berhasil disimpan!') {
    alert('Berhasil! ' + message);
}

// Function untuk menampilkan notifikasi error
function showErrorNotification(message = 'Terjadi kesalahan!') {
    alert('Error! ' + message);
}

// Function untuk menampilkan notifikasi warning
function showWarningNotification(message = 'Peringatan!') {
    alert('Peringatan! ' + message);
}

// Function untuk menampilkan notifikasi info
function showInfoNotification(message = 'Informasi') {
    alert('Info: ' + message);
}

// Function untuk konfirmasi delete yang lebih baik
function confirmDelete(message = 'Yakin ingin menghapus data ini?') {
    return confirm(message);
}

// Function untuk konfirmasi delete multiple
function confirmDeleteMultiple(count, itemName = 'item') {
    const message = count === 1 
        ? `Yakin ingin menghapus ${itemName} yang dipilih?` 
        : `Yakin ingin menghapus ${count} ${itemName} yang dipilih?`;
    
    return confirm(message);
}

// Function untuk handle AJAX response
function handleAjaxResponse(response, successMessage = null) {
    if (response.success) {
        showSuccessNotification(successMessage || response.message);
    } else {
        showErrorNotification(response.message || 'Terjadi kesalahan!');
    }
}

// Function untuk handle AJAX error
function handleAjaxError(xhr, defaultMessage = 'Terjadi kesalahan!') {
    let message = defaultMessage;
    if (xhr.responseJSON && xhr.responseJSON.message) {
        message = xhr.responseJSON.message;
    }
    showErrorNotification(message);
}

// Auto-initialize untuk form submit yang menggunakan AJAX
$(document).ready(function() {
    // Handle form submit dengan notifikasi
    $(document).on('submit', 'form[data-ajax="true"]', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const url = form.attr('action');
        const method = form.find('input[name="_method"]').val() || 'POST';
        
        $.ajax({
            url: url,
            method: method,
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    showSuccessNotification(response.message);
                    // Reload table jika ada
                    if (typeof table !== 'undefined' && table.ajax) {
                        table.ajax.reload();
                    }
                    // Close modal jika ada
                    $('.modal').modal('hide');
                } else {
                    showErrorNotification(response.message || 'Terjadi kesalahan!');
                }
            },
            error: function(xhr) {
                handleAjaxError(xhr, 'Tidak dapat menyimpan data');
            }
        });
    });
    
    // Handle delete button dengan notifikasi
    $(document).on('click', '[data-delete-url]', function(e) {
        e.preventDefault();
        
        const url = $(this).data('delete-url');
        const message = $(this).data('delete-message') || 'Yakin ingin menghapus data ini?';
        
        if (confirmDelete(message)) {
            $.ajax({
                url: url,
                method: 'DELETE',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showSuccessNotification(response.message);
                        // Reload table jika ada
                        if (typeof table !== 'undefined' && table.ajax) {
                            table.ajax.reload();
                        }
                    } else {
                        showErrorNotification(response.message || 'Terjadi kesalahan!');
                    }
                },
                error: function(xhr) {
                    handleAjaxError(xhr, 'Tidak dapat menghapus data');
                }
            });
        }
    });
});

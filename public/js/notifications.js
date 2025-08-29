// Global Notification System
// File ini berisi fungsi notifikasi yang bisa digunakan di semua halaman

// Function untuk menampilkan notifikasi
function showNotification(type, message) {
    // Hapus notifikasi yang sudah ada
    $('.custom-notification').remove();
    
    // Tentukan warna berdasarkan tipe
    let bgColor, icon;
    switch(type) {
        case 'success':
            bgColor = '#28a745';
            icon = '✓';
            break;
        case 'error':
            bgColor = '#dc3545';
            icon = '✗';
            break;
        case 'warning':
            bgColor = '#ffc107';
            icon = '⚠';
            break;
        case 'info':
            bgColor = '#17a2b8';
            icon = 'ℹ';
            break;
        default:
            bgColor = '#6c757d';
            icon = '•';
    }
    
    // Buat elemen notifikasi
    const notification = $(`
        <div class="custom-notification" style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${bgColor};
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            max-width: 400px;
            font-size: 14px;
            display: flex;
            align-items: center;
            animation: slideInRight 0.3s ease-out;
        ">
            <span style="margin-right: 10px; font-size: 18px;">${icon}</span>
            <span>${message}</span>
            <button onclick="$(this).parent().remove()" style="
                background: none;
                border: none;
                color: white;
                margin-left: 15px;
                font-size: 18px;
                cursor: pointer;
                opacity: 0.7;
            ">&times;</button>
        </div>
    `);
    
    // Tambahkan ke body
    $('body').append(notification);
    
    // Auto hide setelah 5 detik
    setTimeout(() => {
        notification.fadeOut(300, function() {
            $(this).remove();
        });
    }, 5000);
}

// Function untuk handle AJAX response
function handleAjaxResponse(response, successMessage = null) {
    if (response.success) {
        showNotification('success', successMessage || response.message);
    } else {
        showNotification('error', response.message || 'Terjadi kesalahan!');
    }
}

// Function untuk handle AJAX error
function handleAjaxError(xhr, defaultMessage = 'Terjadi kesalahan!') {
    let message = defaultMessage;
    if (xhr.responseJSON && xhr.responseJSON.message) {
        message = xhr.responseJSON.message;
    }
    showNotification('error', message);
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

// Tambahkan CSS animation jika belum ada
if (!$('#notification-styles').length) {
    $('<style id="notification-styles">')
        .html(`
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `)
        .appendTo('head');
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
                    showNotification('success', response.message);
                    // Reload table jika ada
                    if (typeof table !== 'undefined' && table.ajax) {
                        table.ajax.reload();
                    }
                    // Close modal jika ada
                    $('.modal').modal('hide');
                } else {
                    showNotification('error', response.message || 'Terjadi kesalahan!');
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
                        showNotification('success', response.message);
                        // Reload table jika ada
                        if (typeof table !== 'undefined' && table.ajax) {
                            table.ajax.reload();
                        }
                    } else {
                        showNotification('error', response.message || 'Terjadi kesalahan!');
                    }
                },
                error: function(xhr) {
                    handleAjaxError(xhr, 'Tidak dapat menghapus data');
                }
            });
        }
    });
});

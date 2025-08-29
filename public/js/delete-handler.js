/**
 * Global Delete Handler untuk mencegah delete otomatis tanpa konfirmasi
 * File ini harus di-include di semua halaman yang memiliki tombol delete
 */

// Override semua fungsi deleteData yang ada
window.deleteData = function(url) {
    // Hentikan event propagation untuk mencegah delete otomatis
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    // Tampilkan konfirmasi
    if (confirm('Yakin ingin menghapus data ini?')) {
        $.post(url, { 
            '_token': $('meta[name="csrf-token"]').attr('content'),
            '_method': 'delete' 
        })
        .done((response) => {
            // Reload table jika ada
            if (typeof table !== 'undefined' && table.ajax) {
                table.ajax.reload(null, false);
            }
            
            if (response.success) {
                alert(response.message);
            } else {
                alert('Data berhasil dihapus!');
            }
        })
        .fail((xhr) => {
            let message = 'Tidak dapat menghapus data';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            alert(message);
        });
    }
};

// Override fungsi deleteSelected untuk bulk delete
window.deleteSelected = function(url) {
    // Hentikan event propagation untuk mencegah delete otomatis
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    // Cek checkbox yang dipilih
    const checkedCount = $('input[name*="id"]:checked, input[name*="id_produk"]:checked, input[name*="id_kategori"]:checked, input[name*="id_member"]:checked, input[name*="id_supplier"]:checked, input[name*="id_user"]:checked, input[name*="id_pengeluaran"]:checked').length;
    
    if (checkedCount === 0) {
        alert('Pilih data yang akan dihapus!');
        return;
    }
    
    const message = checkedCount === 1 
        ? 'Yakin ingin menghapus data yang dipilih?' 
        : `Yakin ingin menghapus ${checkedCount} data yang dipilih?`;
    
    if (confirm(message)) {
        // Serialize form data
        let formData = $('.form-data, .form-produk, .form-kategori, .form-member, .form-supplier, .form-user, .form-pengeluaran').serialize();
        
        $.post(url, formData)
            .done((response) => {
                // Reload table jika ada
                if (typeof table !== 'undefined' && table.ajax) {
                    table.ajax.reload(null, false);
                }
                
                if (response.success) {
                    alert(response.message);
                } else {
                    alert('Data berhasil dihapus!');
                }
            })
            .fail((xhr) => {
                let message = 'Tidak dapat menghapus data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                alert(message);
            });
    }
};

// Override fungsi deleteItem untuk barang habis
window.deleteItem = function(id) {
    // Hentikan event propagation untuk mencegah delete otomatis
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    if (confirm('Yakin ingin menghapus item ini dari daftar barang habis?')) {
        $.ajax({
            url: '/barang-habis/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    if (typeof table !== 'undefined' && table.ajax) {
                        table.ajax.reload();
                    }
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
            }
        });
    }
};

// Override fungsi bulkDelete untuk barang habis
window.bulkDelete = function() {
    // Hentikan event propagation untuk mencegah delete otomatis
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const selectedItems = [];
    $('.item-checkbox:checked').each(function() {
        selectedItems.push($(this).val());
    });
    
    if (selectedItems.length === 0) {
        alert('Tidak ada item yang dipilih');
        return;
    }
    
    if (confirm(`Yakin ingin menghapus ${selectedItems.length} item terpilih dari daftar barang habis?`)) {
        $.ajax({
            url: '/barang-habis/bulk-destroy',
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                ids: selectedItems
            },
            success: function(response) {
                if (response.success) {
                    if (typeof table !== 'undefined' && table.ajax) {
                        table.ajax.reload();
                    }
                    alert(response.message);
                    selectedItems.length = 0;
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
            }
        });
    }
};

// Document ready untuk memastikan semua tombol delete menggunakan handler yang benar
$(document).ready(function() {
    // Override semua onclick delete yang ada
    $(document).on('click', '[onclick*="deleteData"], [onclick*="deleteSelected"], [onclick*="deleteItem"], [onclick*="bulkDelete"]', function(e) {
        // Biarkan event handler asli berjalan, tapi pastikan event propagation dihentikan
        e.stopPropagation();
    });
    
    // Tambahkan event listener untuk tombol delete yang tidak menggunakan onclick
    $(document).on('click', '.btn-delete, .delete-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const url = $(this).data('url') || $(this).attr('href');
        if (url) {
            deleteData(url);
        }
    });
});

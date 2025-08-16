{{-- resources/views/barang_habis/action.blade.php --}}
<div class="btn-group">
    <button onclick="editItem({{ $item->id }}, '{{ addslashes($item->keterangan) }}')" 
            class="btn btn-primary btn-xs btn-flat" title="Edit Keterangan">
        <i class="fa fa-edit"></i>
    </button>
    <button onclick="deleteItem({{ $item->id }})" 
            class="btn btn-danger btn-xs btn-flat" title="Hapus dari Daftar">
        <i class="fa fa-trash"></i>
    </button>
</div>
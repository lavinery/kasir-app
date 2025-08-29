{{-- resources/views/barang_habis/action.blade.php --}}
<div class="btn-group">
    <button type="button" class="btn btn-xs btn-info" onclick="editItem({{ $item->id }}, '{{ addslashes($item->keterangan) }}')" title="Edit Keterangan">
        <i class="fa fa-edit"></i>
    </button>
    <button type="button" class="btn btn-xs btn-danger" onclick="deleteItem({{ $item->id }}, event)" title="Hapus dari Daftar">
        <i class="fa fa-trash"></i>
    </button>
</div>
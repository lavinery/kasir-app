<div class="modal fade" id="modal-member" tabindex="-1" role="dialog" aria-labelledby="modal-member">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Pilih Member</h4>
            </div>
            <div class="modal-body">
                <!-- Search Box -->
                <div class="form-group">
                    <input type="text" id="search-member" class="form-control" placeholder="Cari member...">
                </div>
                
                <table class="table table-striped table-bordered table-member">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Kode Member</th>
                            <th width="30%">Nama</th>
                            <th width="20%">Telepon</th>
                            <th width="20%">Alamat</th>
                            <th width="10%"><i class="fa fa-cog"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($member as $key => $item)
                            <tr>
                                <td width="5%">{{ $key+1 }}</td>
                                <td>{{ $item->kode_member }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->telepon }}</td>
                                <td>{{ $item->alamat }}</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-xs btn-flat"
                                        onclick="pilihMember('{{ $item->id_member }}', '{{ $item->kode_member }}')">
                                        <i class="fa fa-check-circle"></i>
                                        Pilih
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Simple search functionality
    $('#search-member').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('.table-member tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>
<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="" method="post" class="form-horizontal">
            @csrf
            @method('post')

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">
                        <i class="fa fa-id-card"></i> Form Member
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="nama" class="col-lg-2 col-lg-offset-1 control-label">
                            <i class="fa fa-user"></i> Nama <span class="text-red">*</span>
                        </label>
                        <div class="col-lg-6">
                            <input type="text" name="nama" id="nama" class="form-control" required autofocus
                                   placeholder="Masukkan nama lengkap member">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="telepon" class="col-lg-2 col-lg-offset-1 control-label">
                            <i class="fa fa-phone"></i> Telepon <span class="text-red">*</span>
                        </label>
                        <div class="col-lg-6">
                            <input type="text" name="telepon" id="telepon" class="form-control" required
                                   placeholder="Contoh: 08123456789">
                            <span class="help-block with-errors"></span>
                            <small class="text-muted">
                                <i class="fa fa-info-circle"></i> Nomor telepon untuk kontak dan notifikasi
                            </small>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="alamat" class="col-lg-2 col-lg-offset-1 control-label">
                            <i class="fa fa-map-marker"></i> Alamat
                        </label>
                        <div class="col-lg-6">
                            <textarea name="alamat" id="alamat" rows="3" class="form-control"
                                      placeholder="Masukkan alamat lengkap member (opsional)"></textarea>
                            <span class="help-block with-errors"></span>
                            <small class="text-muted">
                                <i class="fa fa-info-circle"></i> Alamat untuk pengiriman dan identifikasi
                            </small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-9 col-lg-offset-3">
                            <div class="alert alert-info">
                                <i class="fa fa-lightbulb-o"></i> <strong>Info:</strong>
                                <ul style="margin-bottom: 0; padding-left: 20px;">
                                    <li>Kode member akan dibuat otomatis setelah data disimpan</li>
                                    <li>Nama dan telepon wajib diisi</li>
                                    <li>Pastikan nomor telepon valid untuk kemudahan kontak</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-flat btn-primary">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal">
                        <i class="fa fa-arrow-circle-left"></i> Batal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
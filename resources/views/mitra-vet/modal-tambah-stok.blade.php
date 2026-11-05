<div class="modal fade" id="modal-tambah-stok">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Tambah Stok Barang</h4>
            </div>
            <div class="modal-body">
                <form id="form-tambah-stok">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" class="form-control" id="stok-nama-barang-display" readonly style="background:#f5f5f5;">
                        <input type="hidden" id="stok-item-id">
                    </div>
                    <div class="form-group">
                        <label>Stok Saat Ini</label>
                        <input type="text" class="form-control" id="stok-current" readonly style="background:#f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Jumlah Tambah Stok <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="stok-tambah-qty" placeholder="Masukkan jumlah stok yang ditambahkan" min="1">
                        <div id="err-stok-tambah-qty" class="validate-error"></div>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" class="form-control" id="stok-tambah-keterangan" placeholder="contoh: Restock dari supplier">
                    </div>
                    <div id="err-global-stok" class="validate-error"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnSimpanTambahStok">Tambah Stok</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-tambah-pembayaran">
	<div class="modal-dialog">
		<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <form class="form-pembarayan-tambah">
          <div class="box-body">
            <div class="form-group showDropdownCabang">
              <label for="selectedCabang">Cari Cabang</label>
              <select id="selectedCabang" class="form-control" style="width: 100%">
              </select>
              <div id="cabangErr1" class="validate-error"></div>
            </div>

            <div class="form-group showDropdownBarang">
              <label for="selectedBarang">Cari Barang</label>
              <select id="selectedBarang" class="form-control" style="width: 100%">
              </select>
            </div>

            <div class="form-group">
              <div class="table-responsive">
                <table class="table table-striped text-nowrap">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama Barang</th>
                      <th>Jumlah</th>
                      <th>Harga Satuan</th>
                      <th>Harga Keseluruhan</th>
                      <th>Hapus</th>
                    </tr>
                  </thead>
                  <tbody id="list-selected-barang">
                    <tr class="text-center"><td colspan="7">Tidak ada data.</td></tr>
                  </tbody>
                </table>
              </div>
              <div id="barangErr1" class="validate-error"></div>
            </div>
            <div class="form-group">
              <div id="beErr" class="validate-error"></div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button id="btnSubmitPembayaran" type="button" class="btn btn-primary pull-right m-r-10px" title="Simpan dan Unduh PDF">Simpan & Unduh</button>
      </div>
    </div>
  </div>
</div>

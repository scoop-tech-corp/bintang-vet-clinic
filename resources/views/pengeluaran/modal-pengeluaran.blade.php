<div class="modal fade" id="modal-pengeluaran">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<form class="form-daftar-barang">
					<div class="box-body">
						<div class="form-group">
							<label for="tanggal">Tanggal</label>
							<div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control" id="tanggal" placeholder="dd/mm/yyyy" autocomplete="off">
              </div>
							<div id="tanggalErr1" class="validate-error"></div>
						</div>
						<div class="form-group">
							<label for="Nama User">User Pembeli</label>
              <select id="selectedNamaUser" class="form-control" style="width: 100%"></select>
							<div id="namaUserErr1" class="validate-error"></div>
						</div>
						<div class="form-group">
							<label for="namaItem">Nama Item</label>
							<input id="namaItem" type="text" class="form-control" min="0" placeholder="Masukan Nama Item">
							<div id="namaItemErr1" class="validate-error"></div>
						</div>
            <div class="form-group">
							<label for="total">Jumlah</label>
							<input id="jumlah" type="number" class="form-control" min="0" placeholder="Masukan Jumlah">
							<div id="jumlahErr1" class="validate-error"></div>
						</div>
            <div class="form-group">
							<label for="nominal">Nominal</label>
							<input id="nominal" type="text" class="form-control" min="0" placeholder="Masukan Nominal">
							<div id="nominalErr1" class="validate-error"></div>
						</div>
            <div class="form-group btnSubmitToTableSection">
              <button type="button" class="btn btn-primary" id="btnSubmitToTable">Tambah</button>
            </div>

            <div class="form-group table-list-final-pengeluaran">
              <div class="table-responsive">
                <table class="table table-striped text-nowrap">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama Item</th>
                      <th>Nominal</th>
                      <th>Jumlah</th>
                      <th>Total</th>
                      <th>Hapus</th>
                    </tr>
                  </thead>
                  <tbody id="list-final-pengeluaran">
                    <tr class="text-center"><td colspan="6">Tidak ada data.</td></tr>
                  </tbody>
                </table>
              </div>
            </div>

						<div class="form-group">
							<div id="beErr" class="validate-error"></div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnSubmitPengeluaran">Simpan</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

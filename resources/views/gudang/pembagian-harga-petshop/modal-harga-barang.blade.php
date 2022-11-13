<div class="modal fade" id="modal-harga-barang">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<form class="form-harga-barang">
					<div class="box-body">

            <div class="form-group">
							<label for="cabang">Cabang</label>
							<select id="selectedCabangOnBarang" class="form-control" style="width: 100%">
							</select>
							<div id="cabangOnBarangErr1" class="validate-error"></div>
            </div>

						<div class="form-group">
							<label for="namaBarang">Nama Barang</label>
              <select id="selectedNamaBarang" class="form-control" style="width: 100%">
							</select>
							<div id="namaBarangErr1" class="validate-error"></div>
            </div>
            
            <table>
              <tr>
                <td class="detail-label">Jumlah Barang</td>
                <td id="jumlahBarangTxt" class="detail-value"></td>
              </tr>
            </table>

            <div class="form-group">
							<label for="hargaJualOnBarang">Harga Jual</label>
							<input id="hargaJualOnBarang" type="text" class="form-control" min="0" placeholder="Masukan Harga Jual">
							<div id="hargaJualOnBarangErr1" class="validate-error"></div>
						</div>

            <div class="form-group">
							<label for="hargaModalOnBarang">Harga Modal</label>
							<input id="hargaModalOnBarang" type="text" class="form-control" min="0" placeholder="Masukan Harga Modal">
							<div id="hargaModalOnBarangErr1" class="validate-error"></div>
						</div>

						<div class="form-group">
							<label for="keuntungan">Keuntungan</label>
							<div class="d-flex">Rp.&nbsp;<div id="label-keuntungan"></div></div>
							<div id="keuntunganErr1" class="validate-error"></div>
						</div>
            <div class="form-group">
							<div id="customErr1" class="validate-error"></div>
						</div>
						<div class="form-group">
							<div id="beErr" class="validate-error"></div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnSubmitHargaBarang">Simpan</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

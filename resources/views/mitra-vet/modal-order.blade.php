<div class="modal fade" id="modal-order" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Buat Orderan Baru</h4>
            </div>
            <div class="modal-body">
                <form id="form-order">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Pilih Pelanggan / Mitra <span class="text-danger">*</span></label>
                                <div style="display: flex; gap: 6px; align-items: center;">
                                    <div style="flex: 1; min-width: 0;">
                                        <select class="form-control" id="order-select-customer" style="width:100%;">
                                            <option value="">-- Cari & Pilih Pelanggan --</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-success" id="btnBukaModalCustomer" title="Tambah Pelanggan Baru" style="height: 34px;">
                                        <i class="fa fa-user-plus"></i>
                                    </button>
                                </div>
                                <div id="err-order-nama" class="validate-error"></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>No HP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="order-no-hp" placeholder="No HP otomatis terisi saat pilih pelanggan">
                                <div id="err-order-hp" class="validate-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Alamat Pengiriman <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="order-alamat" rows="2" placeholder="Alamat otomatis terisi saat pilih pelanggan"></textarea>
                        <div id="err-order-alamat" class="validate-error"></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Biaya Ongkir (Rp) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="order-ongkir" placeholder="contoh: 15000" value="0">
                                <div id="err-order-ongkir" class="validate-error"></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Catatan</label>
                                <input type="text" class="form-control" id="order-catatan" placeholder="Catatan opsional">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5><strong>Pilih Item Barang</strong></h5>

                    <div class="row" style="margin-bottom:10px;">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Barang</label>
                                <select id="order-select-item" class="form-control" style="width:100%"></select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Qty</label>
                                <input type="number" class="form-control" id="order-item-qty" min="1" value="1">
                            </div>
                        </div>
                        <div class="col-sm-3" style="display:flex; align-items:flex-end; padding-bottom:15px;">
                            <button type="button" class="btn btn-success btn-sm btn-block" id="btnAddItemToOrder">
                                <i class="fa fa-plus"></i> Tambah Item
                            </button>
                        </div>
                    </div>
                    <div id="err-order-items" class="validate-error" style="margin-bottom:8px;"></div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th>Qty</th>
                                    <th>Harga Satuan</th>
                                    <th>Subtotal</th>
                                    <th>Hapus</th>
                                </tr>
                            </thead>
                            <tbody id="list-order-items">
                                <tr class="text-center"><td colspan="6">Belum ada item dipilih.</td></tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Total Harga:</strong></td>
                                    <td><strong id="order-total-harga">Rp 0</strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Ongkir:</strong></td>
                                    <td><strong id="order-total-ongkir">Rp 0</strong></td>
                                    <td></td>
                                </tr>
                                <tr class="active">
                                    <td colspan="4" class="text-right"><strong>Grand Total:</strong></td>
                                    <td><strong id="order-grand-total">Rp 0</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="err-global-order" class="validate-error"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanOrder">
                    <i class="fa fa-save"></i> Simpan & Buat Nota
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Cepat Tambah Pelanggan Baru --}}
<div class="modal fade" id="modal-tambah-customer" style="z-index:1060;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-user-plus"></i> Tambah Pelanggan Baru</h4>
            </div>
            <div class="modal-body">
                <form id="form-tambah-customer">
                    <div class="form-group">
                        <label>Nama Pelanggan / Mitra <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cust-nama" placeholder="Masukkan nama lengkap">
                        <div id="err-cust-nama" class="validate-error"></div>
                    </div>
                    <div class="form-group">
                        <label>No. Handphone / WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cust-hp" placeholder="contoh: 08123456789">
                        <div id="err-cust-hp" class="validate-error"></div>
                    </div>
                    <div class="form-group">
                        <label>Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="cust-alamat" rows="2" placeholder="Masukkan alamat pengiriman"></textarea>
                        <div id="err-cust-alamat" class="validate-error"></div>
                    </div>
                    <div id="err-cust-global" class="validate-error"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnSimpanCustomerBaru">
                    <i class="fa fa-check"></i> Simpan & Pilih
                </button>
            </div>
        </div>
    </div>
</div>

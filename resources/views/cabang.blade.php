@extends('layout.master')

@section('content')
<div class="box box-info" id="cabang-app">
  <div class="box-header with-border">
    <h3 class="box-title">Cabang</h3>
    <button class="btn btn-info pull-right" @click="openFormAdd">Tambah</button>
  </div>
  <!-- /.box-header -->
  <!-- form start -->
  <div class="box-body">
    <table id="table-cabang" class="table table-hover table-bordered">
      <thead>
        <tr>
          <th>No</th>
          <th>Kode Cabang</th>
          <th>Cabang</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>AS</td>
          <td>Alam Sutera</td>
          <td>
            <button type="button" class="btn btn-warning">Ubah</button>
            <button type="button" class="btn btn-danger">Hapus</button>
          </td>
        </tr>
        <tr>
          <td>2</td>
          <td>TJ</td>
          <td>Tanjung Duren</td>
          <td>
            <button type="button" class="btn btn-warning">Ubah</button>
            <button type="button" class="btn btn-danger">Hapus</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <!-- /.box-body -->  

  <div class="modal fade" id="modal-cabang">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">@{{titleModal}}</h4>
        </div>
        <div class="modal-body">
          <form>
            <div class="box-body">
              <div class="form-group">
                <label for="kodeCabang">Kode Cabang</label>
                <input type="text" class="form-control" @keyup="kodeCabangKeyup" v-model="kodeCabang" placeholder="Masukan kode cabang">
                <div class="validate-error" v-if="kdCabangErr1">Kode Cabang harus di isi</div>
                <div class="validate-error" v-if="kdCabangErr2">Kode Cabang harus huruf besar dan tidak ada spasi</div>
              </div>
              <div class="form-group">
                <label for="cabang">Cabang</label>
                <input type="text" class="form-control" @keyup="namaCabangKeyup" v-model="namaCabang" placeholder="Masukan nama cabang">
                <div class="validate-error" v-if="namaCabangErr">Nama cabang harus di isi</div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" :disabled="validateSimpanCabang">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

</div>
@endsection

@section('script-content')
<script>
  $('#table-cabang').DataTable({
    'paging'      : false,
    'searching'   : false,
    'ordering'    : true,
  });
</script>
@endsection

@section('vue-content')
  <script src="{{ asset('main/js/cabang/cabang-vue.js') }}"></script>
@endsection

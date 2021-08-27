@extends('layout.master')

@section('content')
<div class="box box-info" id="daftar-barang-app">
  <div class="box-header ">
    <h3 class="box-title">Daftar Barang</h3>
    <div class="inner-box-title">
      <div class="section-left-box-title"></div>
      <div class="section-right-box-title">
        <div class="input-search-section m-r-10px">
          <input type="text" class="form-control" placeholder="cari..">
          <i class="fa fa-search" aria-hidden="true"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="box-body">
    <div class="table-responsive">
      <table class="table table-striped text-nowrap">
        <thead>
          <tr>
            <th>No</th>
            <th class="onOrdering" data='item_name' orderby="none">Nama Barang <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='total_item' orderby="none">Jumlah <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='unit_name' orderby="none">Satuan <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='category_name' orderby="none">Kategori <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='branch_name' orderby="none">Cabang <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='created_by' orderby="none">Dibuat Oleh <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='created_at' orderby="none">Tanggal dibuat <span class="fa fa-sort"></span></th>
            <th class="columnAction">Aksi</th>
          </tr>
        </thead>
        <tbody id="list-daftar-barang">
          <tr class="text-center"><td colspan="9">Tidak ada data.</td></tr>
        </tbody>
      </table>
    </div>
  </div>
  <!-- /.box-body -->

  @component('gudang.daftar-barang.modal-daftar-barang') @endcomponent

  @component('gudang.daftar-barang.upload-daftar-barang') @endcomponent

  @component('layout.modal-confirmation') @endcomponent

  @component('layout.message-box') @endcomponent
</div>
@endsection
@section('css-content')
  <link rel="stylesheet" type='text/css' href="{{ asset('main/css/daftar-barang.css') }}">
@endsection
@section('script-content')
  <script src="{{ asset('plugins/jquery.ui.widget.js') }}"></script>
  <script src="{{ asset('plugins/jquery.iframe-transport.js') }}"></script>
  <script src="{{ asset('plugins/jquery.fileupload.js') }}"></script>
  <script src="{{ asset('plugins/jquery.fileupload-ui.js') }}"></script>
  <script src="{{ asset('plugins/jquery.fileupload-process.js') }}"></script>
  <script src="{{ asset('plugins/jquery.fileupload-validate.js') }}"></script>
  <script src="{{ asset('main/js/gudang/daftar-barang/daftar-barang.js') }}"></script>
@endsection
@section('vue-content')@endsection

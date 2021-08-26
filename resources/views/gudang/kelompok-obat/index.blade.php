@extends('layout.master')

@section('content')
<div class="box box-info" id="kelompok-obat-app">
  <div class="box-header ">
    <h3 class="box-title">Kelompok Obat</h3>
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
            <th class="onOrdering" data='group_name' orderby="none">Nama Kelompok <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='branch_name' orderby="none">Cabang <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='created_by' orderby="none">Dibuat Oleh <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='created_at' orderby="none">Tanggal dibuat <span class="fa fa-sort"></span></th>
            <th class="columnAction">Aksi</th>
          </tr>
        </thead>
        <tbody id="list-kelompok-obat">
          <tr class="text-center"><td colspan="6">Tidak ada data.</td></tr>
        </tbody>
      </table>
    </div>
  </div>
  <!-- /.box-body -->

  @component('gudang.kelompok-obat.modal-kelompok-obat') @endcomponent
  @component('gudang.kelompok-obat.upload-kelompok-obat') @endcomponent
  @component('layout.modal-confirmation') @endcomponent
  @component('layout.message-box') @endcomponent
</div>
@endsection
@section('css-content')
  <link rel="stylesheet" type='text/css' href="{{ asset('main/css/kelompok-obat.css') }}">
@endsection
@section('script-content')
  <script src="{{ asset('plugins/jquery.ui.widget.js') }}"></script>
  <script src="{{ asset('plugins/jquery.iframe-transport.js') }}"></script>
  <script src="{{ asset('plugins/jquery.fileupload.js') }}"></script>
  <script src="{{ asset('plugins/jquery.fileupload-ui.js') }}"></script>
  <script src="{{ asset('plugins/jquery.fileupload-process.js') }}"></script>
  <script src="{{ asset('plugins/jquery.fileupload-validate.js') }}"></script>
  <script src="{{ asset('main/js/gudang/kelompok-obat/kelompok-obat.js') }}"></script>  
@endsection
@section('vue-content')@endsection

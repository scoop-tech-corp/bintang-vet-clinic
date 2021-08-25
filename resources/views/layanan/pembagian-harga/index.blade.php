@extends('layout.master')

@section('content')
<div class="box box-info" id="pembagian-harga-jasa-app">
  <div class="box-header ">
    <h3 class="box-title">Harga Jasa</h3>
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
        <thead style="border-top: 1px solid #f4f4f4;">
          <tr>
            <th>No</th>
            <th class="onOrdering" data='service_name' orderby="none">Jenis Pelayanan <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='category_name' orderby="none">Kategori Jasa <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='branch_name' orderby="none">Cabang <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='selling_price' orderby="none">Harga Jual <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='capital_price' orderby="none">Harga Modal <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='doctor_fee' orderby="none">Fee Dokter <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='petshop_fee' orderby="none">Fee Petshop <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='created_by' orderby="none">Dibuat Oleh <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='created_at' orderby="none">Tanggal dibuat <span class="fa fa-sort"></span></th>
            <th class="columnAction">Aksi</th>
          </tr>
        </thead>
        <tbody id="list-harga-jasa">
          <tr class="text-center"><td colspan="11">Tidak ada data.</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  @component('layanan.pembagian-harga.modal-harga-jasa') @endcomponent
  @component('layout.modal-confirmation') @endcomponent
  @component('layout.message-box') @endcomponent
</div>
@endsection
@section('script-content')
  <script src="{{ asset('plugins/jquery.mask.js') }}"></script>
  <script src="{{ asset('main/js/layanan/pembagian-harga/harga-jasa.js') }}"></script>
@endsection
@section('vue-content') @endsection
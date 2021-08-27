@extends('layout.master')

@section('content')

<div class="box box-info" id="pembagian-harga-kelompok-obat-app">
  <div class="box-header ">
    <h3 class="box-title">Pembagian Harga Kelompok Obat</h3>
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
            <th class="onOrdering" data='group_name' orderby="none">Kelompok Obat <span class="fa fa-sort"></span></th>
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
        <tbody id="list-harga-kelompok-obat">
          <tr class="text-center"><td colspan="10">Tidak ada data.</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  @component('gudang.pembagian-harga-kelompok-obat.modal-harga-kelompok-obat') @endcomponent
  @component('layout.modal-confirmation') @endcomponent
  @component('layout.message-box') @endcomponent
@endsection
@section('css-content')
  <link rel="stylesheet" type='text/css' href="{{ asset('main/css/harga-kelompok-obat.css') }}">
@endsection
@section('script-content')
  <script src="{{ asset('plugins/jquery.mask.js') }}"></script>
  <script src="{{ asset('main/js/gudang/pembagian-harga-kelompok-obat/harga-kelompok-obat.js') }}"></script>
@endsection
@section('vue-content') @endsection
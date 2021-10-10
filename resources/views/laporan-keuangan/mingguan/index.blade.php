@extends('layout.master')

@section('content')
<div class="box box-info" id="laporan-keuangan-Mingguan-app">
  <div class="box-header">
    <h3 class="box-title">Laporan Keuangan Mingguan</h3>
    <div class="inner-box-title">
      <div class="section-left-box-title">
        <label class="label-date">Pilih Tanggal</label>
        <div class="input-group date section-daterangepicker">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control" id="datepicker" placeholder="yyyy-mm-dd - yyyy-mm-dd" autocomplete="off">
        </div>

      </div>
      <div class="section-right-box-title">

        <button type="button" class="btn btn-success btn-download-excel" title="Download Excel">
          <i class="fa fa-file-excel-o" aria-hidden="true"></i>&nbsp;&nbsp;Download Excel
        </button>
        <select id="filterCabang" style="width: 100%"></select>
      </div>
    </div>
  </div>

  <div class="box-body">
    <div class="table-responsive">
      <table class="table table-striped text-nowrap">
        <thead>
          <tr>
            <th>No</th>
            <th class="onOrdering" data='registration_number' orderby="none">No. Registrasi <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='created_at' orderby="none">Tanggal dibuat <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='patient_number' orderby="none">No. Pasien <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='pet_category' orderby="none">Jenis Hewan <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='pet_name' orderby="none">Nama Hewan <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='complaint' orderby="none">Keluhan <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='status_outpatient_inpatient' orderby="none">Perawatan <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='price_overall' orderby="none">Total Keseluruhan <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='capital_price' orderby="none">Harga Modal Keseluruhan <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='doctor_fee' orderby="none">Fee Dokter Keseluruhan <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='petshop_fee' orderby="none">Fee Petshop Keseluruhan <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='created_by' orderby="none">Dibuat Oleh <span class="fa fa-sort"></span></th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="list-laporan-keuangan-mingguan"></tbody>
      </table>
    </div>
    <div class="m-b-10px m-t-10px">
      <label class="label-support-laporan">Total Keseluruhan</label>
      <span id="total-keseluruhan-txt">Rp. -</span>
    </div>
    <div class="m-b-10px m-t-10px">
      <label class="label-support-laporan">Harga Modal</label>
      <span id="harga-modal-txt">Rp. -</span>
    </div>
    <div class="m-b-10px m-t-10px">
      <label class="label-support-laporan">Fee Dokter</label>
      <span id="fee-dokter-txt">Rp. -</span>
    </div>
    <div class="m-b-10px m-t-10px">
      <label class="label-support-laporan">Fee Petshop</label>
      <span id="fee-petshop-txt">Rp. -</span>
    </div>
  </div>
</div>

@component('layout.message-box') @endcomponent

@endsection
@section('script-content')
  <script src="{{ asset('main/js/laporan-keuangan/mingguan/mingguan.js') }}"></script>
  <script src="{{ asset('bower_components/moment/min/moment.min.js') }}"></script>
  <script src="{{ asset('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
@endsection
@section('css-content')
  <link rel="stylesheet" type='text/css' href="{{ asset('main/css/laporan-keuangan.css') }}">
  <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
  {{-- <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}"> --}}
@endsection
@section('vue-content')@endsection

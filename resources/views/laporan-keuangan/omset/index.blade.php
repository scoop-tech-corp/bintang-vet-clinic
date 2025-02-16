@extends('layout.master')

@section('content')
<div class="box box-info" id="laporan-keuangan-omset-app">
  <div class="box-header">
    <h3 class="box-title">Rekapitulasi Omset</h3>
    <div class="inner-box-title">

      <div class="section-left-box-title">
        <label class="label-date">Pilih Tanggal</label>
        <div class="input-group date section-daterangepicker">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control" id="datepicker" placeholder="yyyy-mm-dd - yyyy-mm-dd"
            autocomplete="off">
        </div>

      </div>

      <div class="section-right-box-title">
        <select id="filterCabang" class="filter-branch"></select>
      </div>
    </div>
  </div>

  <div class="box-body">

    <div class="box-header with-border">
      <div style="display: flex; justify-content: space-between">

      </div>
    </div>
    <div class="box-body">
      <div id="rekapWidgetOmset" style="width:100%; height:100%"></div>
    </div>

  </div>

  <div class="box-body">
    <div class="table-responsive">
      <table class="table table-striped text-nowrap">
      <thead id="head-laporan-keuangan-omset"></thead>
        <!-- <thead>
          <tr>
            <th>No</th>
            <th class="onOrdering" data='dates' orderby="none">Periode <span
                class="fa fa-sort"></span></th>
            <th class="onOrdering" data='total_omset' orderby="none">Total Omset (Rp) <span
                class="fa fa-sort"></span></th>
          </tr>
        </thead> -->
        <tbody id="list-laporan-keuangan-omset"></tbody>
      </table>

      <ul class="pagination pagination-sm m-t-10px pull-left"></ul>
    </div>
  </div>
</div>

@component('layout.message-box')
@endcomponent
@endsection
@section('script-content')
<script src="{{ asset('plugins/highcharts/highstock.js') }}"></script>
<script src="{{ asset('main/js/laporan-keuangan/omset/omset.js') }}"></script>
<script src="{{ asset('bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
@endsection
@section('css-content')
<link rel="stylesheet" type='text/css' href="{{ asset('main/css/rekap.css') }}">
<link rel="stylesheet" href="{{ asset('bower_components/bootstrap-daterangepicker/daterangepicker.css') }}"
  @endsection
  @section('vue-content')
  @endsection

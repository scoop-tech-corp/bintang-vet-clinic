@extends('layout.master')

@section('content')
<div class="box box-info" id="penggajian-app">
  <div class="box-header ">
    <h3 class="box-title">Penggajian</h3>
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
            <th class="onOrdering columnNamaUser" data='fullname' orderby="none">Nama User <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='date_payed' orderby="none">Tanggal Penggajian <span class="fa fa-sort"></span></th>
            <th class="onOrdering columnCabang" data='branch_name' orderby="none">Cabang <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='basic_salary' orderby="none">Pokok <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='accomodation' orderby="none">Akomodasi <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='total_turnover' orderby="none">Omset <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='total_inpatient' orderby="none">Inap <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='total_surgery' orderby="none">Operasi <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='total_overall' orderby="none">Total Keseluruhan <span class="fa fa-sort"></span></th>
            <th class="columnAction">Aksi</th>
          </tr>
        </thead>
        <tbody id="list-penggajian">
          <tr class="text-center"><td colspan="11">Tidak ada data.</td></tr>
        </tbody>
      </table>
    </div>
  </div>
  <!-- /.box-body -->

  @component('penggajian.modal-penggajian') @endcomponent
  @component('penggajian.modal-detail-penggajian') @endcomponent
  @component('layout.modal-confirmation') @endcomponent
  @component('layout.message-box') @endcomponent
</div>
@endsection
@section('css-content')
  <link rel="stylesheet" type='text/css' href="{{ asset('main/css/penggajian.css') }}">
  <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection
@section('script-content')
  <script src="{{ asset('main/js/penggajian/penggajian.js') }}"></script>
  <script src="{{ asset('plugins/jquery.mask.js') }}"></script>
  <script src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endsection
@section('vue-content')@endsection

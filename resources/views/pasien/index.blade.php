@extends('layout.master')

@section('content')
<div class="box box-info" id="pasien-app">
  <div class="box-header ">
    <h3 class="box-title">Pendaftaran Pasien</h3>
    <div class="inner-box-title">
      <div class="section-left-box-title">
        <button class="btn btn-info openFormAdd">Tambah</button>
      </div>
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
            <th class="onOrdering" data='id_member' orderby="none">No. Registrasi <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='created_at' orderby="none">Tanggal Daftar <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='pet_category' orderby="none">Jenis Hewan <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='pet_name' orderby="none">Nama Hewan <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='owner_name' orderby="none">Nama Pemilik <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='pet_gender' orderby="none">Jenis Kelamin <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='pet_year_age' orderby="none">Usia Hewan <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='branch_name' orderby="none">Cabang <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='created_by' orderby="none">Didaftarkan Oleh <span class="fa fa-sort"></span></th>
            <th class="columnAction">Aksi</th>
          </tr>
        </thead>
        <tbody id="list-pasien">
          <tr class="text-center"><td colspan="11">Tidak ada data.</td></tr>
        </tbody>
      </table>
    </div>
  </div>
  <!-- /.box-body -->

  @component('pasien.modal-pasien') @endcomponent

  @component('layout.modal-confirmation') @endcomponent

  @component('layout.message-box') @endcomponent

</div>
@endsection
@section('script-content')
  <script src="{{ asset('main/js/pasien/pasien.js') }}"></script>
@endsection
@section('css-content')
  <link rel="stylesheet" type='text/css' href="{{ asset('main/css/pasien.css') }}">
@endsection
@section('vue-content')@endsection

@extends('layout.master')

@section('content')
<div class="box box-info" id="metode-pembayaran-app">
  <div class="box-header ">
    <h3 class="box-title">Metode Pembayaran</h3>
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
            <th class="onOrdering" data='payment_name' orderby="none">Nama Pembayaran <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='created_by' orderby="none">Dibuat Oleh <span class="fa fa-sort"></span></th>
            <th class="onOrdering" data='created_at' orderby="none">Tanggal dibuat <span class="fa fa-sort"></span></th>
            <th class="columnAction">Aksi</th>
          </tr>
        </thead>
        <tbody id="list-metode-pembayaran">
          <tr class="text-center">
            <td colspan="5" >Tidak ada data.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <!-- /.box-body -->

  @component('layanan.metode-pembayaran.modal-metode-pembayaran') @endcomponent
  @component('layout.modal-confirmation') @endcomponent
  @component('layout.message-box') @endcomponent
</div>
@endsection
@section('script-content')
  <script src="{{ asset('main/js/layanan/metode-pembayaran/metode-pembayaran.js') }}"></script>  
@endsection
@section('vue-content')

@endsection

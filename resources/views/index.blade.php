@extends('layout.master')

@section('content')
  <div class="row">
    <div class="col-md-7 pasien">
      <div class="box box-info">
        <div class="box-header with-border">
          <div style="display: flex; justify-content: space-between">
            <div class="box-title">Jumlah pasien per cabang per bulan</div>
            <div class="box-tools">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control" id="datepicker-jumlah-pasien" placeholder="mm-yyyy" autocomplete="off">
              </div>
            </div>
          </div>
        </div>
        <div class="box-body">
          <div id="totalPasienWidget" style="width:100%; height:100%"></div>
        </div>
      </div>
    </div>


    <div class="col-md-5">
      <div class="col-md-12 kelamin">
        <div class="box box-info">
          <div class="box-header with-border">
            <div style="display: flex; justify-content: space-between">
              <div class="box-title">Jenis Kelamin</div>
              <div class="box-tools">
                {{-- <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control" id="datepicker-jumlah-pasien" placeholder="mm-yyyy" autocomplete="off">
                </div> --}}
              </div>
            </div>
          </div>
          <div class="box-body">
            <div id="jenisKelaminWidget" style="width:100%; height:100%"></div>
          </div>
        </div>
      </div>
      <div class="col-md-12 umur">
        <div class="box box-info">
          <div class="box-header with-border">
            <div style="display: flex; justify-content: space-between">
              <div class="box-title">Umur</div>
              <div class="box-tools">
                {{-- <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control" id="datepicker-jumlah-pasien" placeholder="mm-yyyy" autocomplete="off">
                </div> --}}
              </div>
            </div>
          </div>
          <div class="box-body">
            <div id="umurWidget" style="width:100%; height:100%"></div>
          </div>
        </div>
      </div>

    </div>

  </div>
@endsection

@section('script-content')
  <script src="{{ asset('plugins/highcharts/highstock.js') }}"></script>
  <script src="{{ asset('main/js/index.js') }}"></script>
  <script src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endsection
@section('css-content')
  <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@extends('layout.master')

@section('content')
<div class="row">

    <div class="col-md-12 daftar-barang-limit-expired">
        <div class="box box-info" id="daftar-barang-limit-expired-app">
            <div class="box-header ">
                <h3 class="box-title">Daftar barang limit dan expired</h3>
                <div class="nav-tabs-custom m-t-25px">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#clinic" data-toggle="tab">Klinik</a></li>
                        <li><a href="#pet_shop" data-toggle="tab">Pet Shop</a></li>
                    </ul>

                    <div id="tab-content" class="tab-content">

                        <div class="tab-pane fade in active" id="clinic">

                            <div class="inner-box-title">
                                <div class="section-left-box-title"></div>
                                <div class="section-right-box-title"></div>
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
                                                <th class="onOrdering" data='created_by' orderby="none">Dibuat oleh <span class="fa fa-sort"></span></th>
                                                <th class="onOrdering" data='created_at' orderby="none">Tanggal dibuat <span class="fa fa-sort"></span></th>
                                                <th class="onOrdering" data='expired_date' orderby="none">Tanggal kedaluwarsa <span class="fa fa-sort"></span></th>
                                            </tr>
                                        </thead>
                                        <tbody id="list-daftar-barang-limit">
                                            <tr class="text-center">
                                                <td colspan="9">Tidak ada data.</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <ul class="pagination pagination-sm m-t-10px pull-left"></ul>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pet_shop">
                            <div class="inner-box-title">
                                <div class="section-left-box-title"></div>
                                <div class="section-right-box-title-pet"></div>
                            </div>

                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-striped text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th class="onOrderingPet" data='item_name' orderby="none">Nama Barang <span class="fa fa-sort"></span></th>
                                                <th class="onOrderingPet" data='total_item' orderby="none">Jumlah <span class="fa fa-sort"></span></th>
                                                <th class="onOrderingPet" data='branch_name' orderby="none">Cabang <span class="fa fa-sort"></span></th>
                                                <th class="onOrderingPet" data='created_by' orderby="none">Dibuat oleh <span class="fa fa-sort"></span></th>
                                                <th class="onOrderingPet" data='created_at' orderby="none">Tanggal dibuat <span class="fa fa-sort"></span></th>
                                                <th class="onOrderingPet" data='expired_date' orderby="none">Tanggal kedaluwarsa <span class="fa fa-sort"></span></th>
                                            </tr>
                                        </thead>
                                        <tbody id="list-daftar-barang-limit-pet-shop">
                                            <tr class="text-center">
                                                <td colspan="9">Tidak ada data.</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <ul class="pagination pagination-sm m-t-10px pull-left"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
        </div>

        <div class="col-md-6 pasien">
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

        <div class="col-md-6 rawat-inap">
            <div class="box box-info">
                <div class="box-header with-border">
                    <div style="display: flex; justify-content: space-between">
                        <div class="box-title">Rawat Inap</div>
                        <div class="box-tools">
                            <div class="rawat-inap-datepicker">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control" id="datepicker-rawat-inap" placeholder="yyyy-mm-dd" autocomplete="off">
                                </div>
                            </div>
                            {{-- <div class="rawat-inap-filtercabang">
                <select id="filterCabangRawatInap" style="width: 100%"></select>
              </div> --}}
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div id="rawatInapWidget" style="width:100%; height:100%"></div>
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
    <link rel="stylesheet" type='text/css' href="{{ asset('main/css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    @endsection
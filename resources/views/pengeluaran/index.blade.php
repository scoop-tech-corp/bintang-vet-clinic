@extends('layout.master')

@section('content')
    <div class="box box-info" id="pengeluaran-app">
        <div class="box-header ">
            <h3 class="box-title">Pengeluaran</h3>
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
                            <th class="onOrdering" data='date_spend' orderby="none">Tanggal Pembelian <span
                                    class="fa fa-sort"></span></th>
                            <th class="onOrdering" data='fullname' orderby="none">Nama Pembeli <span
                                    class="fa fa-sort"></span></th>
                            <th class="onOrdering" data='item_name' orderby="none">Nama Item <span
                                    class="fa fa-sort"></span></th>
                            <th class="onOrdering" data='quantity' orderby="none">Jumlah <span
                                    class="fa fa-sort"></span></th>
                            <th class="onOrdering" data='amount' orderby="none">Nominal <span
                                    class="fa fa-sort"></span></th>
                            <th class="onOrdering" data='amount_overall' orderby="none">Total Keseluruhan <span
                                    class="fa fa-sort"></span></th>
                            <th class="onOrdering" data='created_by' orderby="none">Dibuat Oleh <span
                                    class="fa fa-sort"></span></th>
                            <th class="onOrdering" data='created_at' orderby="none">Tanggal Dibuat <span
                                    class="fa fa-sort"></span></th>
                            <th class="columnAction">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="list-pengeluaran">
                        <tr class="text-center">
                            <td colspan="10">Tidak ada data.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box-body -->

        @component('pengeluaran.modal-pengeluaran')
        @endcomponent
        @component('layout.modal-confirmation')
        @endcomponent
        @component('layout.message-box')
        @endcomponent
    </div>
@endsection
@section('css-content')
    <link rel="stylesheet" type='text/css' href="{{ asset('main/css/pengeluaran.css') }}">
    <link rel="stylesheet"
        href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection
@section('script-content')
    <script src="{{ asset('main/js/pengeluaran/pengeluaran.js') }}"></script>
    <script src="{{ asset('plugins/jquery.mask.js') }}"></script>
    <script src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endsection
@section('vue-content')
@endsection

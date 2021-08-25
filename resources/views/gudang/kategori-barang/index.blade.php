@extends('layout.master')

@section('content')
<div class="box box-info" id="kategori-barang-app">
  <div class="box-header ">
    <h3 class="box-title">Kategori Barang</h3>
    <div class="inner-box-title">
      <div class="section-left-box-title">
        <button class="btn btn-info" @click="openFormAdd">Tambah</button>
      </div>
      <div class="section-right-box-title">
        <div class="input-search-section">
          <input type="text" class="form-control" placeholder="cari.." v-model="searchTxt" @keydown.enter="onSearch">
          <i class="fa fa-search" aria-hidden="true" @click="onSearch"></i>
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
            <th @click="onOrdering('category_name')">Kategori
              <span v-if="columnStatus.category_name == 'desc'" class="fa fa-sort-desc"></span>
              <span v-if="columnStatus.category_name == 'asc'" class="fa fa-sort-asc"></span>
              <span v-if="columnStatus.category_name == 'none'" class="fa fa-sort"></span>
            </th>
            <th @click="onOrdering('created_by')">Dibuat Oleh
              <span v-if="columnStatus.created_by == 'desc'" class="fa fa-sort-desc"></span>
              <span v-if="columnStatus.created_by == 'asc'" class="fa fa-sort-asc"></span>
              <span v-if="columnStatus.created_by == 'none'" class="fa fa-sort"></span>
            </th>
            <th @click="onOrdering('created_at')">Tanggal dibuat
              <span v-if="columnStatus.created_at == 'desc'" class="fa fa-sort-desc"></span>
              <span v-if="columnStatus.created_at == 'asc'" class="fa fa-sort-asc"></span>
              <span v-if="columnStatus.created_at == 'none'" class="fa fa-sort"></span>
            </th>
            <th class="columnAction">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in listData">
            <td>@{{ index + 1 }}</td>
            <td>@{{ item.category_name }}</td>
            <td>@{{ item.created_by }}</td>
            <td>@{{ item.created_at }}</td>
            <td v-if="item.isRoleAccess">
              <button type="button" class="btn btn-warning" @click="openFormUpdate(item)"><i class="fa fa-pencil" aria-hidden="true"></i></button>
              <button type="button" class="btn btn-danger" @click="openFormDelete(item)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
            </td>
          </tr>
          <tr class="text-center" v-if="!listData.length">
            <td colspan="5">Tidak ada data.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <!-- /.box-body -->

  @component('gudang.kategori-barang.modal-kategori-barang') @endcomponent

  @component('layout.modal-confirmation') @endcomponent

  @component('layout.message-box') @endcomponent
</div>

@endsection
@section('script-content')
  
@endsection
@section('vue-content')
<script src="{{ asset('main/js/gudang/kategori-barang/kategori-barang-vue.js') }}"></script>
@endsection

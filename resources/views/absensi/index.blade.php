@extends('layout.master')

@section('css-content')
<style>
  .badge-hadir       { background-color: #00a65a; color: #fff; }
  .badge-terlambat   { background-color: #f39c12; color: #fff; }
  .badge-tidak_hadir { background-color: #dd4b39; color: #fff; }
  .foto-thumb { width: 40px; height: 40px; object-fit: cover; border-radius: 4px; cursor: pointer; }
  .filter-section { display: flex; flex-wrap: wrap; gap: 8px; align-items: flex-end; margin-bottom: 15px; }
  .filter-section .filter-item label { display: block; margin-bottom: 4px; font-weight: 600; }
  @media (max-width: 767px) {
    .filter-section .filter-item { width: calc(50% - 4px); }
    .filter-section .filter-item input,
    .filter-section .filter-item select { min-width: 0 !important; width: 100%; }
    .filter-section .filter-item:last-child { width: 100%; }
  }
</style>
@endsection

@section('content')
<div class="box box-info" id="absensi-app">
  <div class="box-header">
    <h3 class="box-title">Laporan Absensi</h3>
    <div class="inner-box-title">
      <div class="section-left-box-title"></div>
      <div class="section-right-box-title">
        <button v-if="isAdmin" type="button" class="btn btn-success btn-sm" @click="exportExcel">
          <i class="fa fa-file-excel-o"></i> Export Excel
        </button>
      </div>
    </div>
  </div>

  <div class="box-body">
    <div class="filter-section">

      <!-- Filter Dari — semua role -->
      <div class="filter-item">
        <label>Dari</label>
        <input type="date" class="form-control input-sm" v-model="filter.tanggal_dari" @change="loadAbsensi">
      </div>

      <!-- Filter Sampai — semua role -->
      <div class="filter-item">
        <label>Sampai</label>
        <input type="date" class="form-control input-sm" v-model="filter.tanggal_sampai" @change="loadAbsensi">
      </div>

      <!-- Filter Shift — hanya admin -->
      <div class="filter-item" v-if="isAdmin">
        <label>Shift</label>
        <select class="form-control input-sm" v-model="filter.shift_id" @change="loadAbsensi" style="min-width:160px;">
          <option value="">Semua Shift</option>
          <option v-for="s in listShift" :key="s.id" :value="s.id">@{{ s.nama_shift }}</option>
        </select>
      </div>

      <!-- Filter Status — semua role -->
      <div class="filter-item">
        <label>Status</label>
        <select class="form-control input-sm" v-model="filter.status" @change="loadAbsensi" style="min-width:140px;">
          <option value="">Semua Status</option>
          <option value="hadir">Hadir</option>
          <option value="terlambat">Terlambat</option>
          <option value="tidak_hadir">Tidak Hadir</option>
        </select>
      </div>

      <!-- Filter Cabang — hanya admin -->
      <div class="filter-item" v-if="isAdmin">
        <label>Cabang</label>
        <select class="form-control input-sm" v-model="filter.branch_id" @change="loadAbsensi" style="min-width:180px;">
          <option value="">Semua Cabang</option>
          <option v-for="c in listCabang" :key="c.id" :value="c.id">@{{ c.branch_name }}</option>
        </select>
      </div>

      <!-- Filter Nama — hanya admin -->
      <div class="filter-item" v-if="isAdmin">
        <label>Cari Nama</label>
        <input type="text" class="form-control input-sm" v-model="filter.keyword"
          @keyup.enter="loadAbsensi" placeholder="Nama karyawan..." style="min-width:160px;">
      </div>

      <div class="filter-item">
        <label>&nbsp;</label>
        <button class="btn btn-primary btn-sm" @click="loadAbsensi">
          <i class="fa fa-search"></i> Cari
        </button>
      </div>

    </div>

    <div class="table-responsive">
      <table class="table table-striped text-nowrap">
        <thead>
          <tr>
            <th>No</th>
            <th v-if="isAdmin">Nama</th>
            <th v-if="isAdmin">Cabang</th>
            <th>Shift</th>
            <th>Tanggal</th>
            <th>Jam Masuk</th>
            <th>Jam Pulang</th>
            <th>Jam Shift</th>
            <th>Foto Masuk</th>
            <th>Foto Pulang</th>
            <th>Lokasi</th>
            <th>Keterangan</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="listAbsensi.length === 0" class="text-center">
            <td :colspan="isAdmin ? 13 : 11">@{{ loading ? 'Memuat data...' : 'Tidak ada data.' }}</td>
          </tr>
          <tr v-for="(item, idx) in listAbsensi" :key="item.id">
            <td>@{{ idx + 1 }}</td>
            <td v-if="isAdmin">@{{ item.fullname }}</td>
            <td v-if="isAdmin">@{{ item.branch_name }}</td>
            <td>@{{ item.nama_shift }}</td>
            <td>@{{ item.tanggal }}</td>
            <td>@{{ item.jam_masuk || '-' }}</td>
            <td>@{{ item.jam_keluar || '-' }}</td>
            <td>@{{ item.shift_jam_masuk }} - @{{ item.shift_jam_keluar }}</td>
            <td>
              <img v-if="item.foto_masuk" :src="baseUrl + '/' + item.foto_masuk"
                class="foto-thumb" @click="lihatFoto(baseUrl + '/' + item.foto_masuk)" title="Foto Masuk">
              <span v-else>-</span>
            </td>
            <td>
              <img v-if="item.foto_keluar" :src="baseUrl + '/' + item.foto_keluar"
                class="foto-thumb" @click="lihatFoto(baseUrl + '/' + item.foto_keluar)" title="Foto Pulang">
              <span v-else>-</span>
            </td>
            <td style="max-width:200px; white-space:normal; font-size:12px;">
              <span v-if="item.alamat">@{{ item.alamat }}</span>
              <span v-else>-</span>
            </td>
            <td style="max-width:180px; white-space:normal; font-size:12px;">
              <span v-if="item.keterangan">@{{ item.keterangan }}</span>
              <span v-else>-</span>
            </td>
            <td>
              <span class="badge" :class="'badge-' + item.status">
                @{{ item.status === 'hadir' ? 'Hadir' : item.status === 'terlambat' ? 'Terlambat' : 'Tidak Hadir' }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Lihat Foto -->
  <div class="modal fade" id="modal-foto" tabindex="-1">
    <div class="modal-dialog" style="max-width:420px; width:95%;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Foto Absensi</h4>
        </div>
        <div class="modal-body text-center">
          <img :src="fotoPreview" style="max-width:100%; border-radius:6px;">
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@section('vue-content')
<script src="{{ asset('main/js/absensi/laporan-absensi-vue.js') }}"></script>
@endsection

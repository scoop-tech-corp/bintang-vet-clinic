<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['middleware' => ['api']], function () {

    Route::post('masuk', 'UserController@login');

    Route::group(['middleware' => ['jwt.auth']], function () {

        Route::post('keluar', 'UserController@logout');

        //cabang
        Route::get('cabang', 'CabangController@index');
        Route::post('cabang', 'CabangController@create');
        Route::put('cabang', 'CabangController@update');
        Route::delete('cabang', 'CabangController@delete');

        //user management
        Route::get('user', 'UserController@index');
        Route::post('user', 'UserController@register');
        Route::put('user', 'UserController@update');
        Route::delete('user', 'UserController@delete');

        Route::get('user/dokter', 'UserController@doctor');

        Route::post('user/upload-image', 'ProfileController@upload_photo_profile');
        Route::get('user/profile', 'ProfileController@get_data_user');
        Route::put('user/profile', 'ProfileController@update_data_user');

        //pasien
        Route::get('pasien', 'PasienController@index');
        Route::post('pasien', 'PasienController@create');
        Route::put('pasien', 'PasienController@update');
        Route::delete('pasien', 'PasienController@delete');
        Route::get('pasien/status-terima', 'PasienController@patient_accept_only');

        Route::get('pasien/daftar-pemilik', 'PasienController@ListOwner');

        //kategori barang
        Route::get('kategori-barang', 'KategoriBarangController@index');
        Route::post('kategori-barang', 'KategoriBarangController@create');
        Route::put('kategori-barang', 'KategoriBarangController@update');
        Route::delete('kategori-barang', 'KategoriBarangController@delete');

        //satuan barang
        Route::get('satuan-barang', 'SatuanBarangController@index');
        Route::post('satuan-barang', 'SatuanBarangController@create');
        Route::put('satuan-barang', 'SatuanBarangController@update');
        Route::delete('satuan-barang', 'SatuanBarangController@delete');

        //daftar barang
        Route::get('daftar-barang', 'DaftarBarangController@index');
        Route::post('daftar-barang', 'DaftarBarangController@create');
        Route::put('daftar-barang', 'DaftarBarangController@update');
        Route::delete('daftar-barang', 'DaftarBarangController@delete');

        Route::get('daftar-barang/generate-excel', 'DaftarBarangController@generate_excel');

        Route::get('daftar-barang/download-template', 'DaftarBarangController@download_template');
        Route::post('daftar-barang/upload', 'DaftarBarangController@upload_template');

        //kategori jasa
        Route::get('kategori-jasa', 'KategoriJasaController@index');
        Route::post('kategori-jasa', 'KategoriJasaController@create');
        Route::put('kategori-jasa', 'KategoriJasaController@update');
        Route::delete('kategori-jasa', 'KategoriJasaController@delete');

        //daftar jasa
        Route::get('daftar-jasa', 'DaftarJasaController@index');
        Route::post('daftar-jasa', 'DaftarJasaController@create');
        Route::put('daftar-jasa', 'DaftarJasaController@update');
        Route::delete('daftar-jasa', 'DaftarJasaController@delete');

        //pembagian harga jasa
        Route::get('pembagian-harga-jasa', 'HargaJasaController@index');
        Route::post('pembagian-harga-jasa', 'HargaJasaController@create');
        Route::put('pembagian-harga-jasa', 'HargaJasaController@update');
        Route::delete('pembagian-harga-jasa', 'HargaJasaController@delete');
        Route::get('pembagian-harga-jasa/kategori-jasa', 'HargaJasaController@service_category');
        Route::get('pembagian-harga-jasa/nama-jasa', 'HargaJasaController@service_name');

        //pembagian harga barang
        Route::get('pembagian-harga-barang', 'HargaBarangController@index');
        Route::post('pembagian-harga-barang', 'HargaBarangController@create');
        Route::put('pembagian-harga-barang', 'HargaBarangController@update');
        Route::delete('pembagian-harga-barang', 'HargaBarangController@delete');
        Route::get('pembagian-harga-barang/kategori-barang', 'HargaBarangController@item_category');
        Route::get('pembagian-harga-barang/nama-barang', 'HargaBarangController@item_name');

        Route::get('pembagian-harga-barang/generate-excel', 'HargaBarangController@generate_excel');
        Route::get('pembagian-harga-barang/download-template', 'HargaBarangController@download_template');
        Route::post('pembagian-harga-barang/upload', 'HargaBarangController@upload_template');

        //registrasi pasien
        Route::get('registrasi-pasien', 'RegistrasiController@index');
        Route::post('registrasi-pasien', 'RegistrasiController@create');
        Route::put('registrasi-pasien', 'RegistrasiController@update');
        Route::delete('registrasi-pasien', 'RegistrasiController@delete');

        //penerimaan pasien
        Route::get('penerimaan-pasien', 'PenerimaanPasienController@index');
        Route::get('penerimaan-pasien/terima', 'PenerimaanPasienController@accept');
        Route::get('penerimaan-pasien/tolak', 'PenerimaanPasienController@decline');

        //hasil pemeriksaan
        Route::post('hasil-pemeriksaan', 'HasilPemeriksaanController@create');
        Route::get('hasil-pemeriksaan', 'HasilPemeriksaanController@index');
        Route::get('hasil-pemeriksaan/detail', 'HasilPemeriksaanController@detail');
        Route::put('hasil-pemeriksaan', 'HasilPemeriksaanController@update');
        Route::delete('hasil-pemeriksaan', 'HasilPemeriksaanController@delete');

        //Route::post('hasil-pemeriksaan/upload-gambar', 'HasilPemeriksaanController@upload_images');

        Route::post('hasil-pemeriksaan/update-upload-gambar', 'HasilPemeriksaanController@update_upload_images');

        Route::get('hasil-pemeriksaan/pembayaran', 'HasilPemeriksaanController@payment');

        //pembayaran    DropDownPatient
        Route::get('pembayaran/pasien', 'PembayaranController@DropDownPatient');
        Route::get('pembayaran', 'PembayaranController@index');
        Route::post('pembayaran', 'PembayaranController@create');
        Route::put('pembayaran', 'PembayaranController@update');
        Route::get('pembayaran/detail', 'PembayaranController@detail');
        // Route::get('pembayaran/print', 'PembayaranController@print_pdf');
        //Route::post('pembayaran/printpdf', 'PembayaranController@print_pdf');

        Route::delete('pembayaran', 'PembayaranController@delete');

        //Route::delete('pembayaran/all', 'PembayaranController@delete_all');

        //riwayat pasien
        Route::get('pasien/riwayat', 'PasienController@HistoryPatient');
        Route::get('pasien/detail-riwayat', 'PasienController@DetailHistoryPatient');

        //kategori obat
        Route::get('kelompok-obat', 'KelompokObatController@index');
        Route::post('kelompok-obat', 'KelompokObatController@create');
        Route::put('kelompok-obat', 'KelompokObatController@update');
        Route::delete('kelompok-obat', 'KelompokObatController@delete');

        Route::get('kelompok-obat/download-template', 'KelompokObatController@download_template');
        Route::post('kelompok-obat/upload-template', 'KelompokObatController@upload_template');

        //harga kelompok obat
        Route::get('pembagian-harga-kelompok-obat', 'HargaKelompokObatController@index');
        Route::post('pembagian-harga-kelompok-obat', 'HargaKelompokObatController@create');
        Route::put('pembagian-harga-kelompok-obat', 'HargaKelompokObatController@update');
        Route::delete('pembagian-harga-kelompok-obat', 'HargaKelompokObatController@delete');

        Route::get('pembagian-harga-kelompok-obat/generate-excel', 'HargaKelompokObatController@generate_excel');
        Route::get('pembagian-harga-kelompok-obat/cabang-obat', 'HargaKelompokObatController@branch_medicine');

        //laporan keuangan

        //harian
        Route::get('laporan-keuangan/harian', 'LaporanKeuanganHarianController@index');
        Route::get('laporan-keuangan/harian/download', 'LaporanKeuanganHarianController@download_excel');

        Route::get('laporan-keuangan/detail', 'LaporanKeuanganHarianController@detail');

        //mingguan
        Route::get('laporan-keuangan/mingguan', 'LaporanKeuanganMingguanController@index');
        Route::get('laporan-keuangan/mingguan/download', 'LaporanKeuanganMingguanController@download_excel');

        Route::get('laporan-keuangan/mingguan/detail', 'LaporanKeuanganMingguanController@detail');

        //bulanan
        Route::get('laporan-keuangan/bulanan', 'LaporanKeuanganBulananController@index');
        Route::get('laporan-keuangan/bulanan/download', 'LaporanKeuanganBulananController@download_excel');

        Route::get('laporan-keuangan/bulanan/detail', 'LaporanKeuanganBulananController@detail');

        //dashboard
        Route::get('dashboard/barchart', 'DashboardController@BarChartPatient');
        Route::get('dashboard/barchart-inpatient', 'DashboardController@BarChartInPatient');

        //penggajian
        Route::get('penggajian/gaji-user', 'PenggajianController@sallary_user');

        Route::get('penggajian', 'PenggajianController@index');
        Route::post('penggajian', 'PenggajianController@create');
        Route::put('penggajian', 'PenggajianController@update');
        Route::delete('penggajian', 'PenggajianController@delete');

        Route::get('penggajian/generate', 'PenggajianController@generate');
    });
});

// Route::post('register', 'UserController@register');
// Route::post('login', 'UserController@login');
// Route::get('book', 'BookController@book');

// Route::get('bookall', 'BookController@bookAuth')->middleware('jwt.verify');
// Route::get('user', 'UserController@getAuthenticatedUser')->middleware('jwt.verify');

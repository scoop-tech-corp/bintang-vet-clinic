<?php

namespace App\Http\Controllers;

use App\Exports\MultipleSheetUploadHargaBarang;
use App\Exports\RekapHargaBarang;
use App\Imports\MultipleSheetImportHargaBarang;
use App\Models\Branch;
use App\Models\PriceItem;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class HargaBarangController extends Controller
{
    public function index(Request $request)
    {
        if ($request->keyword) {

            $res = $this->Search($request);

            $price_items = DB::table('price_items')
                ->join('users', 'price_items.user_id', '=', 'users.id')
                ->join('list_of_items', 'price_items.list_of_items_id', '=', 'list_of_items.id')
                ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
                ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
                ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
                ->select('price_items.id',
                    'list_of_items.id as item_name_id',
                    'list_of_items.item_name',
                    'category_item.id as item_categories_id',
                    'category_item.category_name',
                    'list_of_items.unit_item_id as unit_item_id',
                    'unit_item.unit_name',
                    'list_of_items.total_item',
                    'branches.id as branch_id',
                    'branches.branch_name',
                    DB::raw("TRIM(price_items.selling_price)+0 as selling_price"),
                    DB::raw("TRIM(price_items.capital_price)+0 as capital_price"),
                    DB::raw("TRIM(price_items.doctor_fee)+0 as doctor_fee"),
                    DB::raw("TRIM(price_items.petshop_fee)+0 as petshop_fee"),
                    'users.fullname as created_by',
                    DB::raw("DATE_FORMAT(price_items.created_at, '%d %b %Y') as created_at"))
                ->where('price_items.isDeleted', '=', 0);

            if ($res) {
                $price_items = $price_items->where($res, 'like', '%' . $request->keyword . '%');
            } else {
                $data = [];
                return response()->json($data, 200);
            }

            if ($request->branch_id && $request->user()->role == 'admin') {
                $price_items = $price_items->where('branches.id', '=', $request->branch_id);
            }

            if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
                $price_items = $price_items->where('branches.id', '=', $request->user()->branch_id);
            }

            if ($request->orderby) {
                $price_items = $price_items->orderBy($request->column, $request->orderby);
            }

            $price_items = $price_items->orderBy('price_items.id', 'desc');

            $price_items = $price_items->get();

            return response()->json($price_items, 200);
        } else {

            $price_items = DB::table('price_items')
                ->join('users', 'price_items.user_id', '=', 'users.id')
                ->join('list_of_items', 'price_items.list_of_items_id', '=', 'list_of_items.id')
                ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
                ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
                ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
                ->select('price_items.id',
                    'list_of_items.id as item_name_id',
                    'list_of_items.item_name',
                    'category_item.id as item_categories_id',
                    'category_item.category_name',
                    'list_of_items.unit_item_id as unit_item_id',
                    'unit_item.unit_name',
                    'list_of_items.total_item',
                    'branches.id as branch_id',
                    'branches.branch_name',
                    DB::raw("TRIM(price_items.selling_price)+0 as selling_price"),
                    DB::raw("TRIM(price_items.capital_price)+0 as capital_price"),
                    DB::raw("TRIM(price_items.doctor_fee)+0 as doctor_fee"),
                    DB::raw("TRIM(price_items.petshop_fee)+0 as petshop_fee"),
                    'users.fullname as created_by',
                    DB::raw("DATE_FORMAT(price_items.created_at, '%d %b %Y') as created_at"))
                ->where('price_items.isDeleted', '=', 0);

            if ($request->branch_id && $request->user()->role == 'admin') {
                $price_items = $price_items->where('branches.id', '=', $request->branch_id);
            }

            if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
                $price_items = $price_items->where('branches.id', '=', $request->user()->branch_id);
            }

            if ($request->orderby) {
                $price_items = $price_items->orderBy($request->column, $request->orderby);
            }

            $price_items = $price_items->orderBy('price_items.id', 'desc');

            $price_items = $price_items->get();

            return response()->json($price_items, 200);
        }

        if ($request->orderby) {
            $price_items = $price_items->orderBy($request->column, $request->orderby);
        }

        $price_items = $price_items->orderBy('price_items.id', 'desc');

        $price_items = $price_items->get();

        return response()->json($price_items, 200);
    }

    private function Search($request)
    {
        $temp_column = '';

        $price_items = DB::table('price_items')
            ->join('users', 'price_items.user_id', '=', 'users.id')
            ->join('list_of_items', 'price_items.list_of_items_id', '=', 'list_of_items.id')
            ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
            ->select(
                'list_of_items.item_name',
                'category_item.category_name',
                'unit_item.unit_name',
                'branches.branch_name',
                'users.fullname')
            ->where('price_items.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $price_items = $price_items->where('branches.id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $price_items = $price_items->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $price_items = $price_items->where('list_of_items.item_name', 'like', '%' . $request->keyword . '%');
        }

        $price_items = $price_items->get();

        if (count($price_items)) {
            $temp_column = 'list_of_items.item_name';
            return $temp_column;
        }
        //=======================================================

        $price_items = DB::table('price_items')
            ->join('users', 'price_items.user_id', '=', 'users.id')
            ->join('list_of_items', 'price_items.list_of_items_id', '=', 'list_of_items.id')
            ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
            ->select(
                'list_of_items.item_name',
                'category_item.category_name',
                'unit_item.unit_name',
                'branches.branch_name',
                'users.fullname')
            ->where('price_items.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $price_items = $price_items->where('branches.id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $price_items = $price_items->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $price_items = $price_items->where('category_item.category_name', 'like', '%' . $request->keyword . '%');
        }

        $price_items = $price_items->get();

        if (count($price_items)) {
            $temp_column = 'category_item.category_name';
            return $temp_column;
        }
        //=======================================================

        $price_items = DB::table('price_items')
            ->join('users', 'price_items.user_id', '=', 'users.id')
            ->join('list_of_items', 'price_items.list_of_items_id', '=', 'list_of_items.id')
            ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
            ->select(
                'list_of_items.item_name',
                'category_item.category_name',
                'unit_item.unit_name',
                'branches.branch_name',
                'users.fullname')
            ->where('price_items.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $price_items = $price_items->where('branches.id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $price_items = $price_items->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $price_items = $price_items->where('unit_item.unit_name', 'like', '%' . $request->keyword . '%');
        }

        $price_items = $price_items->get();

        if (count($price_items)) {
            $temp_column = 'unit_item.unit_name';
            return $temp_column;
        }
        //=======================================================

        $price_items = DB::table('price_items')
            ->join('users', 'price_items.user_id', '=', 'users.id')
            ->join('list_of_items', 'price_items.list_of_items_id', '=', 'list_of_items.id')
            ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
            ->select(
                'list_of_items.item_name',
                'category_item.category_name',
                'unit_item.unit_name',
                'branches.branch_name',
                'users.fullname')
            ->where('price_items.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $price_items = $price_items->where('branches.id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $price_items = $price_items->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $price_items = $price_items->where('branches.branch_name', 'like', '%' . $request->keyword . '%');
        }

        $price_items = $price_items->get();

        if (count($price_items)) {
            $temp_column = 'branches.branch_name';
            return $temp_column;
        }
        //=======================================================

        $price_items = DB::table('price_items')
            ->join('users', 'price_items.user_id', '=', 'users.id')
            ->join('list_of_items', 'price_items.list_of_items_id', '=', 'list_of_items.id')
            ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
            ->select(
                'list_of_items.item_name',
                'category_item.category_name',
                'unit_item.unit_name',
                'branches.branch_name',
                'users.fullname')
            ->where('price_items.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $price_items = $price_items->where('branches.id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $price_items = $price_items->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $price_items = $price_items->where('users.fullname', 'like', '%' . $request->keyword . '%');
        }

        $price_items = $price_items->get();

        if (count($price_items)) {
            $temp_column = 'users.fullname';
            return $temp_column;
        }
        //=======================================================
    }

    public function create(Request $request)
    {
        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'ListOfItemsId' => 'required|numeric',
            'HargaJual' => 'required|numeric|min:0',
            'HargaModal' => 'required|numeric|min:0',
            'FeeDokter' => 'required|numeric|min:0',
            'FeePetShop' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return response()->json([
                'message' => 'Data yang dimasukkan tidak valid!',
                'errors' => $errors,
            ], 422);
        }

        $check_list_item = DB::table('price_items')
            ->where('list_of_items_id', '=', $request->ListOfItemsId)
            ->where('isDeleted', '=', 0)
            ->count();

        if ($check_list_item > 0) {

            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data yang dimasukkan sudah ada!'],
            ], 422);
        }

        $item = PriceItem::create([
            'list_of_items_id' => $request->ListOfItemsId,
            'selling_price' => $request->HargaJual,
            'capital_price' => $request->HargaModal,
            'doctor_fee' => $request->FeeDokter,
            'petshop_fee' => $request->FeePetShop,
            'user_id' => $request->user()->id,
        ]);

        return response()->json(
            [
                'message' => 'Tambah Data Berhasil!',
            ], 200
        );
    }

    public function update(Request $request)
    {
        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'ListOfItemsId' => 'required|numeric',
            'HargaJual' => 'required|numeric|min:0',
            'HargaModal' => 'required|numeric|min:0',
            'FeeDokter' => 'required|numeric|min:0',
            'FeePetShop' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return response()->json([
                'message' => 'Data yang dimasukkan tidak valid!',
                'errors' => $errors,
            ], 422);
        }

        $price_items = PriceItem::find($request->id);

        if (is_null($price_items)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $check_list_item = DB::table('price_items')
            ->where('list_of_items_id', '=', $request->ListOfItemsId)
            ->where('id', '!=', $request->id)
            ->count();

        if ($check_list_item > 0) {

            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data sudah ada!'],
            ], 422);
        }

        $price_items->list_of_items_id = $request->ListOfItemsId;
        $price_items->selling_price = $request->HargaJual;
        $price_items->capital_price = $request->HargaModal;
        $price_items->doctor_fee = $request->FeeDokter;
        $price_items->petshop_fee = $request->FeePetShop;
        $price_items->user_update_id = $request->user()->id;
        $price_items->updated_at = \Carbon\Carbon::now();
        $price_items->save();

        return response()->json([
            'message' => 'Berhasil mengubah Data',
        ], 200);
    }

    public function delete(Request $request)
    {
        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $price_items = PriceItem::find($request->id);

        if (is_null($price_items)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $price_items->isDeleted = true;
        $price_items->deleted_by = $request->user()->fullname;
        $price_items->deleted_at = \Carbon\Carbon::now();
        $price_items->save();

        //$price_items->delete();

        return response()->json([
            'message' => 'Berhasil menghapus Data',
        ], 200);
    }

    public function item_category(Request $request)
    {
        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $list_of_item = DB::table('list_of_items')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->select('category_item_id', 'category_item.category_name')
            ->where('branch_id', '=', $request->branch_id)
            ->distinct('category_item_id')
            ->get();

        if (is_null($list_of_item)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        return response()->json($list_of_item, 200);
    }

    public function item_name(Request $request)
    {
        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $list_of_items = DB::table('list_of_items')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
            ->select('list_of_items.id', 'list_of_items.item_name', 'total_item', 'unit_item_id', 'unit_item.unit_name')
            ->where('branch_id', '=', $request->branch_id)
            ->where('category_item_id', '=', $request->category_item_id)
            ->get();

        if (is_null($list_of_items)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        return response()->json($list_of_items, 200);
    }

    public function download_template(Request $request)
    {
        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        return (new MultipleSheetUploadHargaBarang())->download('Template Harga Barang.xlsx');
    }

    public function upload_template(Request $request)
    {
        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $this->validate($request, [
            'file' => 'required|mimes:xls,xlsx',
        ]);

        $rows = Excel::toArray(new MultipleSheetImportHargaBarang, $request->file('file'));
        $result = $rows[0];

        foreach ($result as $key_result) {

            $check_duplicate = DB::table('price_items')
                ->where('list_of_items_id', '=', $key_result['kode_daftar_barang'])
                ->where('isDeleted', '=', 0)
                ->count();

            if ($check_duplicate > 0) {

                return response()->json([
                    'message' => 'The data was invalid.',
                    'errors' => ['Terdapat Data yang sudah ada!'],
                ], 422);
            }

            $count_total = $key_result['harga_modal'] + $key_result['fee_dokter'] + $key_result['fee_petshop'];

            if ($count_total != $key_result['harga_jual']) {

                return response()->json([
                    'message' => 'The data was invalid.',
                    'errors' => ['Jumlah Harga Modal, Fee Dokter, dan Fee Petshop harus sama dengan Harga Jual!'],
                ], 422);
            }
        }

        $file = $request->file('file');

        Excel::import(new MultipleSheetImportHargaBarang, $file);

        return response()->json([
            'message' => 'Berhasil mengupload Barang',
        ], 200);
    }

    public function generate_excel(Request $request)
    {
        $date = \Carbon\Carbon::now()->format('d-m-y');

        $branchId = "";

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $branchId = $request->user()->branch_id;
        } else {
            $branchId = $request->branch_id;
        }

        $listBranch = Branch::find($branchId);

        $filename = "";

        if ($listBranch) {
            $filename = 'Rekap Harga Barang Cabang ' . $listBranch->branch_name . ' ' . $date . '.xlsx';
        } else {
            $filename = 'Rekap Harga Barang ' . $date . '.xlsx';
        }

        return (new RekapHargaBarang($request->orderby, $request->column, $request->keyword, $branchId, $request->user()->role))
            ->download($filename);
    }

}

<?php

namespace App\Http\Controllers;

use App\Exports\MultipleSheetUploadDaftarBarang;
use App\Exports\RekapDaftarBarang;
use App\Imports\MultipleSheetImportDaftarBarang;
use App\Models\Branch;
use App\Models\HistoryItemMovement;
use App\Models\ListofItems;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class DaftarBarangController extends Controller
{
    public function index(Request $request)
    {
        if ($request->keyword) {

            $res = $this->Search($request);

            $item = DB::table('list_of_items')
                ->join('users', 'list_of_items.user_id', '=', 'users.id')
                ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
                ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
                ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
                ->select('list_of_items.id',
                    'list_of_items.item_name',
                    'list_of_items.total_item',
                    'unit_item.id as unit_item_id',
                    'unit_item.unit_name',
                    'category_item.id as category_item_id',
                    'category_item.category_name',
                    'branches.id as branch_id',
                    'branches.branch_name',
                    'users.id as user_id',
                    'users.fullname as created_by',
                    DB::raw("DATE_FORMAT(list_of_items.created_at, '%d %b %Y') as created_at"))
                ->where('list_of_items.isDeleted', '=', 0);

            if ($res) {
                $item = $item->where($res, 'like', '%' . $request->keyword . '%');
            } else {
                $data = [];
                return response()->json($data, 200);
            }

            if ($request->branch_id && $request->user()->role == 'admin') {
                $item = $item->where('list_of_items.branch_id', '=', $request->branch_id);
            }

            if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
                $item = $item->where('list_of_items.branch_id', '=', $request->user()->branch_id);
            }

            if ($request->orderby) {
                $item = $item->orderBy($request->column, $request->orderby);
            }

            $item = $item->orderBy('list_of_items.id', 'desc');

            $item = $item->get();

            return response()->json($item, 200);

        } else {

            $item = DB::table('list_of_items')
                ->join('users', 'list_of_items.user_id', '=', 'users.id')
                ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
                ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
                ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
                ->select('list_of_items.id',
                    'list_of_items.item_name',
                    'list_of_items.total_item',
                    'unit_item.id as unit_item_id',
                    'unit_item.unit_name',
                    'category_item.id as category_item_id',
                    'category_item.category_name',
                    'branches.id as branch_id',
                    'branches.branch_name',
                    'users.id as user_id',
                    'users.fullname as created_by',
                    DB::raw("DATE_FORMAT(list_of_items.created_at, '%d %b %Y') as created_at"))
                ->where('list_of_items.isDeleted', '=', 0);

            if ($request->branch_id && $request->user()->role == 'admin') {
                $item = $item->where('list_of_items.branch_id', '=', $request->branch_id);
            }

            if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
                $item = $item->where('list_of_items.branch_id', '=', $request->user()->branch_id);
            }

            if ($request->orderby) {
                $item = $item->orderBy($request->column, $request->orderby);
            }

            $item = $item->orderBy('list_of_items.id', 'desc');

            $item = $item->get();

            return response()->json($item, 200);
        }

    }

    private function Search($request)
    {
        $temp_column = '';

        $item = DB::table('list_of_items')
            ->join('users', 'list_of_items.user_id', '=', 'users.id')
            ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
            ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->select(
                'list_of_items.item_name',
                'unit_item.unit_name',
                'category_item.category_name',
                'branches.branch_name',
                'users.fullname as created_by')
            ->where('list_of_items.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $item = $item->where('list_of_items.branch_id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $item = $item->where('list_of_items.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $item = $item->where('list_of_items.item_name', 'like', '%' . $request->keyword . '%');
        }

        $item = $item->get();

        if (count($item)) {
            $temp_column = 'list_of_items.item_name';
            return $temp_column;
        }
        //=======================================================

        $item = DB::table('list_of_items')
            ->join('users', 'list_of_items.user_id', '=', 'users.id')
            ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
            ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->select(
                'list_of_items.item_name',
                'unit_item.unit_name',
                'category_item.category_name',
                'branches.branch_name',
                'users.fullname as created_by')
            ->where('list_of_items.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $item = $item->where('list_of_items.branch_id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $item = $item->where('list_of_items.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $item = $item->where('unit_item.unit_name', 'like', '%' . $request->keyword . '%');
        }

        $item = $item->get();

        if (count($item)) {
            $temp_column = 'unit_item.unit_name';
            return $temp_column;
        }
        //=======================================================

        $item = DB::table('list_of_items')
            ->join('users', 'list_of_items.user_id', '=', 'users.id')
            ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
            ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->select(
                'list_of_items.item_name',
                'unit_item.unit_name',
                'category_item.category_name',
                'branches.branch_name',
                'users.fullname as created_by')
            ->where('list_of_items.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $item = $item->where('list_of_items.branch_id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $item = $item->where('list_of_items.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $item = $item->where('category_item.category_name', 'like', '%' . $request->keyword . '%');
        }

        $item = $item->get();

        if (count($item)) {
            $temp_column = 'category_item.category_name';
            return $temp_column;
        }
        //=======================================================

        $item = DB::table('list_of_items')
            ->join('users', 'list_of_items.user_id', '=', 'users.id')
            ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
            ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->select(
                'list_of_items.item_name',
                'unit_item.unit_name',
                'category_item.category_name',
                'branches.branch_name',
                'users.fullname as created_by')
            ->where('list_of_items.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $item = $item->where('list_of_items.branch_id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $item = $item->where('list_of_items.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $item = $item->where('branches.branch_name', 'like', '%' . $request->keyword . '%');
        }

        $item = $item->get();

        if (count($item)) {
            $temp_column = 'branches.branch_name';
            return $temp_column;
        }
        //=======================================================

        $item = DB::table('list_of_items')
            ->join('users', 'list_of_items.user_id', '=', 'users.id')
            ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
            ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->select(
                'list_of_items.item_name',
                'unit_item.unit_name',
                'category_item.category_name',
                'branches.branch_name',
                'users.fullname as created_by')
            ->where('list_of_items.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $item = $item->where('list_of_items.branch_id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $item = $item->where('list_of_items.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $item = $item->where('users.fullname', 'like', '%' . $request->keyword . '%');
        }

        $item = $item->get();

        if (count($item)) {
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
            'nama_barang' => 'required|string|min:3|max:50',
            'jumlah_barang' => 'required|numeric|min:0',
            'satuan_barang' => 'required|string|max:50',
            'kategori_barang' => 'required|string|max:50',
            'cabang' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return response()->json([
                'message' => 'Barang yang dimasukkan tidak valid!',
                'errors' => $errors,
            ], 422);
        }

        $check_branch = DB::table('list_of_items')
            ->where('branch_id', '=', $request->cabang)
            ->where('item_name', '=', $request->nama_barang)
            ->count();

        if ($check_branch > 0) {

            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data sudah ada!'],
            ], 422);
        }

        $item = ListofItems::create([
            'item_name' => $request->nama_barang,
            'total_item' => $request->jumlah_barang,
            'unit_item_id' => $request->satuan_barang,
            'category_item_id' => $request->kategori_barang,
            'branch_id' => $request->cabang,
            'user_id' => $request->user()->id,
        ]);

        return response()->json(
            [
                'message' => 'Tambah Daftar Barang Berhasil!',
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
            'nama_barang' => 'required|string|min:3|max:50',
            'jumlah_barang' => 'required|numeric|min:0',
            'satuan_barang' => 'required|string|max:50',
            'kategori_barang' => 'required|string|max:50',
            'cabang' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return response()->json([
                'message' => 'Barang yang dimasukkan tidak valid!',
                'errors' => $errors,
            ], 422);
        }

        $item = ListofItems::find($request->id);

        if (is_null($item)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $find_duplicate = db::table('list_of_items')
            ->where('branch_id', '=', $request->cabang)
            ->where('item_name', '=', $request->nama_barang)
            ->where('id', '!=', $request->id)
            ->count();

        if ($find_duplicate != 0) {

            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data sudah ada!'],
            ], 422);

        }

        $check_stock = DB::table('list_of_items')
            ->select('total_item')
            ->where('id', '=', $request->id)
            ->first();

        if (is_null($check_stock)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data stock tidak ditemukan!'],
            ], 404);
        }

        if ($check_stock->total_item > $request->jumlah_barang) {
            $qty_item = $check_stock->total_item - $request->jumlah_barang;

            $item_history = HistoryItemMovement::create([
                'price_item_id' => $request->id,
                'item_id' => $request->id,
                'quantity' => $qty_item,
                'status' => 'tambah',
                'user_id' => $request->user()->id,
            ]);

        } elseif ($check_stock->total_item < $request->jumlah_barang) {
            $qty_item = $request->jumlah_barang - $check_stock->total_item;

            $item_history = HistoryItemMovement::create([
                'price_item_id' => $request->id,
                'item_id' => $request->id,
                'quantity' => $qty_item,
                'status' => 'kurang',
                'user_id' => $request->user()->id,
            ]);
        }

        $item->item_name = $request->nama_barang;
        $item->total_item = $request->jumlah_barang;
        $item->unit_item_id = $request->satuan_barang;
        $item->category_item_id = $request->kategori_barang;
        $item->branch_id = $request->cabang;
        $item->user_update_id = $request->user()->id;
        $item->updated_at = \Carbon\Carbon::now();
        $item->save();

        return response()->json([
            'message' => 'Berhasil mengubah Barang',
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

        $item = ListofItems::find($request->id);

        if (is_null($item)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $item->isDeleted = true;
        $item->deleted_by = $request->user()->fullname;
        $item->deleted_at = \Carbon\Carbon::now();
        $item->save();

        //$item->delete();

        return response()->json([
            'message' => 'Berhasil menghapus Barang',
        ], 200);
    }

    public function download_template(Request $request)
    {
        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        return (new MultipleSheetUploadDaftarBarang())->download('Template Daftar Barang.xlsx');
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

        $rows = Excel::toArray(new MultipleSheetImportDaftarBarang, $request->file('file'));
        $result = $rows[0];

        foreach ($result as $key_result) {

            $check_branch = DB::table('list_of_items')
                ->where('branch_id', '=', $key_result['kode_cabang_barang'])
                ->where('item_name', '=', $key_result['nama_barang'])
                ->count();

            if ($check_branch > 0) {

                return response()->json([
                    'message' => 'The data was invalid.',
                    'errors' => ['Data ' . $key_result['nama_barang'] . ' sudah ada!'],
                ], 422);
            }
        }

        $file = $request->file('file');

        Excel::import(new MultipleSheetImportDaftarBarang, $file);

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
            $filename = 'Rekap Daftar Barang Cabang ' . $listBranch->branch_name . ' ' . $date . '.xlsx';
        } else {
            $filename = 'Rekap Daftar Barang ' . $date . '.xlsx';
        }

        return (new RekapDaftarBarang($request->orderby, $request->column, $request->keyword, $branchId, $request->user()->role))
            ->download($filename);
    }
}

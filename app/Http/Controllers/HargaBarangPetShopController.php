<?php

namespace App\Http\Controllers;

use App\Exports\MultipleSheetUploadHargaBarangPetShop;
use App\Exports\RekapHargaBarangPetShop;
use App\Imports\MultipleSheetImportHargaBarangPetShop;
use App\Models\Branch;
use App\Models\PriceItemPetShop;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class HargaBarangPetShopController extends Controller
{
    public function index(Request $request)
    {
        $items_per_page = 50;

        $page = $request->page;

        $pi = DB::table('price_item_pet_shops as pi')
            ->join('users', 'pi.user_id', '=', 'users.id')
            ->join('list_of_item_pet_shops as ls', 'pi.list_of_item_pet_shop_id', '=', 'ls.id')
            ->join('branches', 'ls.branch_id', '=', 'branches.id')
            ->select('pi.id',
                'ls.id as list_of_item_pet_shop_id',
                'ls.item_name',
                'ls.total_item',
                DB::raw('DATE_FORMAT(ls.expired_date, "%d/%m/%Y") as expired_date'),
                'ls.limit_item',
                'branches.id as branch_id',
                'branches.branch_name',
                DB::raw("TRIM(pi.selling_price)+0 as selling_price"),
                DB::raw("TRIM(pi.capital_price)+0 as capital_price"),
                DB::raw("TRIM(pi.profit)+0 as profit"),
                'users.fullname as created_by',
                DB::raw("DATE_FORMAT(pi.created_at, '%d %b %Y') as created_at"))
            ->where('pi.isDeleted', '=', 0);

        if ($request->keyword) {

            $res = $this->Search($request);

            if ($res) {
                $pi = $pi->where($res, 'like', '%' . $request->keyword . '%');
            } else {
                $data = [];
                return response()->json(['total_paging' => 0,
                    'data' => $data], 200);
            }
        }

        if ($request->branch_id && $request->user()->role == 'admin') {
            $pi = $pi->where('branches.id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $pi = $pi->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->orderby) {
            $pi = $pi->orderBy($request->column, $request->orderby);
        }

        $pi = $pi->orderBy('pi.id', 'desc');

        $offset = ($page - 1) * $items_per_page;

        $count_data = $pi->count();
        $count_result = $count_data - $offset;

        if ($count_result < 0) {
            $pi = $pi->offset(0)->limit($items_per_page)->get();
        } else {
            $pi = $pi->offset($offset)->limit($items_per_page)->get();
        }

        $total_paging = $count_data / $items_per_page;

        return response()->json(['total_paging' => ceil($total_paging),
            'data' => $pi], 200);

    }

    private function Search($request)
    {
        $temp_column = '';

        $pi = DB::table('price_item_pet_shops as pi')
            ->join('users', 'pi.user_id', '=', 'users.id')
            ->join('list_of_item_pet_shops as ls', 'pi.list_of_item_pet_shop_id', '=', 'ls.id')
            ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
            ->select(
                'list_of_items.item_name',
                'branches.branch_name',
                'users.fullname')
            ->where('pi.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $pi = $pi->where('branches.id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $pi = $pi->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $pi = $pi->where('list_of_items.item_name', 'like', '%' . $request->keyword . '%');
        }

        $pi = $pi->get();

        if (count($pi)) {
            $temp_column = 'list_of_items.item_name';
            return $temp_column;
        }
        //=======================================================

        $pi = DB::table('price_item_pet_shops as pi')
            ->join('users', 'pi.user_id', '=', 'users.id')
            ->join('list_of_item_pet_shops as ls', 'pi.list_of_items_id', '=', 'ls.id')
            ->join('unit_item', 'ls.unit_item_id', '=', 'unit_item.id')
            ->join('category_item', 'ls.category_item_id', '=', 'category_item.id')
            ->join('branches', 'ls.branch_id', '=', 'branches.id')
            ->select(
                'list_of_items.item_name',
                'category_item.category_name',
                'unit_item.unit_name',
                'branches.branch_name',
                'users.fullname')
            ->where('pi.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $pi = $pi->where('branches.id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $pi = $pi->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $pi = $pi->where('category_item.category_name', 'like', '%' . $request->keyword . '%');
        }

        $pi = $pi->get();

        if (count($pi)) {
            $temp_column = 'category_item.category_name';
            return $temp_column;
        }
        //=======================================================

        $pi = DB::table('price_item_pet_shops as pi')
            ->join('users', 'pi.user_id', '=', 'users.id')
            ->join('list_of_items', 'pi.list_of_items_id', '=', 'list_of_items.id')
            ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
            ->select(
                'list_of_items.item_name',
                'category_item.category_name',
                'unit_item.unit_name',
                'branches.branch_name',
                'users.fullname')
            ->where('pi.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $pi = $pi->where('branches.id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $pi = $pi->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $pi = $pi->where('unit_item.unit_name', 'like', '%' . $request->keyword . '%');
        }

        $pi = $pi->get();

        if (count($pi)) {
            $temp_column = 'unit_item.unit_name';
            return $temp_column;
        }
        //=======================================================

        $pi = DB::table('pi')
            ->join('users', 'pi.user_id', '=', 'users.id')
            ->join('list_of_items', 'pi.list_of_items_id', '=', 'list_of_items.id')
            ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
            ->select(
                'list_of_items.item_name',
                'category_item.category_name',
                'unit_item.unit_name',
                'branches.branch_name',
                'users.fullname')
            ->where('pi.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $pi = $pi->where('branches.id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $pi = $pi->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $pi = $pi->where('branches.branch_name', 'like', '%' . $request->keyword . '%');
        }

        $pi = $pi->get();

        if (count($pi)) {
            $temp_column = 'branches.branch_name';
            return $temp_column;
        }
        //=======================================================

        $pi = DB::table('price_item_pet_shops as pi')
            ->join('users', 'pi.user_id', '=', 'users.id')
            ->join('list_of_items', 'pi.list_of_items_id', '=', 'list_of_items.id')
            ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
            ->select(
                'list_of_items.item_name',
                'category_item.category_name',
                'unit_item.unit_name',
                'branches.branch_name',
                'users.fullname')
            ->where('pi.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $pi = $pi->where('branches.id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $pi = $pi->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $pi = $pi->where('users.fullname', 'like', '%' . $request->keyword . '%');
        }

        $pi = $pi->get();

        if (count($pi)) {
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
            'ListOfItemPetShopId' => 'required|numeric',
            'HargaJual' => 'required|numeric|min:0',
            'HargaModal' => 'required|numeric|min:0',
            'Profit' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return response()->json([
                'message' => 'Data yang dimasukkan tidak valid!',
                'errors' => $errors,
            ], 422);
        }

        $check_list_item = DB::table('price_item_pet_shops')
            ->where('list_of_item_pet_shop_id', '=', $request->ListOfItemPetShopId)
            ->where('isDeleted', '=', 0)
            ->count();

        if ($check_list_item > 0) {

            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data yang dimasukkan sudah ada!'],
            ], 422);
        }

        $item = PriceItemPetShop::create([
            'list_of_item_pet_shop_id' => $request->ListOfItemPetShopId,
            'selling_price' => $request->HargaJual,
            'capital_price' => $request->HargaModal,
            'profit' => $request->Profit,
            'branch_id' => $request->branchId,
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
            'id' => 'required|numeric',
            'ListOfItemPetShopId' => 'required|numeric',
            'HargaJual' => 'required|numeric|min:0',
            'HargaModal' => 'required|numeric|min:0',
            'Profit' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return response()->json([
                'message' => 'Data yang dimasukkan tidak valid!',
                'errors' => $errors,
            ], 422);
        }

        $price_items = PriceItemPetShop::find($request->id);

        if (is_null($price_items)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $check_list_item = DB::table('price_item_pet_shops')
            ->where('list_of_item_pet_shop_id', '=', $request->ListOfItemPetShopId)
            ->where('id', '!=', $request->id)
            ->count();

        if ($check_list_item > 0) {

            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data sudah ada!'],
            ], 422);
        }

        $price_items->list_of_item_pet_shop_id = $request->ListOfItemPetShopId;
        $price_items->selling_price = $request->HargaJual;
        $price_items->capital_price = $request->HargaModal;
        $price_items->profit = $request->Profit;
        $price_items->branch_id = $request->BranchId;
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

        $price_items = PriceItemPetShop::find($request->id);

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

        $list_of_item_pet_shops = DB::table('list_of_item_pet_shops')
            ->select('id', 'item_name', 'total_item')
            ->where('branch_id', '=', $request->branch_id)
            ->where('isDeleted', '=', 0)
            ->distinct('id')
            ->get();

        // if (is_null($list_of_item_pet_shops)) {
        //     return response()->json([
        //         'message' => 'The data was invalid.',
        //         'errors' => ['Data tidak ditemukan!'],
        //     ], 404);
        // }

        return response()->json($list_of_item_pet_shops, 200);
    }

    // public function item_name(Request $request)
    // {
    //     if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
    //         return response()->json([
    //             'message' => 'The user role was invalid.',
    //             'errors' => ['Akses User tidak diizinkan!'],
    //         ], 403);
    //     }

    //     $list_of_items = DB::table('list_of_items')
    //         ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
    //         ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
    //         ->select('list_of_items.id', 'list_of_items.item_name', 'total_item', 'unit_item_id', 'unit_item.unit_name')
    //         ->where('branch_id', '=', $request->branch_id)
    //         ->where('category_item_id', '=', $request->category_item_id)
    //         ->get();

    //     if (is_null($list_of_items)) {
    //         return response()->json([
    //             'message' => 'The data was invalid.',
    //             'errors' => ['Data tidak ditemukan!'],
    //         ], 404);
    //     }

    //     return response()->json($list_of_items, 200);
    // }

    public function download_template(Request $request)
    {
        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        return (new MultipleSheetUploadHargaBarangPetShop())->download('Template Harga Barang Pet Shop.xlsx');
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

        $rows = Excel::toArray(new MultipleSheetImportHargaBarangPetShop($request->user()->id), $request->file('file'));
        $result = $rows[0];

        foreach ($result as $key_result) {

            $check_duplicate = DB::table('price_item_pet_shops')
                ->where('list_of_item_pet_shop_id', '=', $key_result['kode_daftar_barang'])
                ->where('isDeleted', '=', 0)
                ->count();

            if ($check_duplicate > 0) {

                return response()->json([
                    'message' => 'The data was invalid.',
                    'errors' => ['Terdapat Data yang sudah ada!'],
                ], 422);
            }

            $modal = floatval($key_result['harga_modal']);
            $jual = floatval($key_result['harga_jual']);

            if ($modal > $jual) {

                return response()->json([
                    'message' => 'The data was invalid.',
                    'errors' => ['Nominal Harga Jual kode barang ' . $key_result['kode_daftar_barang'] . ' harus lebih dari Harga Modal!'],
                ], 422);
            }
        }

        $file = $request->file('file');

        Excel::import(new MultipleSheetImportHargaBarangPetShop($request->user()->id), $file);

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
            $filename = 'Rekap Harga Barang Pet Shop Cabang ' . $listBranch->branch_name . ' ' . $date . '.xlsx';
        } else {
            $filename = 'Rekap Harga Barang Pet Shop ' . $date . '.xlsx';
        }

        return (new RekapHargaBarangPetShop($request->orderby, $request->column, $request->keyword, $branchId, $request->user()->role))
            ->download($filename);
    }

    public function dropdown(Request $request)
    {
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

}

<?php

namespace App\Http\Controllers;

use App\Exports\RekapHargaKelompokObat;
use App\Models\Branch;
use App\Models\PriceMedicineGroup;
use DB;
use Illuminate\Http\Request;
use Validator;

class HargaKelompokObatController extends Controller
{
    public function index(Request $request)
    {//masih error
        $price_medicine_groups = DB::table('price_medicine_groups')
            ->join('users', 'price_medicine_groups.user_id', '=', 'users.id')
            ->join('medicine_groups', 'price_medicine_groups.medicine_group_id', '=', 'medicine_groups.id')
            ->join('branches', 'medicine_groups.branch_id', '=', 'branches.id')
            ->select('price_medicine_groups.id',
                'medicine_groups.id as medicine_group_id',
                'medicine_groups.group_name',
                'branches.id as branch_id',
                'branches.branch_name',
                DB::raw("TRIM(price_medicine_groups.selling_price)+0 as selling_price"),
                DB::raw("TRIM(price_medicine_groups.capital_price)+0 as capital_price"),
                DB::raw("TRIM(price_medicine_groups.doctor_fee)+0 as doctor_fee"),
                DB::raw("TRIM(price_medicine_groups.petshop_fee)+0 as petshop_fee"),
                'users.fullname as created_by',
                DB::raw("DATE_FORMAT(price_medicine_groups.created_at, '%d %b %Y') as created_at"))
            ->where('price_medicine_groups.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $price_medicine_groups = $price_medicine_groups->where('branches.id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $price_medicine_groups = $price_medicine_groups->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {

            $price_medicine_groups = $price_medicine_groups
                ->where('medicine_groups.group_name', 'like', '%' . $request->keyword . '%')
                ->orwhere('branches.branch_name', 'like', '%' . $request->keyword . '%')
                ->orwhere('users.fullname', 'like', '%' . $request->keyword . '%')
                ->orwhere('price_medicine_groups.created_at', 'like', '%' . $request->keyword . '%');
        }

        if ($request->orderby) {
            $price_medicine_groups = $price_medicine_groups->orderBy($request->column, $request->orderby);
        }

        $price_medicine_groups = $price_medicine_groups->orderBy('price_medicine_groups.id', 'desc');

        $price_medicine_groups = $price_medicine_groups->get();

        return response()->json($price_medicine_groups, 200);
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
            'MedicineGroupId' => 'required|numeric',
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

        $check_list_medicine = DB::table('price_medicine_groups')
            ->where('medicine_group_id', '=', $request->MedicineGroupId)
            ->count();

        if ($check_list_medicine > 0) {

            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data sudah ada!'],
            ], 422);
        }

        $item = PriceMedicineGroup::create([
            'medicine_group_id' => $request->MedicineGroupId,
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
            'MedicineGroupId' => 'required|numeric',
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

        $price_medicine_groups = PriceMedicineGroup::find($request->id);

        if (is_null($price_medicine_groups)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $check_list_medicine_group = DB::table('price_medicine_groups')
            ->where('medicine_group_id', '=', $request->MedicineGroupId)
            ->where('id', '!=', $request->id)
            ->count();

        if ($check_list_medicine_group > 0) {

            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data sudah ada!'],
            ], 422);
        }

        $price_medicine_groups->medicine_group_id = $request->MedicineGroupId;
        $price_medicine_groups->selling_price = $request->HargaJual;
        $price_medicine_groups->capital_price = $request->HargaModal;
        $price_medicine_groups->doctor_fee = $request->FeeDokter;
        $price_medicine_groups->petshop_fee = $request->FeePetShop;
        $price_medicine_groups->user_update_id = $request->user()->id;
        $price_medicine_groups->updated_at = \Carbon\Carbon::now();
        $price_medicine_groups->save();

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

        $price_medicine_groups = PriceMedicineGroup::find($request->id);

        if (is_null($price_medicine_groups)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $price_medicine_groups->isDeleted = true;
        $price_medicine_groups->deleted_by = $request->user()->fullname;
        $price_medicine_groups->deleted_at = \Carbon\Carbon::now();
        $price_medicine_groups->save();

        //$price_medicine_groups->delete();

        return response()->json([
            'message' => 'Berhasil menghapus Data',
        ], 200);
    }

    public function branch_medicine(Request $request)
    {
        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $price_medicine_groups = DB::table('medicine_groups')
        //->join('medicine_groups', 'price_medicine_groups.medicine_group_id', '=', 'medicine_groups.id')
            ->select('id as medicine_group_id', 'medicine_groups.group_name')
            ->where('branch_id', '=', $request->branch_id)
            ->distinct('id')
            ->get();

        if (is_null($price_medicine_groups)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        return response()->json($price_medicine_groups, 200);
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
            $filename = 'Rekap Harga Kelompok Obat Cabang ' . $listBranch->branch_name . ' ' . $date . '.xlsx';
        } else {
            $filename = 'Rekap Harga Kelompok Obat ' . $date . '.xlsx';
        }

        return (new RekapHargaKelompokObat($request->orderby, $request->column, $request->keyword, $branchId, $request->user()->role))
            ->download($filename);
    }
}

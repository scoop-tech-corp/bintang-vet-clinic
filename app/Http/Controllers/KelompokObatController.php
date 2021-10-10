<?php

namespace App\Http\Controllers;

use App\Exports\MultipleSheetUploadKelompokObat;
use App\Imports\MultipleSheetImportKelompokObat;
use App\Models\MedicineGroup;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class KelompokObatController extends Controller
{
    public function index(Request $request)
    {

        if ($request->keyword) {

            $res = $this->Search($request);

            $medicine_groups = DB::table('medicine_groups')
                ->join('users', 'medicine_groups.user_id', '=', 'users.id')
                ->join('branches', 'medicine_groups.branch_id', '=', 'branches.id')
                ->select(
                    'medicine_groups.id',
                    'branches.id as branch_id',
                    'branches.branch_name',
                    'group_name',
                    'users.fullname as created_by',
                    DB::raw("DATE_FORMAT(medicine_groups.created_at, '%d %b %Y') as created_at"))
                ->where('medicine_groups.isDeleted', '=', 0);

            if ($res) {
                $medicine_groups = $medicine_groups->where($res, 'like', '%' . $request->keyword . '%');
            } else {
                $medicine_groups = [];
                return response()->json($medicine_groups, 200);
            }

            if ($request->branch_id && $request->user()->role == 'admin') {
                $medicine_groups = $medicine_groups->where('branches.id', '=', $request->branch_id);
            }

            if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
                $medicine_groups = $medicine_groups->where('branches.id', '=', $request->user()->branch_id);
            }

            if ($request->orderby) {
                $medicine_groups = $medicine_groups->orderBy($request->column, $request->orderby);
            }

            $medicine_groups = $medicine_groups->orderBy('medicine_groups.id', 'desc');

            $medicine_groups = $medicine_groups->get();

            return response()->json($medicine_groups, 200);
            // $medicine_groups = $medicine_groups->where('group_name', 'like', '%' . $request->keyword . '%')
            //     ->orwhere('created_by', 'like', '%' . $request->keyword . '%');
        } else {

            $medicine_groups = DB::table('medicine_groups')
                ->join('users', 'medicine_groups.user_id', '=', 'users.id')
                ->join('branches', 'medicine_groups.branch_id', '=', 'branches.id')
                ->select(
                    'medicine_groups.id',
                    'branches.id as branch_id',
                    'branches.branch_name',
                    'group_name',
                    'users.fullname as created_by',
                    DB::raw("DATE_FORMAT(medicine_groups.created_at, '%d %b %Y') as created_at"))
                ->where('medicine_groups.isDeleted', '=', 0);

            if ($request->branch_id && $request->user()->role == 'admin') {
                $medicine_groups = $medicine_groups->where('branches.id', '=', $request->branch_id);
            }

            if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
                $medicine_groups = $medicine_groups->where('branches.id', '=', $request->user()->branch_id);
            }

            if ($request->orderby) {
                $medicine_groups = $medicine_groups->orderBy($request->column, $request->orderby);
            }

            $medicine_groups = $medicine_groups->orderBy('medicine_groups.id', 'desc');

            $medicine_groups = $medicine_groups->get();

            return response()->json($medicine_groups, 200);
        }

    }

    private function Search(Request $request)
    {
        $temp_column = '';

        $medicine_groups = DB::table('medicine_groups')
            ->join('users', 'medicine_groups.user_id', '=', 'users.id')
            ->join('branches', 'medicine_groups.branch_id', '=', 'branches.id')
            ->select(
                'branches.branch_name',
                'group_name',
                'users.fullname as created_by',
                DB::raw("DATE_FORMAT(medicine_groups.created_at, '%d %b %Y') as created_at"))
            ->where('medicine_groups.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $medicine_groups = $medicine_groups->where('branches.id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $medicine_groups = $medicine_groups->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $medicine_groups = $medicine_groups->where('branches.branch_name', 'like', '%' . $request->keyword . '%');
        }

        $medicine_groups = $medicine_groups->get();

        if (count($medicine_groups)) {
            $temp_column = 'branches.branch_name';
            return $temp_column;
        }
        //=======================================================

        $medicine_groups = DB::table('medicine_groups')
            ->join('users', 'medicine_groups.user_id', '=', 'users.id')
            ->join('branches', 'medicine_groups.branch_id', '=', 'branches.id')
            ->select(
                'branches.branch_name',
                'group_name',
                'users.fullname as created_by',
                DB::raw("DATE_FORMAT(medicine_groups.created_at, '%d %b %Y') as created_at"))
            ->where('medicine_groups.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $medicine_groups = $medicine_groups->where('branches.id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $medicine_groups = $medicine_groups->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $medicine_groups = $medicine_groups->where('group_name', 'like', '%' . $request->keyword . '%');
        }

        $medicine_groups = $medicine_groups->get();

        if (count($medicine_groups)) {
            $temp_column = 'group_name';
            return $temp_column;
        }
        //=======================================================

        $medicine_groups = DB::table('medicine_groups')
            ->join('users', 'medicine_groups.user_id', '=', 'users.id')
            ->join('branches', 'medicine_groups.branch_id', '=', 'branches.id')
            ->select(
                'branches.branch_name',
                'group_name',
                'users.fullname as created_by',
                DB::raw("DATE_FORMAT(medicine_groups.created_at, '%d %b %Y') as created_at"))
            ->where('medicine_groups.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $medicine_groups = $medicine_groups->where('branches.id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $medicine_groups = $medicine_groups->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $medicine_groups = $medicine_groups->where('users.fullname', 'like', '%' . $request->keyword . '%');
        }

        $medicine_groups = $medicine_groups->get();

        if (count($medicine_groups)) {
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

        $validate = Validator::make($request->all(), [
            'NamaGrup' => 'required|string|max:50',
            'Cabang' => 'required|integer',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $errors,
            ], 422);
        }

        $find_duplicate = db::table('medicine_groups')
            ->select('group_name')
            ->where('group_name', '=', $request->NamaGrup)
            ->where('branch_id', '=', $request->Cabang)
            ->count();

        if ($find_duplicate != 0) {

            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data sudah ada!'],
            ], 422);

        }

        MedicineGroup::create([
            'group_name' => $request->NamaGrup,
            'branch_id' => $request->Cabang,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Berhasil menambah Kelompok Barang',
        ], 200);
    }

    public function update(Request $request)
    {
        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $validate = Validator::make($request->all(), [
            'NamaGrup' => 'required|string|max:50',
            'Cabang' => 'required|integer',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $errors,
            ], 422);
        }

        $medicine_groups = MedicineGroup::find($request->id);

        if (is_null($medicine_groups)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $find_duplicate = db::table('medicine_groups')
            ->select('group_name')
            ->where('group_name', '=', $request->NamaGrup)
            ->where('branch_id', '=', $request->Cabang)
            ->where('id', '!=', $request->id)
            ->count();

        if ($find_duplicate != 0) {

            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data sudah ada!'],
            ], 422);

        }

        $medicine_groups->group_name = $request->NamaGrup;
        $medicine_groups->branch_id = $request->Cabang;
        $medicine_groups->user_update_id = $request->user()->id;
        $medicine_groups->updated_at = \Carbon\Carbon::now();
        $medicine_groups->save();

        return response()->json([
            'message' => 'Berhasil mengupdate Kelompok Obat',
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

        $medicine_groups = MedicineGroup::find($request->id);

        if (is_null($medicine_groups)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $medicine_groups->isDeleted = true;
        $medicine_groups->deleted_by = $request->user()->fullname;
        $medicine_groups->deleted_at = \Carbon\Carbon::now();
        $medicine_groups->save();

        //$medicine_groups->delete();

        return response()->json([
            'message' => 'Berhasil menghapus Kategori Barang',
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

        return (new MultipleSheetUploadKelompokObat())->download('Template Kelompok Obat.xlsx');
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

        $rows = Excel::toArray(new MultipleSheetImportKelompokObat, $request->file('file'));
        $result = $rows[0];

        foreach ($result as $key_result) {

            $check_branch = DB::table('medicine_groups')
                ->where('branch_id', '=', $key_result['kode_cabang'])
                ->where('group_name', '=', $key_result['nama_kelompok'])
                ->count();

            if ($check_branch > 0) {

                return response()->json([
                    'message' => 'The data was invalid.',
                    'errors' => ['Data ' . $key_result['nama_kelompok'] . ' sudah ada!'],
                ], 422);
            }
        }

        $file = $request->file('file');

        Excel::import(new MultipleSheetImportKelompokObat, $file);

        return response()->json([
            'message' => 'Berhasil mengupload Kelompok Obat',
        ], 200);
    }
}

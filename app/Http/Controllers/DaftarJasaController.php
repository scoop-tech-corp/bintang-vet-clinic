<?php

namespace App\Http\Controllers;

use App\Models\ListofServices;
use DB;
use Illuminate\Http\Request;
use Validator;

class DaftarJasaController extends Controller
{
    public function index(Request $request)
    {

        if ($request->keyword) {

            $res = $this->Search($request);

            $list_of_services = DB::table('list_of_services')
                ->join('users', 'list_of_services.user_id', '=', 'users.id')
                ->join('branches', 'list_of_services.branch_id', '=', 'branches.id')
                ->join('service_categories', 'list_of_services.service_category_id', '=', 'service_categories.id')
                ->select(
                    'list_of_services.id',
                    'list_of_services.service_name',
                    'list_of_services.service_category_id',
                    'service_categories.category_name',
                    'list_of_services.branch_id',
                    'branches.branch_name',
                    'users.fullname as created_by',
                    DB::raw("DATE_FORMAT(list_of_services.created_at, '%d %b %Y') as created_at"))
                ->where('list_of_services.isDeleted', '=', 0);

            if ($res) {
                $list_of_services = $list_of_services->where($res, 'like', '%' . $request->keyword . '%');
            } else {
                $data = [];
                return response()->json($data, 200);
            }

            if ($request->branch_id && $request->user()->role == 'admin') {
                $list_of_services = $list_of_services->where('list_of_services.branch_id', '=', $request->branch_id);
            }

            if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
                $list_of_services = $list_of_services->where('list_of_services.branch_id', '=', $request->user()->branch_id);
            }

            if ($request->orderby) {
                $list_of_services = $list_of_services->orderBy($request->column, $request->orderby);
            }

            $list_of_services = $list_of_services->orderBy('list_of_services.id', 'desc');

            $list_of_services = $list_of_services->get();

            return response()->json($list_of_services, 200);

        } else {

            $list_of_services = DB::table('list_of_services')
                ->join('users', 'list_of_services.user_id', '=', 'users.id')
                ->join('branches', 'list_of_services.branch_id', '=', 'branches.id')
                ->join('service_categories', 'list_of_services.service_category_id', '=', 'service_categories.id')
                ->select(
                    'list_of_services.id',
                    'list_of_services.service_name',
                    'list_of_services.service_category_id',
                    'service_categories.category_name',
                    'list_of_services.branch_id',
                    'branches.branch_name',
                    'users.fullname as created_by',
                    DB::raw("DATE_FORMAT(list_of_services.created_at, '%d %b %Y') as created_at"))
                ->where('list_of_services.isDeleted', '=', 0);

            if ($request->branch_id && $request->user()->role == 'admin') {
                $list_of_services = $list_of_services->where('list_of_services.branch_id', '=', $request->branch_id);
            }

            if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
                $list_of_services = $list_of_services->where('list_of_services.branch_id', '=', $request->user()->branch_id);
            }

            if ($request->orderby) {
                $list_of_services = $list_of_services->orderBy($request->column, $request->orderby);
            }

            $list_of_services = $list_of_services->orderBy('list_of_services.id', 'desc');

            $list_of_services = $list_of_services->get();

            return response()->json($list_of_services, 200);
        }

    }

    private function Search($request)
    {
        $temp_column = '';

        $list_of_services = DB::table('list_of_services')
            ->join('users', 'list_of_services.user_id', '=', 'users.id')
            ->join('branches', 'list_of_services.branch_id', '=', 'branches.id')
            ->join('service_categories', 'list_of_services.service_category_id', '=', 'service_categories.id')
            ->select(
                'list_of_services.service_name',
                'service_categories.category_name',
                'branches.branch_name',
                'users.fullname as created_by')
            ->where('list_of_services.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $list_of_services = $list_of_services->where('list_of_services.branch_id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $list_of_services = $list_of_services->where('list_of_services.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $list_of_services = $list_of_services->where('list_of_services.service_name', 'like', '%' . $request->keyword . '%');
        }

        $list_of_services = $list_of_services->get();

        if (count($list_of_services)) {
            $temp_column = 'list_of_services.service_name';
            return $temp_column;
        }
        //========================================

        $list_of_services = DB::table('list_of_services')
            ->join('users', 'list_of_services.user_id', '=', 'users.id')
            ->join('branches', 'list_of_services.branch_id', '=', 'branches.id')
            ->join('service_categories', 'list_of_services.service_category_id', '=', 'service_categories.id')
            ->select(
                'list_of_services.service_name',
                'service_categories.category_name',
                'branches.branch_name',
                'users.fullname as created_by')
            ->where('list_of_services.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $list_of_services = $list_of_services->where('list_of_services.branch_id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $list_of_services = $list_of_services->where('list_of_services.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $list_of_services = $list_of_services->where('service_categories.category_name', 'like', '%' . $request->keyword . '%');
        }

        $list_of_services = $list_of_services->get();

        if (count($list_of_services)) {
            $temp_column = 'service_categories.category_name';
            return $temp_column;
        }
        //========================================

        $list_of_services = DB::table('list_of_services')
            ->join('users', 'list_of_services.user_id', '=', 'users.id')
            ->join('branches', 'list_of_services.branch_id', '=', 'branches.id')
            ->join('service_categories', 'list_of_services.service_category_id', '=', 'service_categories.id')
            ->select(
                'list_of_services.service_name',
                'service_categories.category_name',
                'branches.branch_name',
                'users.fullname as created_by')
            ->where('list_of_services.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $list_of_services = $list_of_services->where('list_of_services.branch_id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $list_of_services = $list_of_services->where('list_of_services.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $list_of_services = $list_of_services->where('branches.branch_name', 'like', '%' . $request->keyword . '%');
        }

        $list_of_services = $list_of_services->get();

        if (count($list_of_services)) {
            $temp_column = 'branches.branch_name';
            return $temp_column;
        }
        //========================================

        $list_of_services = DB::table('list_of_services')
            ->join('users', 'list_of_services.user_id', '=', 'users.id')
            ->join('branches', 'list_of_services.branch_id', '=', 'branches.id')
            ->join('service_categories', 'list_of_services.service_category_id', '=', 'service_categories.id')
            ->select(
                'list_of_services.service_name',
                'service_categories.category_name',
                'branches.branch_name',
                'users.fullname as created_by')
            ->where('list_of_services.isDeleted', '=', 0);

        if ($request->branch_id && $request->user()->role == 'admin') {
            $list_of_services = $list_of_services->where('list_of_services.branch_id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $list_of_services = $list_of_services->where('list_of_services.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {
            $list_of_services = $list_of_services->where('users.fullname', 'like', '%' . $request->keyword . '%');
        }

        $list_of_services = $list_of_services->get();

        if (count($list_of_services)) {
            $temp_column = 'users.fullname';
            return $temp_column;
        }
        //========================================
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
            'nama_layanan' => 'required|string|min:3|max:50',
            'kategori_jasa' => 'required|integer|max:50',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return response()->json([
                'message' => 'Jasa yang dimasukkan tidak valid!',
                'errors' => $errors,
            ], 422);
        }

        $branchId = $request->cabang;
        $result_branch = json_decode($branchId, true);

        if (count($result_branch) == 0) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => ['Data Cabang Harus dipilih minimal 1!'],
            ], 422);
        }

        foreach ($result_branch as $key_branch) {

            $check_service = DB::table('list_of_services')
                ->where('branch_id', '=', $key_branch)
                ->where('service_category_id', '=', $request->kategori_jasa)
                ->where('service_name', '=', $request->nama_layanan)
                ->count();

            if ($check_service > 0) {

                return response()->json([
                    'message' => 'The data was invalid.',
                    'errors' => ['Data sudah pernah ada sebelumnya!'],
                ], 422);
            }
        }

        foreach ($result_branch as $key_branch) {
            $list_of_services = ListofServices::create([
                'service_name' => $request->nama_layanan,
                'service_category_id' => $request->kategori_jasa,
                'branch_id' => $key_branch,
                'user_id' => $request->user()->id,
            ]);
        }

        return response()->json(
            [
                'message' => 'Tambah Jasa Berhasil!',
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

        $validate = Validator::make($request->all(), [
            'nama_layanan' => 'required|string|min:3|max:50',
            'kategori_jasa' => 'required|integer|max:50',
            'cabang_id' => 'required|integer',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $errors,
            ], 422);
        }

        $list_of_services = ListofServices::find($request->id);

        if (is_null($list_of_services)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $find_duplicate = db::table('list_of_services')
            ->where('branch_id', '=', $request->cabang_id)
            ->where('service_name', '=', $request->nama_layanan)
            ->where('id', '!=', $request->id)
            ->count();

        if ($find_duplicate != 0) {

            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data sudah ada!'],
            ], 422);

        }

        $list_of_services->service_name = $request->nama_layanan;
        $list_of_services->service_category_id = $request->kategori_jasa;
        $list_of_services->branch_id = $request->cabang_id;
        $list_of_services->user_update_id = $request->user()->id;
        $list_of_services->updated_at = \Carbon\Carbon::now();
        $list_of_services->save();

        return response()->json([
            'message' => 'Berhasil mengubah Jasa',
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

        $list_of_services = ListofServices::find($request->id);

        if (is_null($list_of_services)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $list_of_services->isDeleted = true;
        $list_of_services->deleted_by = $request->user()->fullname;
        $list_of_services->deleted_at = \Carbon\Carbon::now();
        $list_of_services->save();

        return response()->json([
            'message' => 'Berhasil menghapus Barang',
        ], 200);
    }
}

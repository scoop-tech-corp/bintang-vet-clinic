<?php

namespace App\Http\Controllers;

use App\Models\UnitItem;
use DB;
use Illuminate\Http\Request;
use Validator;

class SatuanBarangController extends Controller
{
    public function index(Request $request)
    {
        $unit_item = DB::table('unit_item')
            ->join('users', 'unit_item.user_id', '=', 'users.id')
            ->select('unit_item.id', 'unit_name', 'users.fullname as created_by',
                DB::raw("DATE_FORMAT(unit_item.created_at, '%d %b %Y') as created_at"))
            ->where('unit_item.isDeleted', '=', 'false');

        if ($request->keyword) {
            $unit_item = $unit_item->where('unit_name', 'like', '%' . $request->keyword . '%')
                ->orwhere('created_by', 'like', '%' . $request->keyword . '%');
        }

        if ($request->orderby) {
            $unit_item = $unit_item->orderBy($request->column, $request->orderby);
        }

        $unit_item = $unit_item->orderBy('unit_item.id', 'desc');

        $unit_item = $unit_item->get();

        return response()->json($unit_item, 200);

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
            'NamaSatuan' => 'required|string|max:50|unique:unit_item,unit_name',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $errors,
            ], 422);
        }

        UnitItem::create([
            'unit_name' => $request->NamaSatuan,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Berhasil menambah Unit Barang',
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
            'NamaSatuan' => 'required|string|max:50',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $errors,
            ], 422);
        }

        $unit_item = UnitItem::find($request->id);

        if (is_null($unit_item)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $find_duplicate = db::table('unit_item')
            ->select('unit_name')
            ->where('unit_name', '=', $request->NamaSatuan)
            ->where('id', '!=', $request->id)
            ->count();

        if ($find_duplicate != 0) {

            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data sudah ada!'],
            ], 422);

        }

        $unit_item->unit_name = $request->NamaSatuan;
        $unit_item->user_update_id = $request->user()->id;
        $unit_item->updated_at = \Carbon\Carbon::now();
        $unit_item->save();

        return response()->json([
            'message' => 'Berhasil mengupdate Unit Barang',
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

        $unit_item = UnitItem::find($request->id);

        if (is_null($unit_item)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $unit_item->isDeleted = true;
        $unit_item->deleted_by = $request->user()->fullname;
        $unit_item->deleted_at = \Carbon\Carbon::now();
        $unit_item->save();

        //$unit_item->delete();

        return response()->json([
            'message' => 'Berhasil menghapus Unit Barang',
        ], 200);
    }
}

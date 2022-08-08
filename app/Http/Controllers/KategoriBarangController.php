<?php

namespace App\Http\Controllers;

use App\Models\CategoryItem;
use DB;
use Illuminate\Http\Request;
use Validator;

class KategoriBarangController extends Controller
{
    public function index(Request $request)
    {
        $category_item = DB::table('category_item')
            ->join('users', 'category_item.user_id', '=', 'users.id')
            ->select('category_item.id', 'category_name', 'users.fullname as created_by',
                DB::raw("DATE_FORMAT(category_item.created_at, '%d %b %Y') as created_at"))
            ->where('category_item.isDeleted', '=', 'false');

        if ($request->keyword) {
            $category_item = $category_item->where('category_name', 'like', '%' . $request->keyword . '%')
                ->orwhere('created_by', 'like', '%' . $request->keyword . '%');
        }

        if ($request->orderby) {
            $category_item = $category_item->orderBy($request->column, $request->orderby);
        }

        $category_item = $category_item->orderBy('category_item.id', 'desc');

        $category_item = $category_item->get();

        return response()->json($category_item, 200);
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
            'NamaKategori' => 'required|string|max:50|unique:category_item,category_name',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $errors,
            ], 422);
        }

        CategoryItem::create([
            'category_name' => $request->NamaKategori,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Berhasil menambah Kategori Barang',
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
            'NamaKategori' => 'required|string|max:50',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $errors,
            ], 422);
        }

        $category_item = CategoryItem::find($request->id);

        if (is_null($category_item)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $find_duplicate = db::table('category_item')
            ->select('category_name')
            ->where('category_name', 'like', '%' . $request->NamaKategori . '%')
            ->where('id', '!=', $request->id)
            ->count();

        if ($find_duplicate != 0) {

            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data sudah ada!'],
            ], 422);

        }

        $category_item->category_name = $request->NamaKategori;
        $category_item->user_update_id = $request->user()->id;
        $category_item->updated_at = \Carbon\Carbon::now();
        $category_item->save();

        return response()->json([
            'message' => 'Berhasil mengupdate Kategori Barang',
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

        $category_item = CategoryItem::find($request->id);

        if (is_null($category_item)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $category_item->isDeleted = true;
        $category_item->deleted_by = $request->user()->fullname;
        $category_item->deleted_at = \Carbon\Carbon::now();
        $category_item->save();

        return response()->json([
            'message' => 'Berhasil menghapus Kategori Barang',
        ], 200);
    }
}
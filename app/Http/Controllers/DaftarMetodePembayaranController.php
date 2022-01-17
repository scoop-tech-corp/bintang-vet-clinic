<?php

namespace App\Http\Controllers;

use App\Models\payment_method;
use DB;
use Illuminate\Http\Request;
use Validator;

class DaftarMetodePembayaranController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $payment_method = DB::table('payment_methods')
            ->join('users', 'payment_methods.user_id', '=', 'users.id')
            ->select('payment_methods.id', 'payment_name',
                'users.fullname as created_by',
                DB::raw("DATE_FORMAT(payment_methods.created_at, '%d %b %Y') as created_at"))
            ->where('payment_methods.isDeleted', '=', 0);

        if ($request->keyword) {
            $payment_method = $payment_method->where('payment_methods.payment_name', 'like', '%' . $request->keyword . '%')
                ->orwhere('users.fullname', 'like', '%' . $request->keyword . '%');
        }

        if ($request->orderby) {

            $payment_method = $payment_method->orderBy($request->column, $request->orderby);
        }

        $payment_method = $payment_method->orderBy('id', 'desc');

        $payment_method = $payment_method->get();

        return response()->json($payment_method, 200);
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
            'NamaPembayaran' => 'required|string|max:20|min:3|unique:payment_methods,payment_name',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $errors,
            ], 422);
        }

        payment_method::create([
            'payment_name' => $request->NamaPembayaran,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Berhasil menambah Metode Pembayaran',
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
            'NamaPembayaran' => 'required|string|max:20|min:3',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $errors,
            ], 422);
        }

        $payment_method = payment_method::find($request->id);

        if (is_null($payment_method)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $payment_method->payment_name = $request->NamaPembayaran;
        $payment_method->user_update_id = $request->user()->id;
        $payment_method->updated_at = \Carbon\Carbon::now();
        $payment_method->save();

        return response()->json([
            'message' => 'Berhasil mengupdate Cabang',
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

        $payment_method = payment_method::find($request->id);

        if (is_null($payment_method)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $payment_method->isDeleted = true;
        $payment_method->deleted_by = $request->user()->fullname;
        $payment_method->deleted_at = \Carbon\Carbon::now();
        $payment_method->save();

        return response()->json([
            'message' => 'Berhasil menghapus Metode Pembayaran',
        ], 200);
    }
}
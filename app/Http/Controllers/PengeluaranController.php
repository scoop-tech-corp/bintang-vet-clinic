<?php

namespace App\Http\Controllers;

use App\Models\Expenses;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $items_per_page = 50;

        $page = $request->page;

        $expenses = DB::table('expenses as e')
            ->join('users', 'e.user_id', '=', 'users.id')
            ->join('users as user_spender', 'e.user_id_spender', '=', 'user_spender.id')
            ->select('e.date_spend',
                'user_spender.fullname',
                'e.item_name',
                'e.quantity',
                'e.amount',
                'e.amount_overall',
                'users.fullname as created_by',
                DB::raw("DATE_FORMAT(e.created_at, '%d %b %Y') as created_at"));

        if ($request->keyword) {
            $res = $this->Search($request);

            if ($res) {
                $expenses = $expenses->where($res, 'like', '%' . $request->keyword . '%');
            } else {
                $data = [];
                return response()->json(['total_paging' => 0,
                    'data' => $data], 200);
            }
        }

        if ($request->orderby) {
            $expenses = $expenses->orderBy($request->column, $request->orderby);
        }

        $offset = ($page - 1) * $items_per_page;

        $count_data = $expenses->count();
        $count_result = $count_data - $offset;

        if ($count_result < 0) {
            $expenses = $expenses->offset(0)->limit($items_per_page)->get();
        } else {
            $expenses = $expenses->offset($offset)->limit($items_per_page)->get();
        }

        $total_paging = $count_data / $items_per_page;

        return response()->json(['total_paging' => ceil($total_paging),
            'data' => $expenses], 200);

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
            'date_spend' => 'required|date_format:d/m/Y',
            'user_id_spender' => 'required|numeric',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|numeric',
            'items.*.amount' => 'required|numeric',
            'items.*.amount_overall' => 'required|numeric',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $errors,
            ], 422);
        }

        $tmp_items = $request->items;

        // return $tmp_items;

        $items = json_decode($tmp_items, true);

        foreach ($items as $item) {

            $expense = Expenses::create([
                'date_spend' => Carbon::parse(Carbon::createFromFormat('d/m/Y', $request->date_spend)->format('Y/m/d')),
                'user_id_spender' => $request->user_id_spender,
                'item_name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'amount' => $item['amount'],
                'amount_overall' => $item['amount_overall'],
            ]);

        }

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

        $validate = Validator::make($request->all(), [
            'date_spend' => 'required|date_format:d/m/Y',
            'user_id_spender' => 'required|numeric',
            'item_name' => 'required|string',
            'quantity' => 'required|numeric',
            'amount' => 'required|numeric',
            'amount_overall' => 'required|numeric',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $errors,
            ], 422);
        }

        $expense = Expenses::find($request->id);
        $expense->date_spend = Carbon::parse(Carbon::createFromFormat('d/m/Y', $request->date_spend)->format('Y/m/d'));
        $expense->user_id_spender = $request->user_id_spender;
        $expense->item_name = $request->item_name;
        $expense->quantity = $request->quantity;
        $expense->amount = $request->amount;
        $expense->amount_overall = $request->amount_overall;

        $expense->save();

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

        $data = Expenses::where('id', '=', $request->id)
            ->count();

        if ($data == 0) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ada ada!'],
            ], 422);
        }

        $data = Expenses::find($request->id);
        $data->delete();

        return response()->json([
            'message' => 'Berhasil menghapus Data',
        ], 200);
    }
}
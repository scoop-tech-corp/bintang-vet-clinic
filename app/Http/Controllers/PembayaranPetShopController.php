<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ListofItemsPetShop;
use App\Models\master_payment_petshop;
use App\Models\payment_petshop;
use App\Models\PriceItemPetShop;
use DB;
use Illuminate\Http\Request;
use PDF;
use Validator;

class PembayaranPetShopController extends Controller
{
    public function index(Request $request)
    {

        $items_per_page = 50;

        $page = $request->page;

        $payment = DB::table('payments as py')
            ->join('master_payments as mp', 'py.master_payment_id', '=', 'mp.id')
            ->join('list_of_items as loi', 'py.list_of_item_id', '=', 'loi.id')
            ->join('users', 'py.user_id', '=', 'users.id')
            ->join('branches', 'mp.branch_id', '=', 'branches.id')
            ->select(
                'py.id',
                'mp.payment_number',
                'loi.item_name',
                'py.total_item',
                'loi.category',
                DB::raw("TRIM(loi.selling_price)+0 as each_price"),
                DB::raw("TRIM(loi.selling_price * py.total_item)+0 as overall_price"),
                'branches.id as branch_id',
                'branches.branch_name',
                'users.id as user_id',
                'users.fullname as created_by',
                DB::raw("DATE_FORMAT(py.created_at, '%d/%m/%Y') as created_at"))
            ->where('py.isDeleted', '=', 0);

        if ($request->keyword) {

            $res = $this->Search($request);

            if ($res) {
                $data = $data->where($res, 'like', '%' . $request->keyword . '%');
            } else {
                $data = [];
                return response()->json(['total_paging' => 0,
                    'data' => $data], 200);
            }

        }

        if ($request->branch_id && $request->user()->role == 'admin') {
            $payment = $payment->where('loi.branch_id', '=', $request->branch_id);
        }

        if ($request->user()->role == 'kasir') {
            $payment = $payment->where('mp.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->orderby) {
            $payment = $payment->orderBy($request->column, $request->orderby);
        }

        $payment = $payment->orderBy('py.id', 'desc');

        $payment = $payment->get();

        return response()->json($payment, 200);

    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'price_item_pet_shops.*.price_item_pet_shop_id' => 'required|numeric',
            'price_item_pet_shops.*.total_item' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return response()->json([
                'message' => 'Data yang dimasukkan tidak valid!',
                'errors' => $errors,
            ], 422);
        }

        $items = $request->price_item_pet_shops;
        $result_items = json_decode($items, true);
        //$items;
        //json_decode($items, true);

        $lastnumber = DB::table('master_payment_petshops')
            ->where('branch_id', '=', $request->branch_id)
            ->count();

        $branch = Branch::find($request->branch_id);

        $payment_number = 'BVS-P-' . $branch->branch_code . '-' . str_pad($lastnumber + 1, 4, 0, STR_PAD_LEFT);

        $master_payment = master_payment_petshop::create([
            'payment_number' => $payment_number,
            'user_id' => $request->user()->id,
            'branch_id' => $request->branch_id,
        ]);

        foreach ($result_items as $value) {

            $find_item = PriceItemPetShop::find($value['price_item_pet_shop_id']);

            if (is_null($find_item)) {
                return response()->json([
                    'message' => 'The data was invalid.',
                    'errors' => ['Barang tidak ada!'],
                ], 422);
            }

            $find_qty_item = ListofItemsPetShop::find($find_item->id);

            $res_value = $find_qty_item->total_item - $value['total_item'];

            if ($res_value < 0) {
                return response()->json([
                    'message' => 'The data was invalid.',
                    'errors' => ['Stok Barang ' . $find_qty_item->item_name . ' kurang atau habis!'],
                ], 422);
            }

            $find_qty_item->total_item = $res_value;
            $find_qty_item->user_update_id = $request->user()->id;
            $find_qty_item->updated_at = \Carbon\Carbon::now();
            $find_qty_item->save();

            payment_petshop::create([
                'price_item_pet_shop_id' => $value['price_item_pet_shop_id'],
                'total_item' => $value['total_item'],
                'master_payment_petshop_id' => $master_payment->id,
                'user_id' => $request->user()->id,
            ]);

        }

        return response()->json(
            [
                'message' => 'Tambah Data Berhasil!',
                'master_payment_petshop_id' => $master_payment->id,
            ], 200
        );
    }

    public function update(Request $request)
    {

    }

    public function delete(Request $request)
    {
        if ($request->user()->role == 'kasir') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $payment = payment_petshop::where('id', '=', $request->id)
            ->count();

        if ($payment == 0) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ada ada!'],
            ], 422);
        }

        $payment = payment_petshop::find($request->id);

        $find_item = ListofItemsPetShop::find($payment->list_of_item_pet_shop_id);

        $res_value = $find_item->total_item + $payment->total_item;

        $find_item->total_item = $res_value;
        $find_item->user_update_id = $request->user()->id;
        $find_item->updated_at = \Carbon\Carbon::now();
        $find_item->save();

        $payment->user_update_id = $request->user()->id;
        $payment->isDeleted = 1;
        $payment->deleted_by = $request->user()->id;
        $payment->updated_at = \Carbon\Carbon::now();
        $payment->deleted_at = \Carbon\Carbon::now();
        $payment->save();

        return response()->json([
            'message' => 'Berhasil menghapus Data',
        ], 200);
    }

    public function filteritempetshop(Request $request)
    {
        $item = DB::table('list_of_item_pet_shops as loi')
            ->join('price_item_pet_shops as pip', 'loi.id', 'pip.list_of_item_pet_shop_id')
            ->select('pip.id', 'loi.item_name as item_name', DB::raw("TRIM(pip.selling_price)+0 as selling_price"))
            ->where('pip.isDeleted', '=', 0)
            ->where('pip.branch_id', '=', $request->branch_id)
            ->get();

        return response()->json($item, 200);
    }

    public function print_receipt(Request $request)
    {
        $data_header = DB::table('master_payment_petshops as mp')
            ->join('users', 'mp.user_id', '=', 'users.id')
            ->join('branches', 'mp.branch_id', '=', 'branches.id')
            ->select(
                'branches.branch_name',
                'branches.address',
                'mp.payment_number',
                'users.fullname as cashier_name',
                DB::raw("DATE_FORMAT(mp.created_at, '%d %b %Y %H:%i:%s') as paid_time"))
            ->where('mp.id', '=', $request->master_payment_id)
            ->get();

        $data_detail = DB::table('payment_petshops as py')
            ->join('price_item_pet_shops as pi', 'py.price_item_pet_shop_id', '=', 'pi.id')
            ->join('list_of_item_pet_shops as loi', 'pi.list_of_item_pet_shop_id', '=', 'loi.id')
            ->select(
                'loi.item_name',
                'py.total_item',
                DB::raw("TRIM(pi.selling_price)+0 as each_price"),
                DB::raw("TRIM(py.total_item * pi.selling_price)+0 as total_price"))
            ->where('py.master_payment_petshop_id', '=', $request->master_payment_id)
            ->get();

        $price_overall = DB::table('payment_petshops as py')
            ->join('price_item_pet_shops as pi', 'py.price_item_pet_shop_id', '=', 'pi.id')
            ->join('list_of_item_pet_shops as loi', 'pi.list_of_item_pet_shop_id', '=', 'loi.id')
            ->select(
                DB::raw("TRIM(SUM(py.total_item * pi.selling_price))+0 as price_overall"))
            ->where('py.master_payment_petshop_id', '=', $request->master_payment_id)
            ->first();

        $data = [
            'data_header' => $data_header,
            'data_detail' => $data_detail,
            'price_overall' => $price_overall,
        ];

        $find_payment_number = DB::table('master_payment_petshops')
            ->select('payment_number')
            ->where('id', '=', $request->master_payment_id)
            ->first();

        $pdf = PDF::loadview('petshop-print', $data);

        return $pdf->download($find_payment_number->payment_number . '.pdf');
    }
}

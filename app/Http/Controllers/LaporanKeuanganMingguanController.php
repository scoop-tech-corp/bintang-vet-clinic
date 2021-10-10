<?php

namespace App\Http\Controllers;

use App\Exports\LaporanKeuanganMingguan;
use App\Models\Branch;
use App\Models\ListofPayments;
use DB;
use Excel;
use Illuminate\Http\Request;

class LaporanKeuanganMingguanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $item = DB::table('list_of_payments as lop')
            ->join('check_up_results as cur', 'lop.check_up_result_id', '=', 'cur.id')
            ->join('list_of_payment_medicine_groups as lopm', 'lopm.list_of_payment_id', '=', 'lop.id')
            ->join('price_medicine_groups as pmg', 'lopm.medicine_group_id', '=', 'pmg.id')
            ->join('registrations as reg', 'cur.patient_registration_id', '=', 'reg.id')
            ->join('patients as pa', 'reg.patient_id', '=', 'pa.id')
            ->join('users', 'lop.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')

            ->select(
                'lop.id as list_of_payment_id',
                'lop.check_up_result_id as check_up_result_id',
                'reg.id_number as registration_number',
                'pa.id_member as patient_number',
                'pa.pet_category',
                'pa.pet_name',
                'reg.complaint',
                DB::raw("TRIM(SUM(pmg.selling_price))+0 as price_overall"),
                DB::raw("TRIM(SUM(pmg.capital_price))+0 as capital_price"),
                DB::raw("TRIM(SUM(pmg.doctor_fee))+0 as doctor_fee"),
                DB::raw("TRIM(SUM(pmg.petshop_fee))+0 as petshop_fee"),
                'users.fullname as created_by',
                'lop.created_at as created_at',
                'branches.id as branchId');
        if ($request->date_from && $request->date_to) {
            $item = $item->whereBetween(DB::raw('DATE(lopm.updated_at)'), [$request->date_from, $request->date_to]);
        }

        $item = $item->groupBy('lop.check_up_result_id');

        $service = DB::table('list_of_payments')
            ->join('check_up_results', 'list_of_payments.check_up_result_id', '=', 'check_up_results.id')
            ->join('list_of_payment_services', 'check_up_results.id', '=', 'list_of_payment_services.check_up_result_id')
            ->join('detail_service_patients', 'list_of_payment_services.detail_service_patient_id', '=', 'detail_service_patients.id')
            ->join('price_services', 'detail_service_patients.price_service_id', '=', 'price_services.id')
            ->join('registrations', 'check_up_results.patient_registration_id', '=', 'registrations.id')
            ->join('patients', 'registrations.patient_id', '=', 'patients.id')
            ->join('users', 'check_up_results.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')

            ->select(
                'list_of_payments.id as list_of_payment_id',
                'list_of_payments.check_up_result_id as check_up_result_id',
                'registrations.id_number as registration_number',
                'patients.id_member as patient_number',
                'patients.pet_category',
                'patients.pet_name',
                'registrations.complaint',
                DB::raw("TRIM(SUM(detail_service_patients.price_overall))+0 as price_overall"),
                DB::raw("TRIM(SUM(price_services.capital_price * detail_service_patients.quantity))+0 as capital_price"),
                DB::raw("TRIM(SUM(price_services.doctor_fee * detail_service_patients.quantity))+0 as doctor_fee"),
                DB::raw("TRIM(SUM(price_services.petshop_fee * detail_service_patients.quantity))+0 as petshop_fee"),
                'users.fullname as created_by',
                'list_of_payment_services.created_at as created_at',
                'branches.id as branchId'
            );
        if ($request->date_from && $request->date_to) {
            $service = $service->whereBetween(DB::raw('DATE(list_of_payment_services.updated_at)'), [$request->date_from, $request->date_to]);
        }

        $service = $service->groupBy('list_of_payments.check_up_result_id')
            ->union($item);

        $data = DB::query()->fromSub($service, 'p_pn')
            ->select('list_of_payment_id', 'check_up_result_id',
                'registration_number', 'patient_number', 'pet_category', 'pet_name', 'complaint',
                DB::raw("TRIM(SUM(price_overall))+0 as price_overall"),
                DB::raw("TRIM(SUM(capital_price))+0 as capital_price"),
                DB::raw("TRIM(SUM(doctor_fee))+0 as doctor_fee"),
                DB::raw("TRIM(SUM(petshop_fee))+0 as petshop_fee"),
                'created_by',
                DB::raw("DATE_FORMAT(created_at, '%d %b %Y') as created_at"));

        if ($request->branch_id && $request->user()->role == 'admin') {
            $data = $data->where('branchId', '=', $request->branch_id);
        } elseif ($request->user()->role == 'dokter') {
            $data = $data->where('branchId', '=', $request->user()->branch_id);
        }

        if ($request->orderby) {

            $data = $data->orderBy($request->column, $request->orderby);
        } else {
            $data = $data->orderBy('list_of_payment_id', 'desc');
        }

        $data = $data->groupBy('check_up_result_id')
            ->get();

        $price_overall_item = DB::table('list_of_payments as lop')
            ->join('list_of_payment_medicine_groups as lopm', 'lop.id', '=', 'lopm.list_of_payment_id')
            ->join('price_medicine_groups as pmg', 'lopm.medicine_group_id', '=', 'pmg.id')
            ->join('users', 'lop.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->select(
                DB::raw("TRIM(SUM(pmg.selling_price))+0 as price_overall"));

        if ($request->branch_id && $request->user()->role == 'admin') {
            $price_overall_item = $price_overall_item->where('branches.id', '=', $request->branch_id);
        } elseif ($request->user()->role == 'dokter') {
            $price_overall_item = $price_overall_item->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->date_from && $request->date_to) {
            $price_overall_item = $price_overall_item->whereBetween(DB::raw('DATE(lopm.updated_at)'), [$request->date_from, $request->date_to]);
        }
        $price_overall_item = $price_overall_item->first();

        $price_overall_service = DB::table('list_of_payments')
            ->join('check_up_results', 'list_of_payments.check_up_result_id', '=', 'check_up_results.id')
            ->join('list_of_payment_services', 'check_up_results.id', '=', 'list_of_payment_services.check_up_result_id')
            ->join('detail_service_patients', 'list_of_payment_services.detail_service_patient_id', '=', 'detail_service_patients.id')
            ->join('price_services', 'detail_service_patients.price_service_id', '=', 'price_services.id')
            ->join('users', 'check_up_results.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->select(
                DB::raw("TRIM(SUM(detail_service_patients.price_overall))+0 as price_overall"));

        if ($request->branch_id && $request->user()->role == 'admin') {
            $price_overall_service = $price_overall_service->where('branches.id', '=', $request->branch_id);
        } elseif ($request->user()->role == 'dokter') {
            $price_overall_service = $price_overall_service->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->date_from && $request->date_to) {
            $price_overall_service = $price_overall_service->whereBetween(DB::raw('DATE(list_of_payment_services.updated_at)'), [$request->date_from, $request->date_to]);
        }
        $price_overall_service = $price_overall_service->first();

        $price_overall = $price_overall_service->price_overall + $price_overall_item->price_overall;

        $capital_price_item = DB::table('list_of_payments as lop')
            ->join('list_of_payment_medicine_groups as lopm', 'lop.id', '=', 'lopm.list_of_payment_id')
            ->join('price_medicine_groups as pmg', 'lopm.medicine_group_id', '=', 'pmg.id')
            ->join('users', 'lop.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->select(
                DB::raw("TRIM(SUM(pmg.capital_price))+0 as capital_price"));

        if ($request->branch_id && $request->user()->role == 'admin') {
            $capital_price_item = $capital_price_item->where('branches.id', '=', $request->branch_id);
        } elseif ($request->user()->role == 'dokter') {
            $capital_price_item = $capital_price_item->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->date_from && $request->date_to) {
            $capital_price_item = $capital_price_item->whereBetween(DB::raw('DATE(lopm.updated_at)'), [$request->date_from, $request->date_to]);
        }
        $capital_price_item = $capital_price_item->first();

        $capital_price_service = DB::table('list_of_payments')
            ->join('check_up_results', 'list_of_payments.check_up_result_id', '=', 'check_up_results.id')
            ->join('list_of_payment_services', 'check_up_results.id', '=', 'list_of_payment_services.check_up_result_id')
            ->join('detail_service_patients', 'list_of_payment_services.detail_service_patient_id', '=', 'detail_service_patients.id')
            ->join('price_services', 'detail_service_patients.price_service_id', '=', 'price_services.id')
            ->join('users', 'check_up_results.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->select(
                DB::raw("TRIM(SUM(price_services.capital_price * detail_service_patients.quantity))+0 as capital_price"));

        if ($request->branch_id && $request->user()->role == 'admin') {
            $capital_price_service = $capital_price_service->where('branches.id', '=', $request->branch_id);
        } elseif ($request->user()->role == 'dokter') {
            $capital_price_service = $capital_price_service->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->date_from && $request->date_to) {
            $capital_price_service = $capital_price_service->whereBetween(DB::raw('DATE(list_of_payment_services.updated_at)'), [$request->date_from, $request->date_to]);
        }
        $capital_price_service = $capital_price_service->first();

        $capital_price = $capital_price_service->capital_price + $capital_price_item->capital_price;

        $doctor_fee_item = DB::table('list_of_payments as lop')
            ->join('list_of_payment_medicine_groups as lopm', 'lop.id', '=', 'lopm.list_of_payment_id')
            ->join('price_medicine_groups as pmg', 'lopm.medicine_group_id', '=', 'pmg.id')
            ->join('users', 'lop.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->select(
                DB::raw("TRIM(SUM(pmg.doctor_fee))+0 as doctor_fee"));

        if ($request->branch_id && $request->user()->role == 'admin') {
            $doctor_fee_item = $doctor_fee_item->where('branches.id', '=', $request->branch_id);
        } elseif ($request->user()->role == 'dokter') {
            $doctor_fee_item = $doctor_fee_item->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->date_from && $request->date_to) {
            $doctor_fee_item = $doctor_fee_item->whereBetween(DB::raw('DATE(lopm.updated_at)'), [$request->date_from, $request->date_to]);
        }
        $doctor_fee_item = $doctor_fee_item->first();

        $doctor_fee_service = DB::table('list_of_payments')
            ->join('check_up_results', 'list_of_payments.check_up_result_id', '=', 'check_up_results.id')
            ->join('list_of_payment_services', 'check_up_results.id', '=', 'list_of_payment_services.check_up_result_id')
            ->join('detail_service_patients', 'list_of_payment_services.detail_service_patient_id', '=', 'detail_service_patients.id')
            ->join('price_services', 'detail_service_patients.price_service_id', '=', 'price_services.id')
            ->join('users', 'check_up_results.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->select(
                DB::raw("TRIM(SUM(price_services.doctor_fee * detail_service_patients.quantity))+0 as doctor_fee"));

        if ($request->branch_id && $request->user()->role == 'admin') {
            $doctor_fee_service = $doctor_fee_service->where('branches.id', '=', $request->branch_id);
        } elseif ($request->user()->role == 'dokter') {
            $doctor_fee_service = $doctor_fee_service->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->date_from && $request->date_to) {
            $doctor_fee_service = $doctor_fee_service->whereBetween(DB::raw('DATE(list_of_payment_services.updated_at)'), [$request->date_from, $request->date_to]);
        }
        $doctor_fee_service = $doctor_fee_service->first();

        $doctor_fee = $doctor_fee_item->doctor_fee + $doctor_fee_service->doctor_fee;

        $petshop_fee_item = DB::table('list_of_payments as lop')
            ->join('list_of_payment_medicine_groups as lopm', 'lop.id', '=', 'lopm.list_of_payment_id')
            ->join('price_medicine_groups as pmg', 'lopm.medicine_group_id', '=', 'pmg.id')
            ->join('users', 'lop.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->select(
                DB::raw("TRIM(SUM(pmg.petshop_fee))+0 as petshop_fee"));

        if ($request->branch_id && $request->user()->role == 'admin') {
            $petshop_fee_item = $petshop_fee_item->where('branches.id', '=', $request->branch_id);
        } elseif ($request->user()->role == 'dokter') {
            $petshop_fee_item = $petshop_fee_item->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->date_from && $request->date_to) {
            $petshop_fee_item = $petshop_fee_item->whereBetween(DB::raw('DATE(lopm.updated_at)'), [$request->date_from, $request->date_to]);
        }
        $petshop_fee_item = $petshop_fee_item->first();

        $petshop_fee_service = DB::table('list_of_payments')
            ->join('check_up_results', 'list_of_payments.check_up_result_id', '=', 'check_up_results.id')
            ->join('list_of_payment_services', 'check_up_results.id', '=', 'list_of_payment_services.check_up_result_id')
            ->join('detail_service_patients', 'list_of_payment_services.detail_service_patient_id', '=', 'detail_service_patients.id')
            ->join('price_services', 'detail_service_patients.price_service_id', '=', 'price_services.id')
            ->join('users', 'check_up_results.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->select(
                DB::raw("TRIM(SUM(price_services.petshop_fee * detail_service_patients.quantity))+0 as petshop_fee"));

        if ($request->branch_id && $request->user()->role == 'admin') {
            $petshop_fee_service = $petshop_fee_service->where('branches.id', '=', $request->branch_id);
        } elseif ($request->user()->role == 'dokter') {
            $petshop_fee_service = $petshop_fee_service->where('branches.id', '=', $request->user()->branch_id);
        }

        if ($request->date_from && $request->date_to) {
            $petshop_fee_service = $petshop_fee_service->whereBetween(DB::raw('DATE(list_of_payment_services.updated_at)'), [$request->date_from, $request->date_to]);
        }
        $petshop_fee_service = $petshop_fee_service->first();

        $petshop_fee = $petshop_fee_item->petshop_fee + $petshop_fee_service->petshop_fee;

        return response()->json([
            'data' => $data,
            'price_overall' => $price_overall,
            'capital_price' => $capital_price,
            'doctor_fee' => $doctor_fee,
            'petshop_fee' => $petshop_fee,
        ], 200);
    }

    public function detail(Request $request)
    {
        if ($request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $data = ListofPayments::find($request->id);

        if (is_null($data)) {

            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data Hasil Pemeriksaan tidak ditemukan!'],
            ], 404);
        }

        $user = DB::table('list_of_payments')
            ->join('users', 'list_of_payments.user_id', '=', 'users.id')
            ->select('users.id as user_id', 'users.fullname as fullname')
            ->where('users.id', '=', $data->user_id)
            ->first();

        $data->user = $user;

        $check_up_result = DB::table('check_up_results')
            ->where('id', '=', $data->check_up_result_id)
            ->first();

        $data->check_up_result = $check_up_result;

        $registration = DB::table('registrations')
            ->join('patients', 'registrations.patient_id', '=', 'patients.id')
            ->join('owners', 'patients.owner_id', '=', 'owners.id')
            ->select(
                'registrations.id_number as registration_number',
                'patients.id as patient_id',
                'patients.id_member as patient_number',
                'patients.pet_category',
                'patients.pet_name',
                'patients.pet_gender',
                'patients.pet_year_age',
                'patients.pet_month_age',
                DB::raw('(CASE WHEN patients.owner_name = "" THEN owners.owner_name ELSE patients.owner_name END) AS owner_name'),
                DB::raw('(CASE WHEN patients.owner_address = "" THEN owners.owner_address ELSE patients.owner_address END) AS owner_address'),
                DB::raw('(CASE WHEN patients.owner_phone_number = "" THEN owners.owner_phone_number ELSE patients.owner_phone_number END) AS owner_phone_number'),
                'registrations.complaint',
                'registrations.registrant')
            ->where('registrations.id', '=', $check_up_result->patient_registration_id)
            ->first();

        $data->registration = $registration;

        $list_of_payment_services = DB::table('list_of_payment_services')
            ->join('detail_service_patients', 'list_of_payment_services.detail_service_patient_id', '=', 'detail_service_patients.id')
            ->join('price_services', 'detail_service_patients.price_service_id', '=', 'price_services.id')
            ->join('list_of_services', 'price_services.list_of_services_id', '=', 'list_of_services.id')
            ->join('service_categories', 'list_of_services.service_category_id', '=', 'service_categories.id')
            ->join('users', 'detail_service_patients.user_id', '=', 'users.id')
            ->select('detail_service_patients.id as detail_service_patient_id', 'price_services.id as price_service_id',
                'list_of_services.id as list_of_service_id', 'list_of_services.service_name',
                'detail_service_patients.quantity',
                'service_categories.category_name',
                DB::raw("TRIM(detail_service_patients.price_overall )+0 as price_overall"),
                DB::raw("TRIM(price_services.selling_price)+0 as selling_price"),
                DB::raw("TRIM(price_services.capital_price * detail_service_patients.quantity)+0 as capital_price"),
                DB::raw("TRIM(price_services.doctor_fee * detail_service_patients.quantity)+0 as doctor_fee"),
                DB::raw("TRIM(price_services.petshop_fee * detail_service_patients.quantity)+0 as petshop_fee"),
                'users.fullname as created_by', DB::raw("DATE_FORMAT(detail_service_patients.created_at, '%d %b %Y') as created_at"))
            ->where('list_of_payment_services.check_up_result_id', '=', $data->check_up_result_id);

        if ($request->date_from && $request->date_to) {
            $list_of_payment_services = $list_of_payment_services
                ->whereBetween(DB::raw('DATE(list_of_payment_services.updated_at)'), [$request->date_from, $request->date_to]);
        }

        $list_of_payment_services = $list_of_payment_services->orderBy('list_of_payment_services.id', 'desc')
            ->get();

        $data['list_of_payment_services'] = $list_of_payment_services;

        $item = DB::table('list_of_payment_medicine_groups as lopm')
            ->join('price_medicine_groups as pmg', 'lopm.medicine_group_id', '=', 'pmg.id')
            ->join('medicine_groups', 'pmg.medicine_group_id', '=', 'medicine_groups.id')
            ->join('branches', 'medicine_groups.branch_id', '=', 'branches.id')
            ->select('lopm.id as id',
                'pmg.id as price_medicine_group_id',
                DB::raw("TRIM(pmg.selling_price)+0 as selling_price"),
                'lopm.medicine_group_id as medicine_group_id',
                'medicine_groups.group_name',
                'branches.id as branch_id',
                'branches.branch_name')
            ->where('lopm.list_of_payment_id', '=', $data->id);

        if ($request->date_from && $request->date_to) {
            $item = $item
                ->whereBetween(DB::raw('DATE(lopm.updated_at)'), [$request->date_from, $request->date_to]);
        }

        $item = $item->get();

        foreach ($item as $value) {

            $detail_item = DB::table('list_of_payment_items as lopi')
                ->join('price_items', 'lopi.price_item_id', '=', 'price_items.id')
                ->join('list_of_items', 'price_items.list_of_items_id', '=', 'list_of_items.id')
                ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
                ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
                ->join('users', 'lopi.user_id', '=', 'users.id')
                ->select('lopi.id as detail_item_patients_id',
                    'list_of_items.id as list_of_item_id',
                    'price_items.id as price_item_id',
                    'list_of_items.item_name',
                    'lopi.quantity',
                    DB::raw("TRIM(lopi.price_overall)+0 as price_overall"),
                    'unit_item.unit_name',
                    'category_item.category_name',
                    DB::raw("TRIM(price_items.selling_price)+0 as selling_price"),
                    DB::raw("TRIM(price_items.capital_price)+0 as capital_price"),
                    DB::raw("TRIM(price_items.doctor_fee)+0 as doctor_fee"),
                    DB::raw("TRIM(price_items.petshop_fee)+0 as petshop_fee"),
                    'users.fullname as created_by',
                    DB::raw("DATE_FORMAT(lopi.created_at, '%d %b %Y') as created_at"))
                ->where('lopi.list_of_payment_medicine_group_id', '=', $value->id)
                ->orderBy('lopi.id', 'asc')
                ->get();

            $value->list_of_medicine = $detail_item;
        }

        $data['item'] = $item;

        $inpatient = DB::table('in_patients')
            ->join('users', 'in_patients.user_id', '=', 'users.id')
            ->select('in_patients.description', DB::raw("DATE_FORMAT(in_patients.created_at, '%d %b %Y') as created_at"),
                'users.fullname as created_by')
            ->where('in_patients.check_up_result_id', '=', $data->check_up_result_id)
            ->get();

        $data['inpatient'] = $inpatient;

        return response()->json($data, 200);
    }

    public function download_excel(Request $request)
    {
        if ($request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $branch = "";

        if ($request->user()->role == 'admin') {
            $branch = $request->branch_id;
        } else {
            $branch = $request->user()->branch_id;
        }

        $date_from = '';
        $date_to = '';
        $filename = '';

        $branches = Branch::find($branch);

        if ($request->date_from && $request->date_to) {
            $date_from = \Carbon\Carbon::parse($request->date_from)->format('d-m-Y');
            $date_to = \Carbon\Carbon::parse($request->date_to)->format('d-m-Y');
            $filename = 'Laporan Keuangan Mingguan ' . $branches->branch_name . ' ' . $date_from . ' - ' . $date_to . '.xlsx';
        } else {
            $filename = 'Laporan Keuangan Mingguan ' . $branches->branch_name . '.xlsx';
        }

        return Excel::download(
            new LaporanKeuanganMingguan(
                $request->orderby,
                $request->column,
                $request->date_from,
                $request->date_to,
                $branch,
                'Laporan Keuangan Mingguan'), $filename);
    }
}

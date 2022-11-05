<?php

namespace App\Exports;

use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanKeuanganBulanan implements FromView, WithTitle
{

    protected $orderby;
    protected $column;
    protected $month;
    protected $year;
    protected $branch_id;
    protected $title_name;

    public function __construct($orderby, $column, $month, $year, $branch_id, $title_name)
    {
        $this->orderby = $orderby;
        $this->column = $column;
        $this->month = $month;
        $this->year = $year;
        $this->branch_id = $branch_id;
        $this->title_name = $title_name;
    }

    public function view(): View
    {

        $list_date = DB::table('list_of_payments as lop')
            ->join('users', 'lop.user_id', 'users.id')
            ->join('branches', 'users.branch_id', 'branches.id')
            ->select(DB::raw("DATE(lop.updated_at) as date"));

        if ($this->branch_id) {
            $list_date = $list_date->where('branches.id', '=', $this->branch_id);
        }

        if ($this->month && $this->year) {
            $list_date = $list_date->where(DB::raw('MONTH(lop.updated_at)'), '=', $this->month)
                ->where(DB::raw('YEAR(lop.updated_at)'), '=', $this->year);
        }

        $list_date = $list_date->groupby(DB::raw("DATE(lop.updated_at)"))
            ->get();

        foreach ($list_date as $result_data) {

            $item = DB::table('list_of_payments as lop')
                ->join('check_up_results as cur', 'lop.check_up_result_id', '=', 'cur.id')
                ->join('list_of_payment_medicine_groups as lopm', 'lopm.list_of_payment_id', '=', 'lop.id')
                ->join('price_medicine_groups as pmg', 'lopm.medicine_group_id', '=', 'pmg.id')
                ->join('medicine_groups', 'pmg.medicine_group_id', '=', 'medicine_groups.id')
                ->join('registrations as reg', 'cur.patient_registration_id', '=', 'reg.id')
                ->join('patients as p', 'reg.patient_id', '=', 'p.id')
                ->join('owners', 'p.owner_id', '=', 'owners.id')
                ->join('users', 'lop.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->join('payment_methods as pm', 'lopm.payment_method_id', '=', 'pm.id')

                ->select(
                    'lop.id as list_of_payment_id',
                    'lop.check_up_result_id',
                    'medicine_groups.group_name as item',
                    DB::raw("(CASE WHEN lopm.quantity = 0 THEN COUNT(pmg.id) ELSE lopm.quantity END)+0 as total_item"),
                    DB::raw("(TRIM(pmg.selling_price))+0 as each_price"),
                    DB::raw("(CASE WHEN lopm.quantity = 0 THEN TRIM(SUM(pmg.selling_price)) ELSE TRIM(SUM(pmg.selling_price * lopm.quantity)) END)+0 as price_overall"),
                    'lopm.detail_medicine_group_check_up_result_id as dmg',
                    DB::raw("(CASE WHEN lopm.quantity = 0 THEN TRIM(SUM(pmg.capital_price)) ELSE TRIM(SUM(pmg.capital_price * lopm.quantity)) END)+0 as capital_price"),
                    DB::raw("(CASE WHEN lopm.quantity = 0 THEN TRIM(SUM(pmg.selling_price)) ELSE TRIM(SUM(pmg.selling_price * lopm.quantity)) END)+0 as selling_price"),
                    DB::raw("(CASE WHEN lopm.quantity = 0 THEN TRIM(SUM(pmg.petshop_fee)) ELSE TRIM(SUM(pmg.petshop_fee * lopm.quantity)) END)+0 as petshop_fee"),
                    DB::raw("(CASE WHEN lopm.quantity = 0 THEN TRIM(SUM(pmg.doctor_fee)) ELSE TRIM(SUM(pmg.doctor_fee * lopm.quantity)) END)+0 as doctor_fee"),
                    DB::raw("TRIM(lopm.discount)+0 as discount"),
                    DB::raw("TRIM(lopm.amount_discount)+0 as amount_discount"),
                    DB::raw("TRIM(CASE WHEN lopm.quantity = 0 THEN TRIM(SUM(pmg.doctor_fee)) ELSE TRIM(SUM(pmg.doctor_fee * lopm.quantity)) END - lopm.amount_discount)+0 as after_discount"),
                    'p.pet_name as pet_name',
                    DB::raw('(CASE WHEN p.owner_name = "" THEN owners.owner_name ELSE p.owner_name END) AS owner_name'),
                    'branches.id as branchId',
                    DB::raw("DATE_FORMAT(lopm.updated_at, '%d/%m/%Y') as created_at"),
                    'pm.payment_name as payment_name',
                    DB::raw("'clinic' as data_category")
                )
                ->where(DB::raw("DATE(lopm.updated_at)"), '=', $result_data->date);

            if ($this->branch_id) {
                $item = $item->where('branches.id', '=', $this->branch_id);
            }

            $item = $item->groupBy('lopm.detail_medicine_group_check_up_result_id')
                ->orderBy('cur.id', 'asc');

            $service = DB::table('list_of_payments')
                ->join('check_up_results', 'list_of_payments.check_up_result_id', '=', 'check_up_results.id')
                ->join('list_of_payment_services', 'check_up_results.id', '=', 'list_of_payment_services.check_up_result_id')
                ->join('detail_service_patients', 'list_of_payment_services.detail_service_patient_id', '=', 'detail_service_patients.id')
                ->join('price_services', 'detail_service_patients.price_service_id', '=', 'price_services.id')
                ->join('list_of_services', 'price_services.list_of_services_id', '=', 'list_of_services.id')
                ->join('registrations', 'check_up_results.patient_registration_id', '=', 'registrations.id')
                ->join('patients', 'registrations.patient_id', '=', 'patients.id')
                ->join('owners', 'patients.owner_id', '=', 'owners.id')
                ->join('users', 'check_up_results.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->join('payment_methods as pm', 'list_of_payment_services.payment_method_id', '=', 'pm.id')

                ->select(
                    'list_of_payments.id as list_of_payment_id',
                    'list_of_payments.check_up_result_id',
                    'list_of_services.service_name as item',
                    'detail_service_patients.quantity as total_item',
                    DB::raw("TRIM(price_services.selling_price)+0 as each_price"),
                    DB::raw("TRIM(price_services.selling_price * detail_service_patients.quantity)+0 as price_overall"),
                    'list_of_payments.check_up_result_id as dmg',
                    DB::raw("TRIM(capital_price * detail_service_patients.quantity)+0 as capital_price"),
                    DB::raw("TRIM(detail_service_patients.price_overall)+0 as selling_price"),
                    DB::raw("TRIM(price_services.petshop_fee * detail_service_patients.quantity)+0 as petshop_fee"),
                    DB::raw("TRIM(price_services.doctor_fee * detail_service_patients.quantity)+0 as doctor_fee"),
                    DB::raw("TRIM(list_of_payment_services.discount)+0 as discount"),
                    DB::raw("TRIM(list_of_payment_services.amount_discount)+0 as amount_discount"),
                    DB::raw("TRIM((price_services.doctor_fee * detail_service_patients.quantity) - list_of_payment_services.amount_discount)+0 as after_discount"),
                    'patients.pet_name as pet_name',
                    DB::raw('(CASE WHEN patients.owner_name = "" THEN owners.owner_name ELSE patients.owner_name END) AS owner_name'),
                    'branches.id as branchId',
                    DB::raw("DATE_FORMAT(list_of_payment_services.updated_at, '%d/%m/%Y') as created_at"),
                    'pm.payment_name as payment_name',
                    DB::raw("'clinic' as data_category")
                )
                ->where(DB::raw("DATE(list_of_payment_services.updated_at)"), '=', $result_data->date)
                ->orderBy('check_up_results.id', 'asc');

            if ($this->branch_id) {
                $service = $service->where('branches.id', '=', $this->branch_id);
            }

            $pet_shop_clinic = DB::table('list_of_payments as lop')
                ->join('payment_petshop_with_clinics as ppwc', 'lop.id', '=', 'ppwc.list_of_payment_id')
                ->join('price_item_pet_shops as pip', 'ppwc.price_item_pet_shop_id', '=', 'pip.id')
                ->join('list_of_item_pet_shops as loi', 'pip.list_of_item_pet_shop_id', '=', 'loi.id')
                ->join('users', 'ppwc.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->join('payment_methods as pm', 'ppwc.payment_method_id', '=', 'pm.id')
                ->join('check_up_results as cur', 'lop.check_up_result_id', '=', 'cur.id')
                ->join('registrations as reg', 'cur.patient_registration_id', '=', 'reg.id')
                ->join('patients', 'reg.patient_id', '=', 'patients.id')
                ->join('owners', 'patients.owner_id', '=', 'owners.id')
                ->select(
                    'lop.id', // 'list_of_payment_id',
                    DB::raw("'' as check_up_result_id"), // 'check_up_result_id',
                    'loi.item_name as item', // 'action',
                    'ppwc.total_item as total_item', //total_item
                    DB::raw("TRIM(pip.selling_price)+0 as each_price"), //each_price
                    DB::raw("TRIM(pip.selling_price * ppwc.total_item)+0 as price_overall"), //price_overall
                    DB::raw("'' as dmg"), //dmg
                    DB::raw("TRIM(pip.capital_price * ppwc.total_item)+0 as capital_price"), // 'capital_price',
                    DB::raw("TRIM(pip.selling_price * ppwc.total_item)+0 as selling_price"), // 'selling_price',
                    DB::raw("0 as petshop_fee"), // 'petshop_fee',
                    DB::raw("TRIM(pip.profit * ppwc.total_item)+0 as doctor_fee"), // 'doctor_fee',
                    DB::raw("0 as discount"), // 'discount',
                    DB::raw("0 as amount_discount"), // 'amount_discount',
                    DB::raw("0 as after_discount"), // 'after_discount',
                    'patients.pet_name as pet_name',
                    DB::raw('(CASE WHEN patients.owner_name = "" THEN owners.owner_name ELSE patients.owner_name END) AS owner_name'), // 'owner_name',
                    'branches.id as branchId', // 'branchId',
                    DB::raw("DATE_FORMAT(ppwc.created_at, '%d/%m/%Y') as created_at"), // 'created_at',
                    'pm.payment_name as payment_name', // 'payment_name'
                    DB::raw("'clinic' as data_category")
                )
                ->where(DB::raw("DATE(ppwc.created_at)"), '=', $result_data->date)
                ->orderBy('ppwc.id', 'asc');

            if ($this->branch_id) {
                $pet_shop_clinic = $pet_shop_clinic->where('branches.id', '=', $this->branch_id);
            }

            $pet_shops = DB::table('master_payment_petshops as mp')
                ->join('payment_petshops as pp', 'pp.master_payment_petshop_id', 'mp.id')
                ->join('price_item_pet_shops as pip', 'pp.price_item_pet_shop_id', 'pip.id')
                ->join('list_of_item_pet_shops as loi', 'pip.list_of_item_pet_shop_id', 'loi.id')
                ->join('users', 'pp.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->join('payment_methods as pm', 'mp.payment_method_id', '=', 'pm.id')
                ->select(
                    'pp.id', // 'list_of_payment_id',
                    DB::raw("'' as check_up_result_id"), // 'check_up_result_id',
                    'loi.item_name as item', // 'action',
                    'pp.total_item as total_item', //total_item
                    DB::raw("TRIM(pip.selling_price)+0 as each_price"), //each_price
                    DB::raw("TRIM(pip.selling_price * pp.total_item)+0 as price_overall"), //price_overall
                    //DB::raw("TRIM(capital_price * detail_service_patients.quantity)+0 as capital_price"),
                    DB::raw("'' as dmg"), //dmg
                    DB::raw("TRIM(pip.capital_price * pp.total_item)+0 as capital_price"), // 'capital_price',
                    DB::raw("TRIM(pip.selling_price * pp.total_item)+0 as selling_price"), // 'selling_price',
                    DB::raw("0 as petshop_fee"), // 'petshop_fee',
                    DB::raw("TRIM(pip.profit * pp.total_item)+0 as doctor_fee"), // 'doctor_fee',
                    DB::raw("0 as discount"), // 'discount',
                    DB::raw("0 as amount_discount"), // 'amount_discount',
                    DB::raw("TRIM(pip.profit * pp.total_item)+0 as after_discount"), // 'after_discount',
                    'loi.item_name as pet_name', // pet name
                    'mp.payment_number as owner_name', // 'owner_name',
                    'branches.id as branchId', // 'branchId',
                    DB::raw("DATE_FORMAT(pp.created_at, '%d/%m/%Y') as created_at"), // 'created_at',
                    'pm.payment_name as payment_name', // 'payment_name'
                    DB::raw("'shop' as data_category")
                )
                ->where(DB::raw("DATE(pp.created_at)"), '=', $result_data->date)
                ->orderBy('pp.id', 'asc');

            if ($this->branch_id) {
                $pet_shops = $pet_shops->where('branches.id', '=', $this->branch_id);
            }

            $service = $service->union($item)
                ->union($pet_shop_clinic)
                ->union($pet_shops);

            $data = DB::query()->fromSub($service, 'p_pn')
                ->select(
                    'list_of_payment_id',
                    'check_up_result_id',
                    'item',
                    'total_item',
                    'each_price',
                    'price_overall',
                    'capital_price',
                    'selling_price',
                    'petshop_fee',
                    'doctor_fee',
                    'discount',
                    'amount_discount',
                    'after_discount',
                    'pet_name',
                    'owner_name',
                    'branchId',
                    'created_at',
                    'payment_name',
                    'data_category');

            if ($this->orderby) {

                $data = $data->orderBy($this->column, $this->orderby);
            } else {
                $data = $data->orderBy('list_of_payment_id', 'desc');
            }

            $data = $data->orderBy('check_up_result_id', 'asc')
                ->get();

            $array[] = $data;

            $expenses = DB::table('expenses as e')
                ->join('users as u', 'e.user_id_spender', '=', 'u.id')
                ->join('branches as b', 'u.branch_id', '=', 'b.id')
                ->select(DB::raw("TRIM(SUM(IFNULL(e.amount_overall,0)))+0 as amount_overall"));

            if ($this->branch_id) {
                $expenses = $expenses->where('b.id', '=', $this->branch_id);
            }

            $expenses = $expenses->where(DB::raw('DATE(e.date_spend)'), '=', $result_data->date);

            $expenses = $expenses->first();

            $total_expenses = 0;

            if (!is_null($expenses->amount_overall)) {

                $total_expenses = $expenses->amount_overall;
            }

            $expenses_per_day[] = $total_expenses;
        }

        $payment_method = DB::table('payment_methods')
            ->select('id', 'payment_name')
            ->where('id', '!=', 0)
            ->get();

        foreach ($payment_method as $data_pay) {

            $total_each_payment = 0;

            $count_item = DB::table('list_of_payments as lop')
                ->join('list_of_payment_medicine_groups as lopm', 'lop.id', '=', 'lopm.list_of_payment_id')
                ->join('price_medicine_groups as pmg', 'lopm.medicine_group_id', '=', 'pmg.id')
                ->join('users', 'lop.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->join('payment_methods as pm', 'lopm.payment_method_id', '=', 'pm.id')
                ->select(
                    DB::raw("(CASE WHEN lopm.quantity = 0 THEN TRIM(SUM(pmg.selling_price)) ELSE TRIM(SUM(pmg.selling_price * lopm.quantity)) END)+0 as price_overall"));

            $count_item = $count_item->where('lopm.payment_method_id', '=', $data_pay->id);

            if ($this->branch_id) {
                $count_item = $count_item->where('branches.id', '=', $this->branch_id);
            }

            if ($this->month && $this->year) {
                $count_item = $count_item->where(DB::raw('MONTH(lopm.updated_at)'), '=', $this->month)
                    ->where(DB::raw('YEAR(lopm.updated_at)'), '=', $this->year);
            }
            $count_item = $count_item->first();

            $total_each_payment += $count_item->price_overall;

            //service
            $count_service = DB::table('list_of_payments')
                ->join('check_up_results', 'list_of_payments.check_up_result_id', '=', 'check_up_results.id')
                ->join('list_of_payment_services', 'check_up_results.id', '=', 'list_of_payment_services.check_up_result_id')
                ->join('detail_service_patients', 'list_of_payment_services.detail_service_patient_id', '=', 'detail_service_patients.id')
                ->join('price_services', 'detail_service_patients.price_service_id', '=', 'price_services.id')
                ->join('users', 'check_up_results.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->join('payment_methods as pm', 'list_of_payment_services.payment_method_id', '=', 'pm.id')
                ->select(
                    DB::raw("TRIM(SUM(detail_service_patients.price_overall))+0 as price_overall"));

            $count_service = $count_service->where('list_of_payment_services.payment_method_id', '=', $data_pay->id);

            if ($this->branch_id) {
                $count_service = $count_service->where('branches.id', '=', $this->branch_id);
            }

            if ($this->month && $this->year) {
                $count_service = $count_service->where(DB::raw('MONTH(list_of_payment_services.updated_at)'), '=', $this->month)
                    ->where(DB::raw('YEAR(list_of_payment_services.updated_at)'), '=', $this->year);
            }
            $count_service = $count_service->first();

            $total_each_payment += $count_service->price_overall;

            //pet shop with clinic
            $count_pet_shop_clinic = DB::table('list_of_payments as lop')
                ->join('payment_petshop_with_clinics as ppwc', 'lop.id', '=', 'ppwc.list_of_payment_id')
                ->join('price_item_pet_shops as pip', 'ppwc.price_item_pet_shop_id', '=', 'pip.id')
                ->join('list_of_item_pet_shops as loi', 'pip.list_of_item_pet_shop_id', '=', 'loi.id')
                ->join('users', 'ppwc.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->join('payment_methods as pm', 'ppwc.payment_method_id', '=', 'pm.id')
                ->select(DB::raw("TRIM(SUM(pip.selling_price * ppwc.total_item))+0 as price_overall"));

            $count_pet_shop_clinic = $count_pet_shop_clinic->where('ppwc.payment_method_id', '=', $data_pay->id);

            if ($this->branch_id) {
                $count_pet_shop_clinic = $count_pet_shop_clinic->where('branches.id', '=', $this->branch_id);
            }

            if ($this->month && $this->year) {
                $count_pet_shop_clinic = $count_pet_shop_clinic->where(DB::raw('MONTH(ppwc.created_at)'), '=', $this->month)
                    ->where(DB::raw('YEAR(ppwc.created_at)'), '=', $this->year);
            }
            $count_pet_shop_clinic = $count_pet_shop_clinic->first();

            $total_each_payment += $count_pet_shop_clinic->price_overall;

            //pet shop
            $count_pet_shop = DB::table('master_payment_petshops as mp')
                ->join('payment_petshops as pp', 'pp.master_payment_petshop_id', 'mp.id')
                ->join('price_item_pet_shops as pip', 'pp.price_item_pet_shop_id', 'pip.id')
                ->join('list_of_item_pet_shops as loi', 'pip.list_of_item_pet_shop_id', 'loi.id')
                ->join('users', 'pp.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->join('payment_methods as pm', 'mp.payment_method_id', '=', 'pm.id')
                ->select(DB::raw("TRIM(SUM(pip.selling_price * pp.total_item))+0 as price_overall"));

            $count_pet_shop = $count_pet_shop->where('mp.payment_method_id', '=', $data_pay->id);

            if ($this->branch_id) {
                $count_pet_shop = $count_pet_shop->where('branches.id', '=', $this->branch_id);
            }

            if ($this->month && $this->year) {
                $count_pet_shop = $count_pet_shop->where(DB::raw('MONTH(pp.created_at)'), '=', $this->month)
                    ->where(DB::raw('YEAR(pp.created_at)'), '=', $this->year);
            }
            $count_pet_shop = $count_pet_shop->first();

            $total_each_payment += $count_pet_shop->price_overall;

            $arr_payment[] = array('amount' => $total_each_payment, 'payment_name' => $data_pay->payment_name);
        }

        return view('laporan-keuangan', [
            'data' => $array,
            'expenses' => $expenses_per_day,
            'data_payment_method' => $arr_payment,
        ]);
    }

    public function title(): string
    {
        return $this->title_name;
    }
}

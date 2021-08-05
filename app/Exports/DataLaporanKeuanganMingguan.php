<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class DataLaporanKeuanganMingguan implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $orderby;
    protected $column;
    protected $date_from;
    protected $date_to;
    protected $branch_id;

    public function __construct($orderby, $column, $date_from, $date_to, $branch_id)
    {
        $this->orderby = $orderby;
        $this->column = $column;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->branch_id = $branch_id;
    }

    public function collection()
    {
        DB::statement(DB::raw('set @rownum=0'));

        $item_sum = DB::table('list_of_payments as lop')
            ->join('check_up_results as cur', 'lop.check_up_result_id', '=', 'cur.id')
            ->join('list_of_payment_medicine_groups as lopm', 'lopm.list_of_payment_id', '=', 'lop.id')
        //->join('list_of_payment_items as lipi', 'lipi.list_of_payment_medicine_group_id', '=', 'lopm.id')
            ->join('price_medicine_groups as pmg', 'lopm.medicine_group_id', '=', 'pmg.id')
        //->join('price_items as pi', 'lipi.price_item_id', '=', 'pi.id')
            ->join('registrations as reg', 'cur.patient_registration_id', '=', 'reg.id')
            ->join('patients as pa', 'reg.patient_id', '=', 'pa.id')
            ->join('users', 'lop.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')

            ->select(
                DB::raw("TRIM(SUM(pmg.selling_price))+0 as price_overall"),
                DB::raw("TRIM(SUM(pmg.capital_price))+0 as capital_price"),
                DB::raw("TRIM(SUM(pmg.doctor_fee))+0 as doctor_fee"),
                DB::raw("TRIM(SUM(pmg.petshop_fee))+0 as petshop_fee"));

        if ($this->branch_id) {
            $item_sum = $item_sum->where('branches.id', '=', $this->branch_id);
        }

        if ($this->date_from && $this->date_to) {
            $item_sum = $item_sum->whereBetween(DB::raw('DATE(lop.updated_at)'), [$this->date_from, $this->date_to]);
        }

        $item_sum = $item_sum->groupBy('lop.check_up_result_id');

        $service_sum = DB::table('list_of_payments')
            ->join('check_up_results', 'list_of_payments.check_up_result_id', '=', 'check_up_results.id')
            ->join('list_of_payment_services', 'check_up_results.id', '=', 'list_of_payment_services.check_up_result_id')
            ->join('detail_service_patients', 'list_of_payment_services.detail_service_patient_id', '=', 'detail_service_patients.id')
            ->join('price_services', 'detail_service_patients.price_service_id', '=', 'price_services.id')
            ->join('users', 'check_up_results.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')

            ->select(
                DB::raw("TRIM(SUM(detail_service_patients.price_overall))+0 as price_overall"),
                DB::raw("TRIM(SUM(price_services.capital_price * detail_service_patients.quantity))+0 as capital_price"),
                DB::raw("TRIM(SUM(price_services.doctor_fee * detail_service_patients.quantity))+0 as doctor_fee"),
                DB::raw("TRIM(SUM(price_services.petshop_fee * detail_service_patients.quantity))+0 as petshop_fee"));

        if ($this->branch_id) {
            $service_sum = $service_sum->where('branches.id', '=', $this->branch_id);
        }

        if ($this->date_from && $this->date_to) {
            $service_sum = $service_sum->whereBetween(DB::raw('DATE(list_of_payments.updated_at)'), [$this->date_from, $this->date_to]);
        }

        $service_sum = $service_sum
        //->groupBy('list_of_payments.check_up_result_id')
            ->union($item_sum);

        $data = DB::query()->fromSub($service_sum, 't')
            ->select(DB::raw('null as rownum'),
                DB::raw('null as list_of_payment_id'),
                DB::raw('null as check_up_result_id'),
                DB::raw('null as registration_number'),
                DB::raw('null as patient_number'),
                DB::raw('null as pet_category'),
                DB::raw('null as pet_name'),
                DB::raw('null as complaint'),
                DB::raw("TRIM(SUM(price_overall))+0 as price_overall"),
                DB::raw("TRIM(SUM(capital_price))+0 as capital_price"),
                DB::raw("TRIM(SUM(doctor_fee))+0 as doctor_fee"),
                DB::raw("TRIM(SUM(petshop_fee))+0 as petshop_fee"),
                DB::raw('null as created_by'),
                DB::raw('null as created_at'),
                DB::raw('"Total" as status_outpatient_inpatient'));

        $item = DB::table('list_of_payments as lop')
            ->join('check_up_results as cur', 'lop.check_up_result_id', '=', 'cur.id')
            ->join('list_of_payment_medicine_groups as lopm', 'lopm.list_of_payment_id', '=', 'lop.id')
        //->join('list_of_payment_items as lipi', 'lipi.list_of_payment_medicine_group_id', '=', 'lopm.id')
            ->join('price_medicine_groups as pmg', 'lopm.medicine_group_id', '=', 'pmg.id')
        //->join('price_items as pi', 'lipi.price_item_id', '=', 'pi.id')
            ->join('registrations as reg', 'cur.patient_registration_id', '=', 'reg.id')
            ->join('patients as pa', 'reg.patient_id', '=', 'pa.id')
            ->join('users', 'lop.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')

            ->select(
                DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                'lop.id as list_of_payment_id',
                'lop.check_up_result_id as check_up_result_id',
                'reg.id_number as registration_number',
                'pa.id_member as patient_number',
                'pa.pet_category',
                'pa.pet_name',
                'reg.complaint',
                // DB::raw("TRIM(SUM(lipi.price_overall))+0 as price_overall"),
                // DB::raw("TRIM(SUM(pi.capital_price * lipi.quantity))+0 as capital_price"),
                // DB::raw("TRIM(SUM(pi.doctor_fee * lipi.quantity))+0 as doctor_fee"),
                // DB::raw("TRIM(SUM(pi.petshop_fee * lipi.quantity))+0 as petshop_fee"),

                DB::raw("TRIM(SUM(pmg.selling_price))+0 as price_overall"),
                DB::raw("TRIM(SUM(pmg.capital_price))+0 as capital_price"),
                DB::raw("TRIM(SUM(pmg.doctor_fee))+0 as doctor_fee"),
                DB::raw("TRIM(SUM(pmg.petshop_fee))+0 as petshop_fee"),
                'users.fullname as created_by',
                'lop.updated_at as created_at',
                'branches.id as branchId',
                'cur.status_outpatient_inpatient')
            ->groupBy('lop.check_up_result_id');

        $service = DB::table('list_of_payments')
            ->join('check_up_results', 'list_of_payments.check_up_result_id', '=', 'check_up_results.id')
            ->join('list_of_payment_services', 'check_up_results.id', '=', 'list_of_payment_services.check_up_result_id')
            ->join('detail_service_patients', 'list_of_payment_services.detail_service_patient_id', '=', 'detail_service_patients.id')
            ->join('price_services', 'detail_service_patients.price_service_id', '=', 'price_services.id')
            ->join('registrations', 'check_up_results.patient_registration_id', '=', 'registrations.id')
            ->join('patients', 'registrations.patient_id', '=', 'patients.id')
            ->join('users', 'check_up_results.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')

            ->select(DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'list_of_payments.id as list_of_payment_id', 'list_of_payments.check_up_result_id as check_up_result_id',
                'registrations.id_number as registration_number',
                'patients.id_member as patient_number', 'patients.pet_category', 'patients.pet_name', 'registrations.complaint',
                DB::raw("TRIM(SUM(detail_service_patients.price_overall))+0 as price_overall"),
                DB::raw("TRIM(SUM(price_services.capital_price * detail_service_patients.quantity))+0 as capital_price"),
                DB::raw("TRIM(SUM(price_services.doctor_fee * detail_service_patients.quantity))+0 as doctor_fee"),
                DB::raw("TRIM(SUM(price_services.petshop_fee * detail_service_patients.quantity))+0 as petshop_fee"),
                'users.fullname as created_by', 'list_of_payments.updated_at as created_at',
                'branches.id as branchId', 'check_up_results.status_outpatient_inpatient as status_outpatient_inpatient')
            ->groupBy('list_of_payments.check_up_result_id')
            ->union($item);

        $data2 = DB::query()->fromSub($service, 'p_pn')
            ->select('rownum',
                'list_of_payment_id',
                'check_up_result_id',
                'registration_number',
                'patient_number',
                'pet_category',
                'pet_name',
                'complaint',
                DB::raw("TRIM(SUM(price_overall))+0 as price_overall"),
                DB::raw("TRIM(SUM(capital_price))+0 as capital_price"),
                DB::raw("TRIM(SUM(doctor_fee))+0 as doctor_fee"),
                DB::raw("TRIM(SUM(petshop_fee))+0 as petshop_fee"),
                'created_by',
                DB::raw("DATE_FORMAT(created_at, '%d %b %Y') as created_at"),
                DB::raw('(CASE WHEN status_outpatient_inpatient = 1 THEN "Rawat Inap" ELSE "Rawat Jalan" END) AS status_outpatient_inpatient'));

        if ($this->branch_id) {
            $data2 = $data2->where('branchId', '=', $this->branch_id);
        }

        if ($this->date_from && $this->date_to) {
            $data2 = $data2->whereBetween(DB::raw('DATE(created_at)'), [$this->date_from, $this->date_to]);
        }

        if ($this->orderby) {

            $data2 = $data2->orderBy($this->column, $this->orderby);
        } else {
            $data2 = $data2->orderBy('list_of_payment_id', 'desc');
        }

        $data2 = $data2->groupBy('check_up_result_id')
            ->union($data)
            ->get();

        return $data2;
    }

    public function headings(): array
    {
        return [
            ['No.', 'No. Registrasi', 'No. Pasien', 'Jenis Hewan', 'Nama Hewan', 'Keluhan', 'Perawatan', 'Total Keseluruhan',
                'Harga Modal Keseluruhan', 'Fee Dokter Keseluruhan', 'Fee Petshop Keseluruhan', 'Dibuat Oleh', 'Tanggal Dibuat'],
        ];
    }

    public function title(): string
    {
        return 'Laporan Keuangan Mingguan';
    }

    public function map($list_of_payments): array
    {
        $res = [
            [$list_of_payments->rownum,
                $list_of_payments->registration_number,
                $list_of_payments->patient_number,
                $list_of_payments->pet_category,
                $list_of_payments->pet_name,
                $list_of_payments->complaint,
                $list_of_payments->status_outpatient_inpatient,
                number_format($list_of_payments->price_overall, 2, ".", ","),
                number_format($list_of_payments->capital_price, 2, ".", ","),
                number_format($list_of_payments->doctor_fee, 2, ".", ","),
                number_format($list_of_payments->petshop_fee, 2, ".", ","),
                $list_of_payments->created_by,
                $list_of_payments->created_at,
            ],
        ];
        return $res;
    }
}

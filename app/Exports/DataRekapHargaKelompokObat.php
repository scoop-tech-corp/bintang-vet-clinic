<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class DataRekapHargaKelompokObat implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle, WithMapping
{
    protected $orderby;
    protected $column;
    protected $keyword;
    protected $branch_id;
    protected $role;

    public function __construct($orderby, $column, $keyword, $role, $branch_id)
    {
        $this->orderby = $orderby;
        $this->column = $column;
        $this->keyword = $keyword;
        $this->branch_id = $branch_id;
        $this->role = $role;

    }

    public function collection()
    {
        $price_medicine_groups = DB::table('price_medicine_groups')
            ->join('users', 'price_medicine_groups.user_id', '=', 'users.id')
            ->join('medicine_groups', 'price_medicine_groups.medicine_group_id', '=', 'medicine_groups.id')
            ->join('branches', 'medicine_groups.branch_id', '=', 'branches.id')
            ->select(
                'medicine_groups.group_name',
                'branches.branch_name',
                DB::raw("TRIM(price_medicine_groups.selling_price)+0 as selling_price"),
                DB::raw("TRIM(price_medicine_groups.capital_price)+0 as capital_price"),
                DB::raw("TRIM(price_medicine_groups.doctor_fee)+0 as doctor_fee"),
                DB::raw("TRIM(price_medicine_groups.petshop_fee)+0 as petshop_fee"),
                'users.fullname as created_by',
                DB::raw("DATE_FORMAT(price_medicine_groups.created_at, '%d %b %Y') as created_at"))
            ->where('price_medicine_groups.isDeleted', '=', 0);

        if ($this->branch_id && $this->role == 'admin') {
            $price_medicine_groups = $price_medicine_groups->where('branches.id', '=', $this->branch_id);
        }

        if ($this->role == 'dokter' || $this->role == 'resepsionis') {
            $price_medicine_groups = $price_medicine_groups->where('branches.id', '=', $this->branch_id);
        }

        if ($this->keyword) {

            $price_medicine_groups = $price_medicine_groups
                ->where('medicine_groups.group_name', 'like', '%' . $this->keyword . '%')
                ->orwhere('branches.branch_name', 'like', '%' . $this->keyword . '%')
                ->orwhere('users.fullname', 'like', '%' . $this->keyword . '%')
                ->orwhere('price_medicine_groups.created_at', 'like', '%' . $this->keyword . '%');
        }

        if ($this->orderby) {
            $price_medicine_groups = $price_medicine_groups->orderBy($this->column, $this->orderby);
        }

        $price_medicine_groups = $price_medicine_groups->orderBy('price_medicine_groups.id', 'desc');

        $price_medicine_groups = $price_medicine_groups->get();

        $val = 1;
        foreach ($price_medicine_groups as $key) {
            $key->number = $val;
            $val++;
        }

        return $price_medicine_groups;
    }

    public function headings(): array
    {
        return [
            ['No.', 'Kelompok Obat', 'Cabang', 'Harga Jual', 'Harga Modal', 'Fee Dokter', 'Fee Petshop', 'Dibuat Oleh',
                'Tanggal Dibuat'],
        ];
    }

    public function title(): string
    {
        return 'Data Rekap';
    }

    public function map($price_medicine_groups): array
    {
        $res = [
            [
                $price_medicine_groups->number,
                $price_medicine_groups->group_name,
                $price_medicine_groups->branch_name,
                number_format($price_medicine_groups->selling_price, 2, ".", ","),
                number_format($price_medicine_groups->capital_price, 2, ".", ","),
                number_format($price_medicine_groups->doctor_fee, 2, ".", ","),
                number_format($price_medicine_groups->petshop_fee, 2, ".", ","),
                $price_medicine_groups->created_by,
                $price_medicine_groups->created_at,
            ],
        ];
        return $res;
    }
}

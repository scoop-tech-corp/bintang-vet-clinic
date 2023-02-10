<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class DataRekapDaftarBarangPetShop implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle, WithMapping
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
        $item = DB::table('list_of_item_pet_shops as loi')
            ->join('users as u', 'loi.user_id', '=', 'u.id')
            ->join('branches as b', 'loi.branch_id', '=', 'b.id')
            ->select(
                'loi.item_name',
                DB::raw("TRIM(loi.total_item)+0 as total_item"),
                DB::raw("TRIM(loi.limit_item)+0 as limit_item"),
                DB::raw("DATE_FORMAT(loi.expired_date, '%d %b %Y') as expired_date"),
                'b.branch_name',
                'u.fullname as created_by',
                DB::raw("DATE_FORMAT(loi.created_at, '%d %b %Y') as created_at"))
            ->where('loi.isDeleted', '=', 0);

        if ($this->branch_id && $this->role == 'admin') {
            $item = $item->where('loi.branch_id', '=', $this->branch_id);
        }

        if ($this->role == 'dokter' || $this->role == 'resepsionis') {
            $item = $item->where('loi.branch_id', '=', $this->branch_id);
        }

        if ($this->orderby) {
            $item = $item->orderBy($this->column, $this->orderby);
        }

        $item = $item->orderBy('loi.id', 'desc');

        $item = $item->get();

        $val = 1;
        foreach ($item as $key) {
            $key->number = $val;
            $val++;
        }

        return $item;
    }

    public function headings(): array
    {
        return [
            ['No.', 'Nama Barang', 'Jumlah Barang','Limit Barang','Tanggal Kedaluwarsa', 'Cabang', 'Dibuat Oleh',
                'Tanggal Dibuat'],
        ];
    }

    public function title(): string
    {
        return 'Data Rekap';
    }

    public function map($item): array
    {
        $res = [
            [
                $item->number,
                $item->item_name,
                strval($item->total_item),
                strval($item->limit_item),
                $item->expired_date,
                $item->branch_name,
                $item->created_by,
                $item->created_at,
            ],
        ];
        return $res;
    }
}

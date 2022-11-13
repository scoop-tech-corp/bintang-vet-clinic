<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class DataDaftarBarangPetShop implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = DB::table('list_of_item_pet_shops as loi')
            ->join('users', 'loi.user_id', '=', 'users.id')
            ->join('branches', 'loi.branch_id', '=', 'branches.id')
            ->select('loi.id', 'loi.item_name', 'loi.total_item',
                'branches.id as branch_id', 'branches.branch_name', 'users.id as user_id', 'users.fullname as created_by',
                DB::raw("DATE_FORMAT(loi.created_at, '%d %b %Y') as created_at"))
            ->where('loi.isDeleted', '=', 0)
            ->get();

        return $data;
    }

    public function headings(): array
    {
        return [
            ['Kode Daftar Barang', 'Nama Barang', 'Jumlah Barang', 'Kode Cabang', 'Nama Cabang'],
        ];
    }

    public function title(): string
    {
        return 'Data Barang Pet Shop';
    }

    public function map($list_of_items): array
    {
        return [
            $list_of_items->id,
            $list_of_items->item_name,
            $list_of_items->total_item,
            $list_of_items->branch_id,
            $list_of_items->branch_name,
        ];
    }
}

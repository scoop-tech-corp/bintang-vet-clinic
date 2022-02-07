<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class DataKelompokObat implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = DB::table('medicine_groups as mg')
            ->join('branches as b', 'mg.branch_id', 'b.id')
            ->select('mg.id as id', 'mg.group_name', 'b.branch_name')
            ->where('mg.isDeleted', '=', 0)
            ->get();

        return $data;
    }

    public function headings(): array
    {
        return [
            ['Kode Kelompok Obat', 'Nama Kelompok Obat', 'Cabang'],
        ];
    }

    public function title(): string
    {
        return 'Daftar Kelompok Obat';
    }

    public function map($branch): array
    {
        return [
            $branch->id,
            $branch->group_name,
            $branch->branch_name,
        ];
    }
}

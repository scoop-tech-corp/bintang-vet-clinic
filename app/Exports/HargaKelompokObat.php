<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class HargaKelompokObat implements ShouldAutoSize, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return [
            ['Kode Kelompok Obat','Harga Jual','Harga Modal','Fee Dokter','Fee Petshop'],
        ];
    }

    public function title(): string
    {
        return 'Harga Kelompok Obat';
    }
}

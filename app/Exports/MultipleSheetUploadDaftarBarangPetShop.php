<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use App\Exports\Cabang;
use App\Exports\DaftarBarangPetshop;

class MultipleSheetUploadDaftarBarangPetShop implements WithMultipleSheets
{
    use Exportable;

    protected $sheets;

    public function __construct()
    {
        
    }

    public function array(): array
    {
        return $this->sheets;
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets = [
            new DaftarBarangPetShop(),
            new Cabang()
        ];

        return $sheets;
    }
}

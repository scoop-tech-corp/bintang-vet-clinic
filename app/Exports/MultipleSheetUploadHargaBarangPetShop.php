<?php

namespace App\Exports;

use App\Exports\DataDaftarBarangPetShop;
use App\Exports\DataHargaPetShop;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleSheetUploadHargaBarangPetShop implements WithMultipleSheets
{
    use Exportable;
   
    protected $sheets;

    public function __construct()
    {

    }

    function array(): array
    {
        return $this->sheets;
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets = [
            new DataHargaPetShop(),
            new DataDaftarBarangPetShop(),
        ];

        return $sheets;
    }
}

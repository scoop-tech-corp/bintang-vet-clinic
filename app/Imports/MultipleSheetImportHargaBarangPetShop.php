<?php

namespace App\Imports;

use App\Imports\HargaBarangPetShopImport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleSheetImportHargaBarangPetShop implements WithMultipleSheets
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function sheets(): array
    {
        return [
            0 => new HargaBarangPetShopImport($this->id),
        ];
    }
}

<?php

namespace App\Imports;

use App\Imports\DaftarBarangImportPetShop;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleSheetImportDaftarBarangPetShop implements WithMultipleSheets
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function sheets(): array
    {
        return [
            0 => new DaftarBarangImportPetShop($this->id),
        ];
    }
}

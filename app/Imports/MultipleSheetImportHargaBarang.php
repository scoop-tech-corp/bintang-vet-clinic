<?php

namespace App\Imports;

use App\Imports\HargaBarangImport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleSheetImportHargaBarang implements WithMultipleSheets
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function sheets(): array
    {
        return [
            0 => new HargaBarangImport($this->id),
        ];
    }
}

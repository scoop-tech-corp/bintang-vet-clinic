<?php

namespace App\Imports;

use App\Imports\DaftarBarangImport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleSheetImportDaftarBarang implements WithMultipleSheets
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function sheets(): array
    {
        return [
            0 => new DaftarBarangImport($this->id),
        ];
    }
}
<?php

namespace App\Imports;

use App\Imports\KelompokObatImport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleSheetImportKelompokObat implements WithMultipleSheets
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
    public function sheets(): array
    {
        return [
            0 => new KelompokObatImport($this->id),
        ];
    }
}

<?php

namespace App\Imports;

use App\Imports\HargaKelompokObatImport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleSheetImportHargaKelompokObat implements WithMultipleSheets
{
  protected $id;

  public function __construct($id)
  {
      $this->id = $id;
  }
    public function sheets(): array
    {
        return [
            0 => new HargaKelompokObatImport($this->id),
        ];
    }
}

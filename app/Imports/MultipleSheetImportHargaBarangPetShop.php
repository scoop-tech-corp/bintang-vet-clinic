<?php

namespace App\Imports;

use App\Imports\HargaBarangPetShopImport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleSheetImportHargaBarangPetShop implements WithMultipleSheets
{
    /**
     * @param Collection $collection
     */
    public function sheets(): array
    {
        return [
            0 => new HargaBarangPetShopImport(),
        ];
    }
}

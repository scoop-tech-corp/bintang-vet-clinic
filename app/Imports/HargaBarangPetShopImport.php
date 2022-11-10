<?php

namespace App\Imports;

use App\Models\PriceItemPetShop;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class HargaBarangPetShopImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function model(array $row)
    {

        $modal = str_replace(",","",$row['harga_modal']);
        $jual = str_replace(",","",$row['harga_jual']);

        return new PriceItemPetShop([
            'list_of_item_pet_shop_id' => $row['kode_daftar_barang'],
            'selling_price' => $jual,
            'capital_price' => $modal,
            'profit' => $jual - $modal,
            'branch_id' => $row['kode_cabang'],
            'user_id' => $this->id,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.kode_daftar_barang' => 'required|integer',
            '*.harga_jual' => 'required',
            '*.harga_modal' => 'required',
            '*.kode_cabang' => 'required|numeric',
        ];
    }
}

<?php

namespace App\Imports;

use App\Models\PriceItem;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class HargaBarangImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function model(array $row)
    {

        return new PriceItem([
            'list_of_items_id' => $row['kode_daftar_barang'],
            'selling_price' => $row['harga_jual'],
            'capital_price' => $row['harga_modal'],
            'doctor_fee' => $row['fee_dokter'],
            'petshop_fee' => $row['fee_petshop'],
            'user_id' => $this->id,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.kode_daftar_barang' => 'required|integer',
            '*.harga_jual' => 'required|numeric',
            '*.harga_modal' => 'required|numeric',
            '*.fee_dokter' => 'required|numeric',
            '*.fee_petshop' => 'required|numeric',
        ];
    }
}

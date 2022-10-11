<?php

namespace App\Imports;

use App\Models\ListofItems;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DaftarBarangImportPetShop implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function model(array $row)
    {
        $exp_date = \Carbon\Carbon::parse(Carbon::createFromFormat('d/m/Y', $row['tanggal_kedaluwarsa_barang_ddmmyyyy'])->format('Y/m/d'));
        return new ListofItems(
            [
                'item_name' => $row['nama_barang'],
                'total_item' => $row['jumlah_barang'],
                'limit_item' => $row['limit_barang'],
                'expired_date' => $exp_date,
                'branch_id' => $row['kode_cabang_barang'],
                'diff_item' => $row['jumlah_barang'] - $row['limit_barang'],
                'diff_expired_days' => \Carbon\Carbon::parse(now())->diffInDays($exp_date, false),
                'user_id' => $this->id,
            ]);
    }

    public function rules(): array
    {
        return [
            '*.nama_barang' => 'required|string',
            '*.jumlah_barang' => 'required|integer',
            '*.limit_barang' => 'required|integer',
            // '*.tanggal_kedaluwarsa_barang_ddmmyyyy' => 'required|date_format:d/m/Y',
            '*.kode_cabang_barang' => 'required|integer',
        ];
    }
}

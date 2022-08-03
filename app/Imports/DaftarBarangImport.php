<?php

namespace App\Imports;

use App\Models\ListofItems;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DaftarBarangImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function model(array $row)
    {
        $exp_date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_kedaluwarsa_barang_ddmmyyyy']));
        
        return new ListofItems(
            [
                'item_name' => $row['nama_barang'],
                'total_item' => $row['jumlah_barang'],
                'limit_item' => $row['limit_barang'],
                'expired_date' => $exp_date,
                'unit_item_id' => $row['kode_satuan_barang'],
                'category_item_id' => $row['kode_kategori_barang'],
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
            '*.tanggal_kedaluwarsa_barang_ddmmyyyy' => 'required',
            '*.kode_satuan_barang' => 'required|integer',
            '*.kode_kategori_barang' => 'required|integer',
            '*.kode_cabang_barang' => 'required|integer',
        ];
    }
}
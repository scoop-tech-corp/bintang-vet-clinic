<?php

namespace App\Imports;

use App\Models\ListofItemsPetShop;
use Carbon\Carbon;
use DateTime;
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

    // if ($row['tanggal_kedaluwarsa_barang_ddmmyyyy']) {
    //   // $exp_date = $row['tanggal_kedaluwarsa_barang_ddmmyyyy']->format('Y-m-d');
    //   //$exp_date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int) $row['tanggal_kedaluwarsa_barang_ddmmyyyy']));
    //   $exp_date = Carbon::parse(Carbon::createFromFormat('d/m/Y', $row['tanggal_kedaluwarsa_barang_ddmmyyyy'])->format('Y/m/d'));
    //   $diff_expired = Carbon::parse(now())->diffInDays($exp_date, false);
    // } else {
    //   $exp_date = '0000/00/00';
    //   $diff_expired = 0;
    // }

    $Temp = DateTime::createFromFormat('d/m/Y', $row['tanggal_kedaluwarsa_barang_ddmmyyyy']);

    if ($Temp) {
      $exp_date = DateTime::createFromFormat('d/m/Y', $row['tanggal_kedaluwarsa_barang_ddmmyyyy']);

      $diff_expired = Carbon::parse(now())->diffInDays($exp_date, false);
    } else {
      $exp_date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int) $row['tanggal_kedaluwarsa_barang_ddmmyyyy']));
      //$exp_date = Carbon::parse(Carbon::createFromFormat('d/m/Y', $row['tanggal_kedaluwarsa_barang_ddmmyyyy'])->format('Y/m/d'));

      $diff_expired = Carbon::parse(now())->diffInDays($exp_date, false);
    }

    return new ListofItemsPetShop(
      [
        'item_name' => $row['nama_barang'],
        'total_item' => $row['jumlah_barang'],
        'limit_item' => $row['limit_barang'],
        'expired_date' => $exp_date,
        'branch_id' => $row['kode_cabang_barang'],
        'diff_item' => $row['jumlah_barang'] - $row['limit_barang'],
        'diff_expired_days' => $diff_expired,
        'user_id' => $this->id,
      ]
    );
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

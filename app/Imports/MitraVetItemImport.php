<?php

namespace App\Imports;

use App\Models\MitraVetItem;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class MitraVetItemImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected $userId;
    protected $errors = [];

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Kolom yang diharapkan dari Excel:
     * nama_barang | satuan | harga_satuan | stok
     */
    public function model(array $row)
    {
        // Cek duplikasi nama barang (case insensitive)
        $existing = MitraVetItem::whereRaw('LOWER(nama_barang) = ?', [strtolower(trim($row['nama_barang']))])
            ->where('is_deleted', false)
            ->first();

        if ($existing) {
            // Jika sudah ada, tambah stok saja (upsert behavior)
            $existing->stok += (int) $row['stok'];
            $existing->updated_by = $this->userId;
            $existing->save();
            return null;
        }

        return new MitraVetItem([
            'nama_barang'  => trim($row['nama_barang']),
            'satuan'       => trim($row['satuan']),
            'harga_satuan' => (float) str_replace(',', '', $row['harga_satuan']),
            'stok'         => (int) $row['stok'],
            'created_by'   => $this->userId,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.nama_barang'  => 'required|string|max:150',
            '*.satuan'       => 'required|string|max:50',
            '*.harga_satuan' => 'required|numeric|min:0',
            '*.stok'         => 'required|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.nama_barang.required'  => 'Kolom nama_barang wajib diisi.',
            '*.satuan.required'       => 'Kolom satuan wajib diisi.',
            '*.harga_satuan.required' => 'Kolom harga_satuan wajib diisi.',
            '*.harga_satuan.numeric'  => 'Kolom harga_satuan harus berupa angka.',
            '*.stok.required'         => 'Kolom stok wajib diisi.',
            '*.stok.integer'          => 'Kolom stok harus berupa angka bulat.',
            '*.stok.min'              => 'Kolom stok tidak boleh negatif.',
        ];
    }
}

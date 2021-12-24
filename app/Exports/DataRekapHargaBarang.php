<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class DataRekapHargaBarang implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle, WithMapping
{
    protected $orderby;
    protected $column;
    protected $keyword;
    protected $branch_id;
    protected $role;

    public function __construct($orderby, $column, $keyword, $role, $branch_id)
    {
        $this->orderby = $orderby;
        $this->column = $column;
        $this->keyword = $keyword;
        $this->branch_id = $branch_id;
        $this->role = $role;

    }

    public function collection()
    {
        $price_items = DB::table('price_items')
            ->join('users', 'price_items.user_id', '=', 'users.id')
            ->join('list_of_items', 'price_items.list_of_items_id', '=', 'list_of_items.id')
            ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
            ->select(
                'list_of_items.item_name',
                'category_item.category_name',
                'unit_item.unit_name',
                DB::raw("TRIM(list_of_items.total_item)+0 as total_item"),
                'branches.branch_name',
                DB::raw("TRIM(price_items.selling_price)+0 as selling_price"),
                DB::raw("TRIM(price_items.capital_price)+0 as capital_price"),
                DB::raw("TRIM(price_items.doctor_fee)+0 as doctor_fee"),
                DB::raw("TRIM(price_items.petshop_fee)+0 as petshop_fee"),
                'users.fullname as created_by',
                DB::raw("DATE_FORMAT(price_items.created_at, '%d %b %Y') as created_at"))
            ->where('price_items.isDeleted', '=', 0);

        if ($this->branch_id && $this->role == 'admin') {
            $price_items = $price_items->where('branches.id', '=', $this->branch_id);
        }

        if ($this->role == 'dokter' || $this->role == 'resepsionis') {
            $price_items = $price_items->where('branches.id', '=', $this->branch_id);
        }

        if ($this->orderby) {
            $price_items = $price_items->orderBy($this->column, $this->orderby);
        }

        $price_items = $price_items->orderBy('price_items.id', 'desc');

        $price_items = $price_items->get();

        $val = 1;
        foreach ($price_items as $key) {
            $key->number = $val;
            $val++;
        }

        return $price_items;
    }

    public function headings(): array
    {
        return [
            ['No.', 'Nama Barang', 'Kategori Barang', 'Jumlah Barang', 'Satuan Barang', 'Harga Jual',
                'Harga Modal', 'Fee Dokter', 'Fee Petshop', 'Cabang', 'Dibuat Oleh', 'Tanggal Dibuat'],
        ];
    }

    public function title(): string
    {
        return 'Data Rekap';
    }

    public function map($price_items): array
    {
        $res = [
            [
                $price_items->number,
                $price_items->item_name,
                $price_items->category_name,
                strval($price_items->total_item),
                $price_items->unit_name,
                // number_format($res_data->doctor_fee, 2, ',', '.')
                number_format($price_items->selling_price, 2, ",", "."),
                number_format($price_items->capital_price, 2, ",", "."),
                number_format($price_items->doctor_fee, 2, ",", "."),
                number_format($price_items->petshop_fee, 2, ",", "."),
                $price_items->branch_name,
                $price_items->created_by,
                $price_items->created_at,
            ],
        ];
        return $res;
    }
}

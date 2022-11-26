<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class DataRekapHargaBarangPetShop implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle, WithMapping
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
        $price_items = DB::table('price_item_pet_shops as pi')
        ->join('users', 'pi.user_id', '=', 'users.id')
        ->join('list_of_item_pet_shops as ls', 'pi.list_of_item_pet_shop_id', '=', 'ls.id')
        ->join('branches', 'ls.branch_id', '=', 'branches.id')
        ->select(
            'pi.id',
            'ls.id as list_of_item_pet_shop_id',
            'ls.item_name',
            'ls.total_item',
            DB::raw('DATE_FORMAT(ls.expired_date, "%d/%m/%Y") as expired_date'),
            'ls.limit_item',
            'branches.id as branch_id',
            'branches.branch_name',
            DB::raw("TRIM(pi.selling_price)+0 as selling_price"),
            DB::raw("TRIM(pi.capital_price)+0 as capital_price"),
            DB::raw("TRIM(pi.profit)+0 as profit"),
            'users.fullname as created_by',
            DB::raw("DATE_FORMAT(pi.created_at, '%d %b %Y') as created_at")
        )
        ->where('pi.isDeleted', '=', 0);

        if ($this->branch_id && $this->role == 'admin') {
            $price_items = $price_items->where('branches.id', '=', $this->branch_id);
        }

        if ($this->role == 'dokter' || $this->role == 'resepsionis') {
            $price_items = $price_items->where('branches.id', '=', $this->branch_id);
        }

        if ($this->orderby) {
            $price_items = $price_items->orderBy($this->column, $this->orderby);
        }

        $price_items = $price_items->orderBy('pi.id', 'desc');

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
            ['No.', 'Nama Barang', 'Jumlah Barang', 'Limit Barang', 'Tanggal Kedaluwarsa',
                'Harga Jual', 'Harga Modal', 'Keuntungan', 'Cabang', 'Dibuat Oleh', 'Tanggal Dibuat'],
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
                strval($price_items->total_item),
                strval($price_items->limit_item),
                $price_items->expired_date,
                number_format($price_items->selling_price, 2, ",", "."),
                number_format($price_items->capital_price, 2, ",", "."),
                number_format($price_items->profit, 2, ",", "."),
                $price_items->branch_name,
                $price_items->created_by,
                $price_items->created_at,
            ],
        ];
        return $res;
    }
}

<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class DataRekapDaftarBarang implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle, WithMapping
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
        $item = DB::table('list_of_items')
            ->join('users', 'list_of_items.user_id', '=', 'users.id')
            ->join('branches', 'list_of_items.branch_id', '=', 'branches.id')
            ->join('unit_item', 'list_of_items.unit_item_id', '=', 'unit_item.id')
            ->join('category_item', 'list_of_items.category_item_id', '=', 'category_item.id')
            ->select(
                'list_of_items.item_name',
                DB::raw("TRIM(list_of_items.total_item)+0 as total_item"),
                'unit_item.unit_name',
                'category_item.category_name',
                'branches.branch_name',
                'users.fullname as created_by',
                DB::raw("DATE_FORMAT(list_of_items.created_at, '%d %b %Y') as created_at"))
            ->where('list_of_items.isDeleted', '=', 0);

        if ($this->branch_id && $this->role == 'admin') {
            $item = $item->where('list_of_items.branch_id', '=', $this->branch_id);
        }

        if ($this->role == 'dokter' || $this->role == 'resepsionis') {
            $item = $item->where('list_of_items.branch_id', '=', $this->branch_id);
        }

        if ($this->orderby) {
            $item = $item->orderBy($this->column, $this->orderby);
        }

        $item = $item->orderBy('list_of_items.id', 'desc');

        $item = $item->get();

        $val = 1;
        foreach ($item as $key) {
            $key->number = $val;
            $val++;
        }

        return $item;
    }

    public function headings(): array
    {
        return [
            ['No.', 'Nama Barang', 'Jumlah Barang', 'Satuan', 'Kategori', 'Cabang', 'Dibuat Oleh',
                'Tanggal Dibuat'],
        ];
    }

    public function title(): string
    {
        return 'Data Rekap';
    }

    public function map($item): array
    {
        $res = [
            [
                $item->number,
                $item->item_name,
                strval($item->total_item),
                $item->unit_name,
                $item->category_name,
                $item->branch_name,
                $item->created_by,
                $item->created_at,
            ],
        ];
        return $res;
    }
}

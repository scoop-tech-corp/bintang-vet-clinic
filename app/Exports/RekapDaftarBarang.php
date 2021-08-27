<?php

namespace App\Exports;

use App\Exports\DataRekapDaftarBarang;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RekapDaftarBarang implements WithMultipleSheets
{
    use Exportable;

    protected $sheets;

    protected $orderby;
    protected $column;
    protected $date;
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

    function array(): array
    {
        return $this->sheets;
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets = [
            new DataRekapDaftarBarang($this->orderby, $this->column, $this->keyword, $this->branch_id, $this->role),
        ];

        return $sheets;
    }
}

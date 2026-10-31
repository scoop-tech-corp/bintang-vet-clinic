<?php

namespace App\Exports;

use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MitraVetStockHistoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    use Exportable;

    protected $month;
    protected $year;
    protected $keyword;
    protected $rowNumber = 0;

    public function __construct($month = null, $year = null, $keyword = null)
    {
        $this->month   = $month;
        $this->year    = $year;
        $this->keyword = $keyword;
    }

    public function collection()
    {
        // Ambil semua histori mutasi secara kronologis untuk kalkulasi saldo/total stok berjalan per item
        $allHistories = DB::table('mitra_vet_stock_histories as h')
            ->join('mitra_vet_items as i', 'h.item_id', '=', 'i.id')
            ->leftJoin('users as u', 'h.created_by', '=', 'u.id')
            ->select(
                'h.id',
                'h.item_id',
                'h.type',
                'h.quantity',
                'h.keterangan',
                'h.created_at',
                'i.nama_barang',
                'i.satuan',
                DB::raw('TRIM(i.harga_satuan)+0 as harga_satuan'),
                'u.fullname as petugas'
            )
            ->orderBy('h.created_at', 'asc')
            ->orderBy('h.id', 'asc')
            ->get();

        $itemBalances = [];
        $calculatedList = [];

        foreach ($allHistories as $h) {
            $itemId = $h->item_id;
            $qty = (int) $h->quantity;

            if ($h->type === 'masuk') {
                $qtyMasuk  = $qty;
                $qtyKeluar = 0;
                $itemBalances[$itemId] = ($itemBalances[$itemId] ?? 0) + $qty;
            } else {
                $qtyMasuk  = 0;
                $qtyKeluar = $qty;
                $itemBalances[$itemId] = ($itemBalances[$itemId] ?? 0) - $qty;
            }

            $currentBalance = $itemBalances[$itemId];
            $nominalTotal   = $qty * (float) $h->harga_satuan;

            $h->qty_masuk   = $qtyMasuk;
            $h->qty_keluar  = $qtyKeluar;
            $h->total_stok  = $currentBalance;
            $h->total_nilai = $nominalTotal;

            // Cek apakah memenuhi kriteria filter tanggal & keyword
            $dt = Carbon::parse($h->created_at);
            $matchDate = true;
            if ($this->month && $this->year) {
                $matchDate = ($dt->month == $this->month && $dt->year == $this->year);
            } elseif ($this->year) {
                $matchDate = ($dt->year == $this->year);
            } elseif ($this->month) {
                $matchDate = ($dt->month == $this->month);
            }

            $matchKeyword = true;
            if ($this->keyword) {
                $kw = strtolower($this->keyword);
                $matchKeyword = (strpos(strtolower($h->nama_barang), $kw) !== false ||
                                 strpos(strtolower($h->keterangan ?? ''), $kw) !== false);
            }

            if ($matchDate && $matchKeyword) {
                $calculatedList[] = $h;
            }
        }

        // Urutkan kembali berdasarkan waktu transaksi terbaru
        return collect(array_reverse($calculatedList));
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal & Waktu',
            'Nama Barang',
            'Satuan',
            'Jumlah Masuk',
            'Jumlah Keluar',
            'Total Stok',
            'Harga Satuan (Rp)',
            'Total Nilai (Rp)',
            'Tipe Mutasi',
            'Keterangan / Sumber',
            'Dicatat Oleh',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            Carbon::parse($row->created_at)->format('d/m/Y H:i'),
            $row->nama_barang,
            $row->satuan,
            $row->qty_masuk > 0 ? (int) $row->qty_masuk : 0,
            $row->qty_keluar > 0 ? (int) $row->qty_keluar : 0,
            (int) $row->total_stok,
            number_format((float) $row->harga_satuan, 2, ',', '.'),
            number_format((float) $row->total_nilai, 2, ',', '.'),
            ucfirst($row->type),
            $row->keterangan ?: '-',
            $row->petugas ?: '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '008d4c'], // Green header
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Format perataan: Jumlah (E, F, G) rata tengah, Nominal (H, I) rata kanan
        $sheet->getStyle('E:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H:I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 18,  // Tanggal & Waktu
            'C' => 28,  // Nama Barang
            'D' => 12,  // Satuan
            'E' => 16,  // Jumlah Masuk
            'F' => 16,  // Jumlah Keluar
            'G' => 16,  // Total Stok
            'H' => 18,  // Harga Satuan
            'I' => 18,  // Total Nilai
            'J' => 14,  // Tipe Mutasi
            'K' => 35,  // Keterangan / Sumber
            'L' => 20,  // Dicatat Oleh
        ];
    }
}

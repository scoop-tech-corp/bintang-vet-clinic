<?php

namespace App\Exports;

use App\Models\MitraVetOrder;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MitraVetOrderExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
        $query = MitraVetOrder::with(['items.item', 'creator'])
            ->whereNotNull('id');

        if ($this->keyword) {
            $query->where(function ($q) {
                $q->where('nama_pembeli', 'like', '%' . $this->keyword . '%')
                    ->orWhere('nomor_order', 'like', '%' . $this->keyword . '%')
                    ->orWhere('no_hp', 'like', '%' . $this->keyword . '%');
            });
        }

        if ($this->month && $this->year) {
            $query->whereMonth('created_at', $this->month)
                ->whereYear('created_at', $this->year);
        } elseif ($this->year) {
            $query->whereYear('created_at', $this->year);
        } elseif ($this->month) {
            $query->whereMonth('created_at', $this->month);
        }

        // SESUAI PERMINTAAN USER: Disort berdasarkan NAMA ORANG YANG ORDER
        $query->orderBy('nama_pembeli', 'asc')->orderBy('created_at', 'desc');

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pembeli (Mitra)',
            'No. Handphone',
            'Alamat Pengiriman',
            'No. Order',
            'Tanggal Order',
            'Rincian Produk (Qty)',
            'Total Produk (Rp)',
            'Ongkos Kirim (Rp)',
            'Grand Total (Rp)',
            'Dibuat Oleh',
        ];
    }

    public function map($order): array
    {
        $this->rowNumber++;

        // Format rincian produk
        $itemDetails = [];
        foreach ($order->items as $oi) {
            $namaItem = $oi->item ? $oi->item->nama_barang : 'Barang dihapus';
            $itemDetails[] = "{$namaItem} ({$oi->quantity} " . ($oi->item ? $oi->item->satuan : '') . ")";
        }
        $rincianProduk = implode(",\n", $itemDetails);

        return [
            $this->rowNumber,
            $order->nama_pembeli,
            $order->no_hp,
            $order->alamat,
            $order->nomor_order,
            Carbon::parse($order->created_at)->format('d/m/Y H:i'),
            $rincianProduk,
            number_format((float) $order->total_harga, 2, ',', '.'),
            number_format((float) $order->biaya_ongkir, 2, ',', '.'),
            number_format((float) $order->grand_total, 2, ',', '.'),
            $order->creator ? $order->creator->fullname : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1a6596'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Enable wrap text for column D (alamat) and G (rincian produk)
        $sheet->getStyle('D:D')->getAlignment()->setWrapText(true);
        $sheet->getStyle('G:G')->getAlignment()->setWrapText(true);

        // Format perataan nominal (H, I, J) rata kanan
        $sheet->getStyle('H:J')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 25,  // Nama Pembeli (disort)
            'C' => 16,  // No HP
            'D' => 30,  // Alamat
            'E' => 20,  // No Order
            'F' => 18,  // Tanggal
            'G' => 35,  // Rincian Produk
            'H' => 18,  // Total Produk
            'I' => 16,  // Ongkir
            'J' => 18,  // Grand Total
            'K' => 18,  // Dibuat Oleh
        ];
    }
}

<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class AbsensiExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    protected $filters;
    protected $no = 0;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = DB::table('attendances as a')
            ->join('users', 'a.user_id', '=', 'users.id')
            ->join('shifts', 'a.shift_id', '=', 'shifts.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->select(
                'users.fullname',
                'branches.branch_name',
                'shifts.nama_shift',
                'a.tanggal',
                DB::raw("TIME_FORMAT(shifts.jam_masuk, '%H:%i') as shift_jam_masuk"),
                DB::raw("TIME_FORMAT(shifts.jam_keluar, '%H:%i') as shift_jam_keluar"),
                DB::raw("TIME_FORMAT(a.jam_masuk, '%H:%i') as jam_masuk"),
                DB::raw("TIME_FORMAT(a.jam_keluar, '%H:%i') as jam_keluar"),
                'a.alamat',
                'a.status'
            );

        if (!empty($this->filters['branch_id'])) {
            $query->where('users.branch_id', '=', $this->filters['branch_id']);
        }

        if (!empty($this->filters['user_id'])) {
            $query->where('a.user_id', '=', $this->filters['user_id']);
        }

        if (!empty($this->filters['tanggal_dari'])) {
            $query->where('a.tanggal', '>=', $this->filters['tanggal_dari']);
        }

        if (!empty($this->filters['tanggal_sampai'])) {
            $query->where('a.tanggal', '<=', $this->filters['tanggal_sampai']);
        }

        if (!empty($this->filters['shift_id'])) {
            $query->where('a.shift_id', '=', $this->filters['shift_id']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('a.status', '=', $this->filters['status']);
        }

        if (!empty($this->filters['keyword'])) {
            $query->where('users.fullname', 'like', '%' . $this->filters['keyword'] . '%');
        }

        return $query->orderBy('a.tanggal', 'desc')->orderBy('users.fullname', 'asc')->get();
    }

    public function headings(): array
    {
        return ['No', 'Nama Karyawan', 'Cabang', 'Shift', 'Tanggal', 'Jam Shift Masuk', 'Jam Shift Keluar', 'Jam Absen Masuk', 'Jam Absen Keluar', 'Alamat', 'Status'];
    }

    public function map($row): array
    {
        $this->no++;

        $statusLabel = match ($row->status) {
            'hadir'       => 'Hadir',
            'terlambat'   => 'Terlambat',
            'tidak_hadir' => 'Tidak Hadir',
            default       => $row->status,
        };

        return [$this->no, $row->fullname, $row->branch_name, $row->nama_shift, $row->tanggal, $row->shift_jam_masuk, $row->shift_jam_keluar, $row->jam_masuk ?? '-', $row->jam_keluar ?? '-', $row->alamat ?? '-', $statusLabel];
    }

    public function title(): string
    {
        return 'Laporan Absensi';
    }
}

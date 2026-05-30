<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiExport;
use App\Models\Attendance;
use App\Models\Shift;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class AbsensiController extends Controller
{
    public function cekHariIni(Request $request)
    {
        $today = Carbon::now()->toDateString();
        $attendance = DB::table('attendances')
            ->where('user_id', $request->user()->id)
            ->where('tanggal', $today)
            ->first();

        return response()->json([
            'sudah_absen' => $attendance ? true : false,
            'sudah_keluar' => $attendance && $attendance->jam_keluar ? true : false,
            'data' => $attendance,
        ], 200);
    }

    public function masuk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shift_id' => 'required|integer',
            'foto'     => 'required|string',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'alamat'      => 'nullable|string',
            'keterangan'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Data tidak valid!', 'errors' => $validator->errors()->all()], 422);
        }

        $today = Carbon::now()->toDateString();

        $existing = DB::table('attendances')
            ->where('user_id', $request->user()->id)
            ->where('tanggal', $today)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Anda sudah melakukan absen masuk hari ini!', 'errors' => ['Sudah absen masuk!']], 422);
        }

        $shift = Shift::find($request->shift_id);
        if (!$shift) {
            return response()->json(['message' => 'Shift tidak ditemukan!', 'errors' => ['Shift tidak valid!']], 404);
        }

        $jamSekarang = Carbon::now()->format('H:i:s');
        $batasLambat = Carbon::parse($shift->jam_masuk)->addMinutes($shift->toleransi_menit)->format('H:i:s');
        $status = $jamSekarang <= $batasLambat ? 'hadir' : 'terlambat';

        $fotoPath = $this->simpanFoto($request->foto, 'absensi/masuk');

        Attendance::create([
            'user_id'    => $request->user()->id,
            'shift_id'   => $request->shift_id,
            'tanggal'    => $today,
            'jam_masuk'  => $jamSekarang,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'alamat'      => $request->alamat,
            'keterangan'  => $request->keterangan,
            'foto_masuk'  => $fotoPath,
            'status'      => $status,
        ]);

        $pesan = $status === 'terlambat' ? 'Absen masuk berhasil (Terlambat).' : 'Absen masuk berhasil!';

        return response()->json(['message' => $pesan, 'status' => $status], 200);
    }

    public function keluar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'foto' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Data tidak valid!', 'errors' => $validator->errors()->all()], 422);
        }

        $today = Carbon::now()->toDateString();

        $attendance = Attendance::where('user_id', $request->user()->id)
            ->where('tanggal', $today)
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Anda belum melakukan absen masuk hari ini!', 'errors' => ['Belum absen masuk!']], 422);
        }

        if ($attendance->jam_keluar) {
            return response()->json(['message' => 'Anda sudah melakukan absen keluar hari ini!', 'errors' => ['Sudah absen keluar!']], 422);
        }

        $fotoPath = $this->simpanFoto($request->foto, 'absensi/keluar');

        $attendance->jam_keluar  = Carbon::now()->format('H:i:s');
        $attendance->foto_keluar = $fotoPath;
        $attendance->save();

        return response()->json(['message' => 'Absen keluar berhasil!'], 200);
    }

    public function index(Request $request)
    {
        $query = DB::table('attendances as a')
            ->join('users', 'a.user_id', '=', 'users.id')
            ->join('shifts', 'a.shift_id', '=', 'shifts.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->select(
                'a.id',
                'users.fullname',
                'users.username',
                'branches.branch_name',
                'shifts.nama_shift',
                DB::raw("DATE_FORMAT(a.tanggal, '%d %b %Y') as tanggal"),
                DB::raw("TIME_FORMAT(a.jam_masuk, '%H:%i') as jam_masuk"),
                DB::raw("TIME_FORMAT(a.jam_keluar, '%H:%i') as jam_keluar"),
                DB::raw("TIME_FORMAT(shifts.jam_masuk, '%H:%i') as shift_jam_masuk"),
                DB::raw("TIME_FORMAT(shifts.jam_keluar, '%H:%i') as shift_jam_keluar"),
                'a.foto_masuk',
                'a.foto_keluar',
                'a.latitude',
                'a.longitude',
                'a.alamat',
                'a.keterangan',
                'a.status'
            );

        if ($request->user()->role === 'dokter' || $request->user()->role === 'resepsionis') {
            $query->where('a.user_id', '=', $request->user()->id);
        }
        // admin sees all branches — branch_id filter applied below if provided via request

        if ($request->branch_id) {
            $query->where('users.branch_id', '=', $request->branch_id);
        }

        if ($request->tanggal_dari) {
            $query->where('a.tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->tanggal_sampai) {
            $query->where('a.tanggal', '<=', $request->tanggal_sampai);
        }

        if ($request->shift_id) {
            $query->where('a.shift_id', '=', $request->shift_id);
        }

        if ($request->status) {
            $query->where('a.status', '=', $request->status);
        }

        if ($request->keyword) {
            $query->where('users.fullname', 'like', '%' . $request->keyword . '%');
        }

        $query->orderBy('a.tanggal', 'desc')->orderBy('a.id', 'desc');

        return response()->json($query->get(), 200);
    }

    public function export(Request $request)
    {
        $filters = [
            'tanggal_dari'   => $request->tanggal_dari,
            'tanggal_sampai' => $request->tanggal_sampai,
            'shift_id'       => $request->shift_id,
            'status'         => $request->status,
            'keyword'        => $request->keyword,
        ];

        if ($request->user()->role === 'dokter' || $request->user()->role === 'resepsionis') {
            $filters['user_id'] = $request->user()->id;
        } else {
            // admin: filter by branch_id from request if provided, otherwise no restriction
            $filters['branch_id'] = $request->branch_id ?: null;
        }

        $dari   = $filters['tanggal_dari'] ?? now()->format('Y-m-d');
        $sampai = $filters['tanggal_sampai'] ?? now()->format('Y-m-d');
        $filename = 'Laporan Absensi ' . $dari . ' sd ' . $sampai . '.xlsx';

        return Excel::download(new AbsensiExport($filters), $filename);
    }

    private function simpanFoto(string $base64, string $folder): string
    {
        $image = str_replace(['data:image/jpeg;base64,', 'data:image/png;base64,', 'data:image/webp;base64,'], '', $base64);
        $image = base64_decode($image);

        $dir = public_path('uploads/' . $folder);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = time() . '_' . uniqid() . '.jpg';
        file_put_contents($dir . '/' . $filename, $image);

        return 'uploads/' . $folder . '/' . $filename;
    }
}

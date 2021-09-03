<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function BarChartPatient(Request $request)
    {
        if ($request->user()->role == 'resepsionis' || $request->user()->role == 'dokter') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $data = DB::table('registrations')
            ->join('patients', 'registrations.patient_id', '=', 'patients.id')
            ->join('users', 'registrations.doctor_user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->select(DB::raw('COUNT(registrations.id) as total_patient'), 'branches.branch_name')
            ->where('registrations.isDeleted', '=', 0);

        if ($request->month && $request->year) {
            $data = $data->where(DB::raw("MONTH(registrations.created_at)"), $request->month)
                ->where(DB::raw("YEAR(registrations.created_at)"), $request->year);
        }

        $data = $data->groupby('branches.branch_name')
            ->get();

        return response()->json($data, 200);
    }

    public function BarChartInPatient(Request $request)
    {
        if ($request->user()->role == 'resepsionis' || $request->user()->role == 'dokter') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $data = DB::table('check_up_results as cur')
            ->join('registrations as reg', 'cur.patient_registration_id', '=', 'reg.id')
            ->join('users', 'cur.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->select(DB::raw('COUNT(cur.id) as total_patient'), 'branches.branch_name')
            ->where('reg.isDeleted', '=', 0)
            ->where('cur.status_outpatient_inpatient', '=', 1);

        if ($request->date) {
            $data = $data->where(DB::raw("DATE(cur.created_at)"), $request->date);
        }

        $data = $data->groupby('branches.branch_name')
            ->get();

        return response()->json($data, 200);

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\OutPatient;
use DB;
use Illuminate\Http\Request;
use Validator;

class RawatJalanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role == 'dokter') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $data = DB::table('out_patients')
            ->join('users', 'out_patients.user_id', '=', 'users.id')
            ->join('users as user_doctor', 'out_patients.doctor_user_id', '=', 'user_doctor.id')
            ->join('patients', 'out_patients.patient_id', '=', 'patients.id')
            ->join('branches', 'patients.branch_id', '=', 'branches.id')
            ->select('out_patients.id as id', 'out_patients.id_number', 'out_patients.patient_id',
                'patients.id_member as id_number_patient', 'patients.pet_category', 'patients.pet_name', 'patients.pet_gender',
                'patients.pet_year_age', 'patients.pet_month_age', 'patients.owner_name', 'patients.owner_address',
                'patients.owner_phone_number', 'complaint', 'registrant', 'user_doctor.id as user_doctor_id',
                'user_doctor.username as username_doctor', 'out_patients.acceptance_status', 'users.fullname as created_by',
                DB::raw("DATE_FORMAT(out_patients.created_at, '%d %b %Y') as created_at"), 'users.branch_id as user_branch_id');

        if ($request->user()->role == 'resepsionis') {
            $data = $data->where('users.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {

            $data = $data->where('out_patients.id', 'like', '%' . $request->keyword . '%')
                ->orwhere('patients.id_member', 'like', '%' . $request->keyword . '%')
                ->orwhere('patients.pet_name', 'like', '%' . $request->keyword . '%')
                ->orwhere('users.fullname', 'like', '%' . $request->keyword . '%')
                ->orwhere('out_patients.complaint', 'like', '%' . $request->keyword . '%')
                ->orwhere('out_patients.registrant', 'like', '%' . $request->keyword . '%')
                ->orwhere('user_doctor.username', 'like', '%' . $request->keyword . '%')
                ->orwhere('users.fullname', 'like', '%' . $request->keyword . '%');
        }

        if ($request->orderby) {

            $data = $data->orderBy($request->column, $request->orderby);
        }

        $data = $data->orderBy('out_patients.id', 'desc');

        $data = $data->get();

        return response()->json($data, 200);

    }

    public function create(Request $request)
    {
        if ($request->user()->role == 'dokter') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|numeric',
            'doctor_user_id' => 'required|numeric',
            'keluhan' => 'required|string|min:3|max:50',
            'nama_pendaftar' => 'required|string|min:3|max:50',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return response()->json([
                'message' => 'Data yang dimasukkan tidak valid!',
                'errors' => $errors,
            ], 422);
        }

        $lasttransaction = DB::table('out_patients')
            ->join('users', 'out_patients.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->where('branch_id', '=', $request->user()->branch_id)
            ->count();

        $getbranchuser = DB::table('branches')
            ->select('branch_code')
            ->where('id', '=', $request->user()->branch_id)
            ->first();

        $out_patient_number = 'NV-RJ-' . $getbranchuser->branch_code . '-' . str_pad($lasttransaction + 1, 4, 0, STR_PAD_LEFT);

        $patient = OutPatient::create([
            'id_number' => $out_patient_number,
            'patient_id' => $request->patient_id,
            'doctor_user_id' => $request->doctor_user_id,
            'complaint' => $request->keluhan,
            'registrant' => $request->nama_pendaftar,
            'user_id' => $request->user()->id,
            'acceptance_status' => 0,
        ]);

        return response()->json(
            [
                'message' => 'Tambah Data Berhasil!',
            ], 200
        );
    }

    public function update(Request $request)
    {
        if ($request->user()->role == 'dokter') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|numeric',
            'doctor_user_id' => 'required|numeric',
            'keluhan' => 'required|string|min:3|max:51',
            'nama_pendaftar' => 'required|string|min:3|max:50',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return response()->json([
                'message' => 'Data yang dimasukkan tidak valid!',
                'errors' => $errors,
            ], 422);
        }

        $out_patient = OutPatient::find($request->id);

        if (is_null($out_patient)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        } elseif ($out_patient->acceptance_status == 1) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak dapat diubah karena sudah diterima oleh dokter!'],
            ], 422);
        }

        $out_patient->patient_id = $request->patient_id;
        $out_patient->doctor_user_id = $request->doctor_user_id;
        $out_patient->complaint = $request->keluhan;
        $out_patient->registrant = $request->nama_pendaftar;
        $out_patient->user_update_id = $request->user()->id;
        $out_patient->acceptance_status = 0;
        $out_patient->updated_at = \Carbon\Carbon::now();
        $out_patient->save();

        return response()->json([
            'message' => 'Berhasil mengupdate Data',
        ], 200);
    }

    public function delete(Request $request)
    {
        if ($request->user()->role == 'dokter') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $out_patient = OutPatient::find($request->id);

        if (is_null($out_patient)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        } elseif ($out_patient->acceptance_status == 1) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak dapat dihapus karena sudah diterima oleh dokter!'],
            ], 422);
        }

        $out_patient->isDeleted = true;
        $out_patient->deleted_by = $request->user()->fullname;
        $out_patient->deleted_at = \Carbon\Carbon::now();
        $out_patient->save();

        $out_patient->delete();

        return response()->json([
            'message' => 'Berhasil menghapus Data',
        ], 200);
    }
}

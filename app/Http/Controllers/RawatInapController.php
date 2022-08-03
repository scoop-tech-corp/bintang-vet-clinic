<?php

namespace App\Http\Controllers;

use App\Models\InPatient;
use DB;
use Illuminate\Http\Request;
use Validator;

class RawatInapController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role == 'dokter') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $data = DB::table('in_patients')
            ->join('users', 'in_patients.user_id', '=', 'users.id')
            ->join('users as user_doctor', 'in_patients.doctor_user_id', '=', 'user_doctor.id')
            ->join('patients', 'in_patients.patient_id', '=', 'patients.id')
            ->join('branches', 'patients.branch_id', '=', 'branches.id')
            ->select('in_patients.id as id', 'in_patients.id_number', 'in_patients.patient_id',
                'patients.id_member as id_number_patient', 'patients.pet_category', 'patients.pet_name', 'patients.pet_gender',
                'patients.pet_year_age', 'patients.pet_month_age', 'patients.owner_name', 'patients.owner_address',
                'patients.owner_phone_number', 'in_patients.complaint', 'in_patients.registrant',
                'in_patients.estimate_day', 'in_patients.reality_day', 'user_doctor.id as user_doctor_id', 'in_patients.acceptance_status',
                'user_doctor.username as username_doctor', 'users.fullname as created_by',
                DB::raw("DATE_FORMAT(in_patients.created_at, '%d %b %Y') as created_at"), 'users.branch_id as user_branch_id');
                //$table->integer('acceptance_status');
        if ($request->user()->role == 'resepsionis') {
            $data = $data->where('users.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->keyword) {

            $data = $data->where('in_patients.id', 'like', '%' . $request->keyword . '%')
                ->orwhere('patients.id_member', 'like', '%' . $request->keyword . '%')
                ->orwhere('patients.pet_name', 'like', '%' . $request->keyword . '%')
                ->orwhere('users.fullname', 'like', '%' . $request->keyword . '%')
                ->orwhere('in_patients.complaint', 'like', '%' . $request->keyword . '%')
                ->orwhere('in_patients.registrant', 'like', '%' . $request->keyword . '%')
                ->orwhere('user_doctor.username', 'like', '%' . $request->keyword . '%')
                ->orwhere('users.fullname', 'like', '%' . $request->keyword . '%');
        }

        if ($request->orderby) {

            $data = $data->orderBy($request->column, $request->orderby);
        }

        $data = $data->orderBy('in_patients.id', 'desc');

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
            'keluhan' => 'required|string|min:3|max:50',
            'nama_pendaftar' => 'required|string|min:3|max:50',
            'doctor_user_id' => 'required|numeric',
            'estimasi_waktu' => 'required|numeric',
            'realita_waktu' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return response()->json([
                'message' => 'Data yang dimasukkan tidak valid!',
                'errors' => $errors,
            ], 422);
        }

        $lasttransaction = DB::table('in_patients')
            ->join('users', 'in_patients.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->where('branch_id', '=', $request->user()->branch_id)
            ->count();

        $getbranchuser = DB::table('branches')
            ->select('branch_code')
            ->where('id', '=', $request->user()->branch_id)
            ->first();

        $in_patient_number = 'SVC-RI-' . $getbranchuser->branch_code . '-' . str_pad($lasttransaction + 1, 4, 0, STR_PAD_LEFT);

        $patient = InPatient::create([
            'id_number' => $in_patient_number,
            'patient_id' => $request->patient_id,
            'complaint' => $request->keluhan,
            'registrant' => $request->nama_pendaftar,
            'doctor_user_id' => $request->doctor_user_id,
            'estimate_day' => $request->estimasi_waktu,
            'reality_day' => $request->realita_waktu,
            'user_id' => $request->user()->id,
            'acceptance_status' => 0
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
            'keluhan' => 'required|string|min:3|max:50',
            'nama_pendaftar' => 'required|string|min:3|max:50',
            'doctor_user_id' => 'required|numeric',
            'estimasi_waktu' => 'required|numeric',
            'realita_waktu' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return response()->json([
                'message' => 'Data yang dimasukkan tidak valid!',
                'errors' => $errors,
            ], 422);
        }

        $in_patient = InPatient::find($request->id);

        if (is_null($in_patient)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $in_patient->patient_id = $request->patient_id;
        $in_patient->doctor_user_id = $request->doctor_user_id;
        $in_patient->complaint = $request->keluhan;
        $in_patient->registrant = $request->nama_pendaftar;
        $in_patient->estimate_day = $request->estimasi_waktu;
        $in_patient->reality_day = $request->realita_waktu;
        $in_patient->user_update_id = $request->user()->id;
        $in_patient->updated_at = \Carbon\Carbon::now();
        $in_patient->acceptance_status = 0;
        $in_patient->save();

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

        $in_patient = InPatient::find($request->id);

        if (is_null($in_patient)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        }

        $in_patient->isDeleted = true;
        $in_patient->deleted_by = $request->user()->fullname;
        $in_patient->deleted_at = \Carbon\Carbon::now();
        $in_patient->save();

        $in_patient->delete();

        return response()->json([
            'message' => 'Berhasil menghapus Data',
        ], 200);
    }
}

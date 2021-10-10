<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use DB;
use Illuminate\Http\Request;
use Validator;

class RegistrasiController extends Controller
{
    public function index(Request $request)
    {

        if ($request->keyword) {

            $res = $this->Search($request);

            $data = DB::table('registrations')
                ->join('users', 'registrations.user_id', '=', 'users.id')
                ->join('users as user_doctor', 'registrations.doctor_user_id', '=', 'user_doctor.id')
                ->join('patients', 'registrations.patient_id', '=', 'patients.id')
                ->join('owners', 'patients.owner_id', '=', 'owners.id')
                ->join('branches', 'patients.branch_id', '=', 'branches.id')
                ->select(
                    'registrations.id as id',
                    'registrations.id_number',
                    'registrations.patient_id',
                    'patients.id_member as id_number_patient',
                    'patients.pet_category',
                    'patients.pet_name',
                    'patients.pet_gender',
                    'patients.pet_year_age',
                    'patients.pet_month_age',
                    DB::raw('(CASE WHEN patients.owner_name = "" THEN owners.owner_name ELSE patients.owner_name END) AS owner_name'),
                    DB::raw('(CASE WHEN patients.owner_address = "" THEN owners.owner_address ELSE patients.owner_address END) AS owner_address'),
                    DB::raw('(CASE WHEN patients.owner_phone_number = "" THEN owners.owner_phone_number ELSE patients.owner_phone_number END) AS owner_phone_number'),
                    'complaint',
                    'registrant',
                    'user_doctor.id as user_doctor_id',
                    'user_doctor.username as username_doctor',
                    'registrations.acceptance_status',
                    'users.fullname as created_by',
                    DB::raw("DATE_FORMAT(registrations.created_at, '%d %b %Y') as created_at"),
                    'users.branch_id as user_branch_id')
                ->where('registrations.isDeleted', '=', 0);

            if ($res) {
                $data = $data->where($res, 'like', '%' . $request->keyword . '%');
            } else {
                $data = [];
                return response()->json($data, 200);
            }

            if ($request->user()->role == 'resepsionis') {
                $data = $data->where('users.branch_id', '=', $request->user()->branch_id);
            }

            if ($request->user()->role == 'dokter') {
                $data = $data->where('user_doctor.branch_id', '=', $request->user()->branch_id);
            }

            if ($request->branch_id && $request->user()->role == 'admin') {
                $data = $data->where('user_doctor.branch_id', '=', $request->branch_id);
            }

            if ($request->orderby) {

                $data = $data->orderBy($request->column, $request->orderby);
            }

            $data = $data->orderBy('registrations.id', 'desc');

            $data = $data->get();

            return response()->json($data, 200);
        } else {

            $data = DB::table('registrations')
                ->join('users', 'registrations.user_id', '=', 'users.id')
                ->join('users as user_doctor', 'registrations.doctor_user_id', '=', 'user_doctor.id')
                ->join('patients', 'registrations.patient_id', '=', 'patients.id')
                ->join('owners', 'patients.owner_id', '=', 'owners.id')
                ->join('branches', 'patients.branch_id', '=', 'branches.id')
                ->select(
                    'registrations.id as id',
                    'registrations.id_number',
                    'registrations.patient_id',
                    'patients.id_member as id_number_patient',
                    'patients.pet_category',
                    'patients.pet_name',
                    'patients.pet_gender',
                    'patients.pet_year_age',
                    'patients.pet_month_age',
                    DB::raw('(CASE WHEN patients.owner_name = "" THEN owners.owner_name ELSE patients.owner_name END) AS owner_name'),
                    DB::raw('(CASE WHEN patients.owner_address = "" THEN owners.owner_address ELSE patients.owner_address END) AS owner_address'),
                    DB::raw('(CASE WHEN patients.owner_phone_number = "" THEN owners.owner_phone_number ELSE patients.owner_phone_number END) AS owner_phone_number'),
                    'complaint',
                    'registrant',
                    'user_doctor.id as user_doctor_id',
                    'user_doctor.username as username_doctor',
                    'registrations.acceptance_status',
                    'users.fullname as created_by',
                    DB::raw("DATE_FORMAT(registrations.created_at, '%d %b %Y') as created_at"),
                    'users.branch_id as user_branch_id')
                ->where('registrations.isDeleted', '=', 0);

            if ($request->user()->role == 'resepsionis') {
                $data = $data->where('users.branch_id', '=', $request->user()->branch_id);
            }

            if ($request->user()->role == 'dokter') {
                $data = $data->where('user_doctor.branch_id', '=', $request->user()->branch_id);
            }

            if ($request->branch_id && $request->user()->role == 'admin') {
                $data = $data->where('user_doctor.branch_id', '=', $request->branch_id);
            }

            if ($request->orderby) {

                $data = $data->orderBy($request->column, $request->orderby);
            }

            $data = $data->orderBy('registrations.id', 'desc');

            $data = $data->get();

            return response()->json($data, 200);
        }

    }

    private function Search($request)
    {

        $temp_column = '';
        $data = DB::table('registrations')
            ->join('users', 'registrations.user_id', '=', 'users.id')
            ->join('users as user_doctor', 'registrations.doctor_user_id', '=', 'user_doctor.id')
            ->join('patients', 'registrations.patient_id', '=', 'patients.id')
            ->join('branches', 'patients.branch_id', '=', 'branches.id')
            ->select(
                'registrations.id_number',
                'patients.id_member',
                'patients.pet_name',
                'patients.owner_name',
                'complaint',
                'registrant',
                'user_doctor.username',
                'users.fullname',
                DB::raw("DATE_FORMAT(registrations.created_at, '%d %b %Y') as created_at")
            )
            ->where('registrations.isDeleted', '=', 0);

        if ($request->user()->role == 'resepsionis') {
            $data = $data->where('users.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->user()->role == 'dokter') {
            $data = $data->where('user_doctor.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->branch_id && $request->user()->role == 'admin') {
            $data = $data->where('user_doctor.branch_id', '=', $request->branch_id);
        }

        if ($request->keyword) {
            $data = $data->where('registrations.id_number', 'like', '%' . $request->keyword . '%');
        }

        $data = $data->get();

        if (count($data)) {
            $temp_column = 'registrations.id_number';
            return $temp_column;
        }
        //==================================================

        $data = DB::table('registrations')
            ->join('users', 'registrations.user_id', '=', 'users.id')
            ->join('users as user_doctor', 'registrations.doctor_user_id', '=', 'user_doctor.id')
            ->join('patients', 'registrations.patient_id', '=', 'patients.id')
            ->join('branches', 'patients.branch_id', '=', 'branches.id')
            ->select(
                'registrations.id_number',
                'patients.id_member',
                'patients.pet_name',
                'patients.owner_name',
                'complaint',
                'registrant',
                'user_doctor.username',
                'users.fullname',
                DB::raw("DATE_FORMAT(registrations.created_at, '%d %b %Y') as created_at")
            )
            ->where('registrations.isDeleted', '=', 0);

        if ($request->user()->role == 'resepsionis') {
            $data = $data->where('users.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->user()->role == 'dokter') {
            $data = $data->where('user_doctor.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->branch_id && $request->user()->role == 'admin') {
            $data = $data->where('user_doctor.branch_id', '=', $request->branch_id);
        }

        if ($request->keyword) {
            $data = $data->where('patients.id_member', 'like', '%' . $request->keyword . '%');
        }

        $data = $data->get();

        if (count($data)) {
            $temp_column = 'patients.id_member';
            return $temp_column;
        }
        //==================================================

        $data = DB::table('registrations')
            ->join('users', 'registrations.user_id', '=', 'users.id')
            ->join('users as user_doctor', 'registrations.doctor_user_id', '=', 'user_doctor.id')
            ->join('patients', 'registrations.patient_id', '=', 'patients.id')
            ->join('branches', 'patients.branch_id', '=', 'branches.id')
            ->select(
                'registrations.id_number',
                'patients.id_member',
                'patients.pet_name',
                'patients.owner_name',
                'complaint',
                'registrant',
                'user_doctor.username',
                'users.fullname',
                DB::raw("DATE_FORMAT(registrations.created_at, '%d %b %Y') as created_at")
            )
            ->where('registrations.isDeleted', '=', 0);

        if ($request->user()->role == 'resepsionis') {
            $data = $data->where('users.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->user()->role == 'dokter') {
            $data = $data->where('user_doctor.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->branch_id && $request->user()->role == 'admin') {
            $data = $data->where('user_doctor.branch_id', '=', $request->branch_id);
        }

        if ($request->keyword) {
            $data = $data->where('patients.pet_name', 'like', '%' . $request->keyword . '%');
        }

        $data = $data->get();

        if (count($data)) {
            $temp_column = 'patients.pet_name';
            return $temp_column;
        }
        //==================================================

        $data = DB::table('registrations')
            ->join('users', 'registrations.user_id', '=', 'users.id')
            ->join('users as user_doctor', 'registrations.doctor_user_id', '=', 'user_doctor.id')
            ->join('patients', 'registrations.patient_id', '=', 'patients.id')
            ->join('branches', 'patients.branch_id', '=', 'branches.id')
            ->select(
                'registrations.id_number',
                'patients.id_member',
                'patients.pet_name',
                'patients.owner_name',
                'complaint',
                'registrant',
                'user_doctor.username',
                'users.fullname',
                DB::raw("DATE_FORMAT(registrations.created_at, '%d %b %Y') as created_at")
            )
            ->where('registrations.isDeleted', '=', 0);

        if ($request->user()->role == 'resepsionis') {
            $data = $data->where('users.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->user()->role == 'dokter') {
            $data = $data->where('user_doctor.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->branch_id && $request->user()->role == 'admin') {
            $data = $data->where('user_doctor.branch_id', '=', $request->branch_id);
        }

        if ($request->keyword) {
            $data = $data->where('patients.owner_name', 'like', '%' . $request->keyword . '%');
        }

        $data = $data->get();

        if (count($data)) {
            $temp_column = 'patients.owner_name';
            return $temp_column;
        }
        //==================================================

        $data = DB::table('registrations')
            ->join('users', 'registrations.user_id', '=', 'users.id')
            ->join('users as user_doctor', 'registrations.doctor_user_id', '=', 'user_doctor.id')
            ->join('patients', 'registrations.patient_id', '=', 'patients.id')
            ->join('branches', 'patients.branch_id', '=', 'branches.id')
            ->select(
                'registrations.id_number',
                'patients.id_member',
                'patients.pet_name',
                'patients.owner_name',
                'complaint',
                'registrant',
                'user_doctor.username',
                'users.fullname',
                DB::raw("DATE_FORMAT(registrations.created_at, '%d %b %Y') as created_at")
            )
            ->where('registrations.isDeleted', '=', 0);

        if ($request->user()->role == 'resepsionis') {
            $data = $data->where('users.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->user()->role == 'dokter') {
            $data = $data->where('user_doctor.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->branch_id && $request->user()->role == 'admin') {
            $data = $data->where('user_doctor.branch_id', '=', $request->branch_id);
        }

        if ($request->keyword) {
            $data = $data->where('complaint', 'like', '%' . $request->keyword . '%');
        }

        $data = $data->get();

        if (count($data)) {
            $temp_column = 'complaint';
            return $temp_column;
        }
        //==================================================

        $data = DB::table('registrations')
            ->join('users', 'registrations.user_id', '=', 'users.id')
            ->join('users as user_doctor', 'registrations.doctor_user_id', '=', 'user_doctor.id')
            ->join('patients', 'registrations.patient_id', '=', 'patients.id')
            ->join('branches', 'patients.branch_id', '=', 'branches.id')
            ->select(
                'registrations.id_number',
                'patients.id_member',
                'patients.pet_name',
                'patients.owner_name',
                'complaint',
                'registrant',
                'user_doctor.username',
                'users.fullname',
                DB::raw("DATE_FORMAT(registrations.created_at, '%d %b %Y') as created_at")
            )
            ->where('registrations.isDeleted', '=', 0);

        if ($request->user()->role == 'resepsionis') {
            $data = $data->where('users.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->user()->role == 'dokter') {
            $data = $data->where('user_doctor.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->branch_id && $request->user()->role == 'admin') {
            $data = $data->where('user_doctor.branch_id', '=', $request->branch_id);
        }

        if ($request->keyword) {
            $data = $data->where('registrant', 'like', '%' . $request->keyword . '%');
        }

        $data = $data->get();

        if (count($data)) {
            $temp_column = 'registrant';
            return $temp_column;
        }
        //==================================================

        $data = DB::table('registrations')
            ->join('users', 'registrations.user_id', '=', 'users.id')
            ->join('users as user_doctor', 'registrations.doctor_user_id', '=', 'user_doctor.id')
            ->join('patients', 'registrations.patient_id', '=', 'patients.id')
            ->join('branches', 'patients.branch_id', '=', 'branches.id')
            ->select(
                'registrations.id_number',
                'patients.id_member',
                'patients.pet_name',
                'patients.owner_name',
                'complaint',
                'registrant',
                'user_doctor.username',
                'users.fullname',
                DB::raw("DATE_FORMAT(registrations.created_at, '%d %b %Y') as created_at")
            )
            ->where('registrations.isDeleted', '=', 0);

        if ($request->user()->role == 'resepsionis') {
            $data = $data->where('users.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->user()->role == 'dokter') {
            $data = $data->where('user_doctor.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->branch_id && $request->user()->role == 'admin') {
            $data = $data->where('user_doctor.branch_id', '=', $request->branch_id);
        }

        if ($request->keyword) {
            $data = $data->where('user_doctor.username', 'like', '%' . $request->keyword . '%');
        }

        $data = $data->get();

        if (count($data)) {
            $temp_column = 'user_doctor.username';
            return $temp_column;
        }
        //==================================================

        $data = DB::table('registrations')
            ->join('users', 'registrations.user_id', '=', 'users.id')
            ->join('users as user_doctor', 'registrations.doctor_user_id', '=', 'user_doctor.id')
            ->join('patients', 'registrations.patient_id', '=', 'patients.id')
            ->join('branches', 'patients.branch_id', '=', 'branches.id')
            ->select(
                'registrations.id_number',
                'patients.id_member',
                'patients.pet_name',
                'patients.owner_name',
                'complaint',
                'registrant',
                'user_doctor.username',
                'users.fullname',
                DB::raw("DATE_FORMAT(registrations.created_at, '%d %b %Y') as created_at")
            )
            ->where('registrations.isDeleted', '=', 0);

        if ($request->user()->role == 'resepsionis') {
            $data = $data->where('users.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->user()->role == 'dokter') {
            $data = $data->where('user_doctor.branch_id', '=', $request->user()->branch_id);
        }

        if ($request->branch_id && $request->user()->role == 'admin') {
            $data = $data->where('user_doctor.branch_id', '=', $request->branch_id);
        }

        if ($request->keyword) {
            $data = $data->where('users.fullname', 'like', '%' . $request->keyword . '%');
        }

        $data = $data->get();

        if (count($data)) {
            $temp_column = 'users.fullname';
            return $temp_column;
        }
        //==================================================
    }

    public function create(Request $request)
    {
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

        $lasttransaction = DB::table('registrations')
            ->join('users', 'registrations.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->where('branch_id', '=', $request->user()->branch_id)
            ->count();

        $getbranchuser = DB::table('branches')
            ->select('branch_code')
            ->where('id', '=', $request->user()->branch_id)
            ->first();

        $registration_number = 'BVC-RP-' . $getbranchuser->branch_code . '-' . str_pad($lasttransaction + 1, 4, 0, STR_PAD_LEFT);

        $patient = Registration::create([
            'id_number' => $registration_number,
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

        $registration = Registration::find($request->id);

        if (is_null($registration)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        } elseif ($registration->acceptance_status == 1) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak dapat diubah karena sudah diterima oleh dokter!'],
            ], 422);
        }

        $registration->patient_id = $request->patient_id;
        $registration->doctor_user_id = $request->doctor_user_id;
        $registration->complaint = $request->keluhan;
        $registration->registrant = $request->nama_pendaftar;
        $registration->user_update_id = $request->user()->id;
        $registration->acceptance_status = 0;
        $registration->updated_at = \Carbon\Carbon::now();
        $registration->save();

        return response()->json([
            'message' => 'Berhasil mengupdate Data',
        ], 200);
    }

    public function delete(Request $request)
    {
        $registration = Registration::find($request->id);

        if (is_null($registration)) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ditemukan!'],
            ], 404);
        } elseif ($registration->acceptance_status == 1 || $registration->acceptance_status == 3) {

            if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
                return response()->json([
                    'message' => 'The user role was invalid.',
                    'errors' => ['Akses User tidak diizinkan!'],
                ], 403);
            }
        }

        $registration->isDeleted = true;
        $registration->deleted_by = $request->user()->fullname;
        $registration->deleted_at = \Carbon\Carbon::now();
        $registration->save();

        return response()->json([
            'message' => 'Berhasil menghapus Data',
        ], 200);
    }
}

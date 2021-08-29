<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use DB;
use Illuminate\Http\Request;
use PDF;
use Response;
use Validator;

class PenggajianController extends Controller
{
    // private $fpdf;

    public function index(Request $request)
    {

        $data = DB::table('payrolls as py')
            ->join('users', 'py.user_employee_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->select(
                'py.id as id',
                'users.fullname as fullname',
                'py.date_payed as date_payed',
                'branches.branch_name as branch_name',
                DB::raw("TRIM(py.basic_sallary)+0 as basic_sallary"),
                DB::raw("TRIM(py.accomodation)+0 as accomodation"),
                DB::raw("TRIM(py.percentage_turnover)+0 as percentage_turnover"),
                DB::raw("TRIM(py.amount_turnover)+0 as amount_turnover"),
                DB::raw("TRIM(py.total_turnover)+0 as total_turnover"),
                DB::raw("TRIM(py.amount_inpatient)+0 as amount_inpatient"),
                DB::raw("TRIM(py.count_inpatient)+0 as count_inpatient"),
                DB::raw("TRIM(py.total_inpatient)+0 as total_inpatient"),
                DB::raw("TRIM(py.percentage_surgery)+0 as percentage_surgery"),
                DB::raw("TRIM(py.amount_surgery)+0 as amount_surgery"),
                DB::raw("TRIM(py.total_surgery)+0 as total_surgery"),
                DB::raw("TRIM(py.total_overall)+0 as total_overall"),
            );

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            $data = $data->where('py.user_employee_id', '=', $request->user()->id);
        }

        if ($request->branch_id && $request->user()->role == 'admin') {
            $data = $data->where('users.branch_id', '=', $request->branch_id);
        }

        if ($request->orderby) {
            $data = $data->orderBy($request->column, $request->orderby);
        }

        $data = $data->orderBy('py.id', 'desc');

        $data = $data->get();

        return response()->json($data, 200);

    }

    public function create(Request $request)
    {
        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $validate = Validator::make($request->all(), [
            'date_payed' => 'required|date',
            'user_employee_id' => 'required|numeric',
            'basic_sallary' => 'required|numeric|min:0',
            'accomodation' => 'required|numeric|min:0',
            'percentage_turnover' => 'required|numeric|min:0',
            'amount_turnover' => 'required|numeric|min:0',
            'total_turnover' => 'required|numeric|min:0',
            'amount_inpatient' => 'required|numeric|min:0',
            'count_inpatient' => 'required|numeric|min:0',
            'total_inpatient' => 'required|numeric|min:0',
            'percentage_surgery' => 'required|numeric|min:0',
            'amount_surgery' => 'required|numeric|min:0',
            'total_surgery' => 'required|numeric|min:0',
            'total_overall' => 'required|numeric|min:0',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $errors,
            ], 422);
        }

        $find_duplicate = db::table('payrolls')
            ->select('id')
            ->where('user_employee_id', '=', $request->user_employee_id)
            ->where('date_payed', '=', $request->date_payed)
            ->where('isDeleted', '=', 0)
            ->count();

        if ($find_duplicate != 0) {

            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data sudah ada!'],
            ], 422);

        }

        $payroll = Payroll::create([
            'user_employee_id' => $request->user_employee_id,
            'date_payed' => $request->date_payed,
            'basic_sallary' => $request->basic_sallary,
            'accomodation' => $request->accomodation,
            'percentage_turnover' => $request->percentage_turnover,
            'amount_turnover' => $request->amount_turnover,
            'total_turnover' => $request->total_turnover,
            'amount_inpatient' => $request->amount_inpatient,
            'count_inpatient' => $request->count_inpatient,
            'total_inpatient' => $request->total_inpatient,
            'percentage_surgery' => $request->percentage_surgery,
            'amount_surgery' => $request->amount_surgery,
            'total_surgery' => $request->total_surgery,
            'total_overall' => $request->total_overall,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'id' => $payroll->id,
            'message' => 'Berhasil menambah Data',
        ], 200);

    }

    public function sallary_user(Request $request)
    {
        $amount_turnover = DB::table('users as usr')
            ->join('branches as brn', 'usr.branch_id', 'brn.id')
            ->join('detail_medicine_group_check_up_results as dmg', 'dmg.user_id', 'usr.id')
            ->join('price_medicine_groups as pmg', 'dmg.medicine_group_id', 'pmg.id')
            ->select(DB::raw("TRIM(SUM(pmg.doctor_fee))+0 as amount_turnover"))
            ->where('usr.id', '=', $request->id)
            ->where(DB::raw('DATE(dmg.updated_at)'), '=', $request->date)
            ->first();

        $count_inpatient = DB::table('users as usr')
            ->join('branches as brn', 'usr.branch_id', 'brn.id')
            ->join('check_up_results as cur', 'usr.id', 'cur.user_id')
            ->where('usr.id', '=', $request->id)
            ->where(DB::raw('DATE(cur.updated_at)'), '=', $request->date)
            ->where('cur.status_outpatient_inpatient', '=', 1)
            ->count();

        $amount_surgery = DB::table('users as usr')
            ->join('branches as brn', 'usr.branch_id', 'brn.id')
            ->join('detail_medicine_group_check_up_results as dmg', 'dmg.user_id', 'usr.id')
            ->join('price_medicine_groups as pmg', 'dmg.medicine_group_id', 'pmg.id')
            ->select(DB::raw("TRIM(SUM(pmg.doctor_fee))+0 as amount_surgery"))
            ->where(DB::raw('DATE(dmg.updated_at)'), '=', $request->date)
            ->where('usr.id', '=', $request->id)
            ->first();

        return response()->json([
            'amount_turnover' => $amount_turnover->amount_turnover,
            'count_inpatient' => $count_inpatient,
            'amount_surgery' => $amount_surgery->amount_surgery,
        ], 200);
    }

    public function update(Request $request)
    {
        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $validate = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'date_payed' => 'required|date',
            'user_employee_id' => 'required|numeric',
            'basic_sallary' => 'required|numeric|min:0',
            'accomodation' => 'required|numeric|min:0',
            'percentage_turnover' => 'required|numeric|min:0',
            'amount_turnover' => 'required|numeric|min:0',
            'total_turnover' => 'required|numeric|min:0',
            'amount_inpatient' => 'required|numeric|min:0',
            'count_inpatient' => 'required|numeric|min:0',
            'total_inpatient' => 'required|numeric|min:0',
            'percentage_surgery' => 'required|numeric|min:0',
            'amount_surgery' => 'required|numeric|min:0',
            'total_surgery' => 'required|numeric|min:0',
            'total_overall' => 'required|numeric|min:0',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $errors,
            ], 422);
        }

        $find_duplicate = db::table('payrolls')
            ->select('id')
            ->where('id', '=', $request->id)
            ->count();

        if ($find_duplicate == 0) {

            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ada!'],
            ], 422);

        }

        $payroll = Payroll::find($request->id);

        $payroll->date_payed = $request->date_payed;
        $payroll->user_employee_id = $request->user_employee_id;
        $payroll->basic_sallary = $request->basic_sallary;
        $payroll->accomodation = $request->accomodation;
        $payroll->percentage_turnover = $request->percentage_turnover;
        $payroll->amount_turnover = $request->amount_turnover;
        $payroll->total_turnover = $request->total_turnover;
        $payroll->amount_inpatient = $request->amount_inpatient;
        $payroll->count_inpatient = $request->count_inpatient;
        $payroll->total_inpatient = $request->total_inpatient;
        $payroll->percentage_surgery = $request->percentage_surgery;
        $payroll->amount_surgery = $request->amount_surgery;
        $payroll->total_surgery = $request->total_surgery;
        $payroll->total_overall = $request->total_overall;
        $payroll->user_update_id = $request->user()->id;
        $payroll->updated_at = \Carbon\Carbon::now();
        $payroll->save();

        return response()->json([
            'message' => 'Berhasil mengubah Data',
        ], 200);
    }
    public function delete(Request $request)
    {

        if ($request->user()->role == 'dokter' || $request->user()->role == 'resepsionis') {
            return response()->json([
                'message' => 'The user role was invalid.',
                'errors' => ['Akses User tidak diizinkan!'],
            ], 403);
        }

        $data = Payroll::where('id', '=', $request->id)
            ->where('isDeleted', '=', 0)
            ->count();

        if ($data == 0) {
            return response()->json([
                'message' => 'The data was invalid.',
                'errors' => ['Data tidak ada ada!'],
            ], 422);
        }

        $data = Payroll::find($request->id);

        $data->user_update_id = $request->user()->id;
        $data->isDeleted = 1;
        $data->deleted_by = $request->user()->id;
        $data->updated_at = \Carbon\Carbon::now();
        $data->deleted_at = \Carbon\Carbon::now();
        $data->save();

        return response()->json([
            'message' => 'Berhasil menghapus Data',
        ], 200);

    }

    public function generate(Request $request)
    {

        // $pdf = app('Fpdf');
        // $pdf->AddPage();
        // $pdf->SetFont('Arial', 'B', 16);
        // $pdf->Cell(40, 10, 'Hello World!');
        // $pdf->Output('F', 'filename3.pdf');

        // $file = public_path() . "/filename3.pdf";

        // $headers = array(
        //     'Content-Type: application/pdf',
        // );

        // return Response::download($file, 'filename3.pdf', $headers);

        $data_user = DB::table('payrolls as py')
            ->join('users', 'py.user_employee_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->select(
                'py.id as id',
                'users.fullname as fullname',
                'py.date_payed as date_payed',
                'users.phone_number as phone_number',
                'users.address as address',
                'users.role as role',
                'branches.branch_name as branch_name',
                'branches.address as branch_address',
                'py.basic_sallary as basic_sallary',
                'py.accomodation as accomodation',
                'py.total_turnover as total_turnover',
                'py.total_inpatient as total_inpatient',
                'py.total_surgery as total_surgery',
                'py.total_overall as total_overall',
            )
            ->where('py.id', '=', $request->id)
            ->get();

        $data = ['data_user' => $data_user];

        $pdf = PDF::loadview('sallary-slip', $data);

        return $pdf->download('testing' . '.pdf');
    }

}

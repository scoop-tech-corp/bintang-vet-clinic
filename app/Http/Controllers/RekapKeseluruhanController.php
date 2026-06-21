<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RekapKeseluruhanController extends Controller
{
    private function getFirstTransactionDate(Request $request = null): string
    {
        // Jika branch tertentu dipilih, cari tanggal transaksi pertama branch itu saja
        if ($request && $request->branch_id && $request->connection) {
            try {
                $row = DB::connection($request->connection)
                    ->table('list_of_payments as lop')
                    ->join('users', 'lop.user_id', '=', 'users.id')
                    ->join('branches', 'users.branch_id', '=', 'branches.id')
                    ->selectRaw('MIN(DATE(lop.created_at)) as min_date')
                    ->where('branches.id', $request->branch_id)
                    ->first();

                if ($row && $row->min_date) {
                    return $row->min_date;
                }
            } catch (\Exception) {}

            return Carbon::now()->subYear()->format('Y-m-d');
        }

        // Semua branch → global minimum dari semua koneksi
        $conns   = ['mysql', 'mysql_second', 'mysql_third', 'mysql_forth'];
        $minDate = null;

        foreach ($conns as $conn) {
            try {
                $row = DB::connection($conn)
                    ->table('list_of_payments')
                    ->selectRaw('MIN(DATE(created_at)) as min_date')
                    ->first();

                if ($row && $row->min_date) {
                    if (!$minDate || $row->min_date < $minDate) {
                        $minDate = $row->min_date;
                    }
                }
            } catch (\Exception) {
                // skip connection jika tidak tersedia
            }
        }

        return $minDate ?? Carbon::now()->subYear()->format('Y-m-d');
    }

    /**
     * Build period list based on filter type.
     *
     * Returns: [$periods, $groupBy, $dateFrom, $dateTo]
     *   $periods  — array of ['key' => ..., 'label' => ...]
     *   $groupBy  — 'day' | 'month'
     *   $dateFrom — SQL-safe date string for WHERE
     *   $dateTo   — SQL-safe date string for WHERE
     */
    private function buildPeriods(Request $request): array
    {
        Carbon::setLocale('id');
        $periode = $request->periode ?? 'mingguan';
        $periods = [];
        $groupBy = 'day';

        switch ($periode) {
            case 'bulanan':
                $cur = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                while ($cur->lte($end)) {
                    $periods[] = [
                        'key'   => $cur->format('Y-m-d'),
                        'label' => $cur->translatedFormat('j M Y'),
                    ];
                    $cur->addDay();
                }
                $groupBy = 'day';
                break;

            case 'tahunan':
                $year = Carbon::now()->year;
                for ($m = 1; $m <= 12; $m++) {
                    $month = Carbon::createFromDate($year, $m, 1);
                    $periods[] = [
                        'key'   => $month->format('Y-m'),
                        'label' => $month->translatedFormat('F Y'),
                    ];
                }
                $groupBy = 'month';
                break;

            case 'sejak_dibuka':
                $firstDate = $this->getFirstTransactionDate($request);
                $cur = Carbon::parse($firstDate)->startOfMonth();
                $end = Carbon::now()->startOfMonth();
                while ($cur->lte($end)) {
                    $periods[] = [
                        'key'   => $cur->format('Y-m'),
                        'label' => $cur->translatedFormat('F Y'),
                    ];
                    $cur->addMonth();
                }
                $groupBy = 'month';
                break;

            default: // mingguan
                $cur = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                while ($cur->lte($end)) {
                    $periods[] = [
                        'key'   => $cur->format('Y-m-d'),
                        'label' => $cur->translatedFormat('l, j M Y'),
                    ];
                    $cur->addDay();
                }
                $groupBy = 'day';
                break;
        }

        $keys     = array_column($periods, 'key');
        $dateFrom = $groupBy === 'day'
            ? $keys[0]
            : Carbon::createFromFormat('Y-m', $keys[0])->startOfMonth()->format('Y-m-d');
        $dateTo   = $groupBy === 'day'
            ? $keys[count($keys) - 1]
            : Carbon::createFromFormat('Y-m', $keys[count($keys) - 1])->endOfMonth()->format('Y-m-d');

        return [$periods, $groupBy, $dateFrom, $dateTo];
    }

    // Load branches from all connections, adding branch_slug to each
    private function getBranches(Request $request): Collection
    {
        if ($request->branch_id) {
            return DB::connection($request->connection)->table('branches')
                ->select('id', 'branch_name', DB::raw("'{$request->connection}' as connection"))
                ->where('isDeleted', 0)
                ->where('id', $request->branch_id)
                ->get()
                ->map(function ($b) {
                    $b->branch_slug = Str::slug($b->branch_name, '_');
                    return $b;
                });
        }

        $sources = [
            'mysql'        => 4,
            'mysql_second' => 1,
            'mysql_third'  => null,
            'mysql_forth'  => null,
        ];

        $all = collect();
        foreach ($sources as $conn => $excludeId) {
            $q = DB::connection($conn)->table('branches')
                ->select('id', 'branch_name', DB::raw("'{$conn}' as connection"))
                ->where('isDeleted', 0);
            if ($excludeId !== null) {
                $q->where('id', '!=', $excludeId);
            }
            $all = $all->merge($q->get());
        }

        return $all->map(function ($b) {
            $b->branch_slug = Str::slug($b->branch_name, '_');
            return $b;
        })->values();
    }

    /**
     * Fetch net omset per branch per period in bulk.
     *
     * $groupBy 'day'   → groups by DATE(column),           key format Y-m-d
     * $groupBy 'month' → groups by DATE_FORMAT(column,'%Y-%m'), key format Y-m
     */
    private function fetchNetByBranchAndPeriod(
        Collection $branches,
        array $periods,
        string $groupBy,
        string $dateFrom,
        string $dateTo
    ): array {
        $keys   = array_column($periods, 'key');
        $netMap = [];

        $grpDay  = $groupBy === 'day';
        $grpExpr = $grpDay ? "DATE(%s)" : "DATE_FORMAT(%s, '%%Y-%%m')";

        foreach ($branches->groupBy('connection') as $conn => $connBranches) {
            $branchIds = $connBranches->pluck('id')->toArray();

            $g = fn(string $col) => DB::raw(sprintf($grpExpr, $col));

            // --- revenue: medicine items ---
            $itemRows = DB::connection($conn)->table('list_of_payments as lop')
                ->join('list_of_payment_medicine_groups as lopm', 'lop.id', '=', 'lopm.list_of_payment_id')
                ->join('price_medicine_groups as pmg', 'lopm.medicine_group_id', '=', 'pmg.id')
                ->join('users', 'lop.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->select(
                    'branches.id as branch_id',
                    DB::raw(sprintf($grpExpr, 'lopm.updated_at') . ' as period'),
                    DB::raw("SUM(CASE WHEN lopm.quantity = 0 THEN pmg.selling_price ELSE pmg.selling_price * lopm.quantity END) as total")
                )
                ->whereIn('branches.id', $branchIds)
                ->whereBetween(DB::raw("DATE(lopm.updated_at)"), [$dateFrom, $dateTo])
                ->groupBy('branches.id', $g('lopm.updated_at'))
                ->get();

            // --- revenue: services ---
            $svcRows = DB::connection($conn)->table('list_of_payments')
                ->join('check_up_results', 'list_of_payments.check_up_result_id', '=', 'check_up_results.id')
                ->join('list_of_payment_services', 'check_up_results.id', '=', 'list_of_payment_services.check_up_result_id')
                ->join('detail_service_patients', 'list_of_payment_services.detail_service_patient_id', '=', 'detail_service_patients.id')
                ->join('price_services', 'detail_service_patients.price_service_id', '=', 'price_services.id')
                ->join('users', 'check_up_results.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->select(
                    'branches.id as branch_id',
                    DB::raw(sprintf($grpExpr, 'list_of_payment_services.updated_at') . ' as period'),
                    DB::raw("SUM(detail_service_patients.price_overall) as total")
                )
                ->whereIn('branches.id', $branchIds)
                ->whereBetween(DB::raw("DATE(list_of_payment_services.updated_at)"), [$dateFrom, $dateTo])
                ->groupBy('branches.id', $g('list_of_payment_services.updated_at'))
                ->get();

            // --- revenue: petshop + clinic ---
            $shopClinicRows = DB::connection($conn)->table('payment_petshop_with_clinics as ppwc')
                ->join('price_item_pet_shops as pip', 'ppwc.price_item_pet_shop_id', '=', 'pip.id')
                ->join('users', 'ppwc.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->select(
                    'branches.id as branch_id',
                    DB::raw(sprintf($grpExpr, 'ppwc.created_at') . ' as period'),
                    DB::raw("SUM(pip.selling_price * ppwc.total_item) as total")
                )
                ->whereIn('branches.id', $branchIds)
                ->whereBetween(DB::raw("DATE(ppwc.created_at)"), [$dateFrom, $dateTo])
                ->groupBy('branches.id', $g('ppwc.created_at'))
                ->get();

            // --- revenue: petshop only ---
            $shopRows = DB::connection($conn)->table('payment_petshops as pp')
                ->join('price_item_pet_shops as pip', 'pp.price_item_pet_shop_id', '=', 'pip.id')
                ->join('users', 'pp.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->select(
                    'branches.id as branch_id',
                    DB::raw(sprintf($grpExpr, 'pp.created_at') . ' as period'),
                    DB::raw("SUM(pip.selling_price * pp.total_item) as total")
                )
                ->whereIn('branches.id', $branchIds)
                ->whereBetween(DB::raw("DATE(pp.created_at)"), [$dateFrom, $dateTo])
                ->groupBy('branches.id', $g('pp.created_at'))
                ->get();

            // --- discount: medicine items ---
            $discItemRows = DB::connection($conn)->table('list_of_payments as lop')
                ->join('check_up_results as cur', 'lop.check_up_result_id', '=', 'cur.id')
                ->join('list_of_payment_medicine_groups as lopm', 'lopm.list_of_payment_id', '=', 'lop.id')
                ->join('price_medicine_groups as pmg', 'lopm.medicine_group_id', '=', 'pmg.id')
                ->join('registrations as reg', 'cur.patient_registration_id', '=', 'reg.id')
                ->join('patients as pa', 'reg.patient_id', '=', 'pa.id')
                ->join('users', 'lop.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->select(
                    'branches.id as branch_id',
                    DB::raw(sprintf($grpExpr, 'lopm.updated_at') . ' as period'),
                    DB::raw("SUM(lopm.amount_discount) as total")
                )
                ->whereIn('branches.id', $branchIds)
                ->whereBetween(DB::raw("DATE(lopm.updated_at)"), [$dateFrom, $dateTo])
                ->groupBy('branches.id', $g('lopm.updated_at'))
                ->get();

            // --- discount: services ---
            $discSvcRows = DB::connection($conn)->table('list_of_payments')
                ->join('check_up_results', 'list_of_payments.check_up_result_id', '=', 'check_up_results.id')
                ->join('list_of_payment_services', 'check_up_results.id', '=', 'list_of_payment_services.check_up_result_id')
                ->join('detail_service_patients', 'list_of_payment_services.detail_service_patient_id', '=', 'detail_service_patients.id')
                ->join('price_services', 'detail_service_patients.price_service_id', '=', 'price_services.id')
                ->join('users', 'check_up_results.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->select(
                    'branches.id as branch_id',
                    DB::raw(sprintf($grpExpr, 'list_of_payment_services.updated_at') . ' as period'),
                    DB::raw("SUM(list_of_payment_services.amount_discount) as total")
                )
                ->whereIn('branches.id', $branchIds)
                ->whereBetween(DB::raw("DATE(list_of_payment_services.updated_at)"), [$dateFrom, $dateTo])
                ->groupBy('branches.id', $g('list_of_payment_services.updated_at'))
                ->get();

            // Build in-memory lookup maps
            $rev  = [];
            $disc = [];

            foreach ([$itemRows, $svcRows, $shopClinicRows, $shopRows] as $rows) {
                foreach ($rows as $row) {
                    $rev[$row->branch_id][$row->period] = ($rev[$row->branch_id][$row->period] ?? 0) + (float) $row->total;
                }
            }
            foreach ([$discItemRows, $discSvcRows] as $rows) {
                foreach ($rows as $row) {
                    $disc[$row->branch_id][$row->period] = ($disc[$row->branch_id][$row->period] ?? 0) + (float) $row->total;
                }
            }

            foreach ($connBranches as $branch) {
                foreach ($keys as $key) {
                    $netMap[$branch->id][$key] = ($rev[$branch->id][$key] ?? 0) - ($disc[$branch->id][$key] ?? 0);
                }
            }
        }

        return $netMap;
    }

    public function index(Request $request)
    {
        [$periods, $groupBy, $dateFrom, $dateTo] = $this->buildPeriods($request);
        $branches = $this->getBranches($request);
        $netMap   = $this->fetchNetByBranchAndPeriod($branches, $periods, $groupBy, $dateFrom, $dateTo);

        $datas = collect();

        if ($request->branch_id) {
            foreach ($periods as $period) {
                $total = 0;
                foreach ($branches as $b) {
                    $total += $netMap[$b->id][$period['key']] ?? 0;
                }
                $datas->push(['dates' => $period['label'], 'total_omset' => $total]);
            }

            return response()->json(['datas' => $datas->toArray(), 'branches' => $branches], 200);
        }

        foreach ($periods as $period) {
            $row   = ['dates' => $period['label']];
            $total = 0;
            foreach ($branches as $b) {
                $net                  = $netMap[$b->id][$period['key']] ?? 0;
                $row[$b->branch_slug] = $net;
                $total               += $net;
            }
            $row['total_omset'] = $total;
            $datas->push($row);
        }

        return response()->json(['datas' => $datas->toArray(), 'branches' => $branches], 200);
    }

    public function chart(Request $request)
    {
        [$periods, $groupBy, $dateFrom, $dateTo] = $this->buildPeriods($request);
        $branches = $this->getBranches($request);
        $netMap   = $this->fetchNetByBranchAndPeriod($branches, $periods, $groupBy, $dateFrom, $dateTo);

        $datas = collect();
        foreach ($periods as $period) {
            $total = 0;
            foreach ($branches as $b) {
                $total += $netMap[$b->id][$period['key']] ?? 0;
            }
            $datas->push(['dates' => $period['label'], 'total_omset' => $total]);
        }

        return response()->json($datas, 200);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RekapKeseluruhanController extends Controller
{
    // Build parallel date arrays: raw Y-m-d keys and translated display labels
    private function buildDateRange(Request $request): array
    {
        Carbon::setLocale('id');
        $dates  = [];
        $labels = [];

        if ($request->date_from && $request->date_to) {
            $cur = Carbon::parse($request->date_from);
            $end = Carbon::parse($request->date_to);
            while ($cur->lte($end)) {
                $dates[]  = $cur->format('Y-m-d');
                $labels[] = $cur->translatedFormat('l, j M Y');
                $cur->addDay();
            }
        } else {
            $cur = Carbon::now()->subDays(6);
            for ($i = 0; $i < 7; $i++) {
                $dates[]  = $cur->format('Y-m-d');
                $labels[] = $cur->translatedFormat('l, j M Y');
                $cur->addDay();
            }
        }

        return [$dates, $labels];
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

        // connection => branch_id to exclude (null = no exclusion)
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
     * Fetch net omset (revenue - discount) per branch per date in bulk.
     *
     * Instead of 1 query per metric per branch per date, runs 6 queries per
     * connection (GROUP BY branch_id + DATE), then assembles a lookup map.
     *
     * Returns: $map[$branchId][$date] = net float
     */
    private function fetchNetByBranchAndDate(Collection $branches, array $dates): array
    {
        $dateFrom = $dates[0];
        $dateTo   = $dates[count($dates) - 1];
        $netMap   = [];

        foreach ($branches->groupBy('connection') as $conn => $connBranches) {
            $branchIds = $connBranches->pluck('id')->toArray();

            // --- revenue: medicine items ---
            $itemRows = DB::connection($conn)->table('list_of_payments as lop')
                ->join('list_of_payment_medicine_groups as lopm', 'lop.id', '=', 'lopm.list_of_payment_id')
                ->join('price_medicine_groups as pmg', 'lopm.medicine_group_id', '=', 'pmg.id')
                ->join('users', 'lop.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->select(
                    'branches.id as branch_id',
                    DB::raw("DATE(lopm.updated_at) as day"),
                    DB::raw("SUM(CASE WHEN lopm.quantity = 0 THEN pmg.selling_price ELSE pmg.selling_price * lopm.quantity END) as total")
                )
                ->whereIn('branches.id', $branchIds)
                ->whereBetween(DB::raw("DATE(lopm.updated_at)"), [$dateFrom, $dateTo])
                ->groupBy('branches.id', DB::raw("DATE(lopm.updated_at)"))
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
                    DB::raw("DATE(list_of_payment_services.updated_at) as day"),
                    DB::raw("SUM(detail_service_patients.price_overall) as total")
                )
                ->whereIn('branches.id', $branchIds)
                ->whereBetween(DB::raw("DATE(list_of_payment_services.updated_at)"), [$dateFrom, $dateTo])
                ->groupBy('branches.id', DB::raw("DATE(list_of_payment_services.updated_at)"))
                ->get();

            // --- revenue: petshop + clinic ---
            $shopClinicRows = DB::connection($conn)->table('payment_petshop_with_clinics as ppwc')
                ->join('price_item_pet_shops as pip', 'ppwc.price_item_pet_shop_id', '=', 'pip.id')
                ->join('users', 'ppwc.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->select(
                    'branches.id as branch_id',
                    DB::raw("DATE(ppwc.created_at) as day"),
                    DB::raw("SUM(pip.selling_price * ppwc.total_item) as total")
                )
                ->whereIn('branches.id', $branchIds)
                ->whereBetween(DB::raw("DATE(ppwc.created_at)"), [$dateFrom, $dateTo])
                ->groupBy('branches.id', DB::raw("DATE(ppwc.created_at)"))
                ->get();

            // --- revenue: petshop only ---
            $shopRows = DB::connection($conn)->table('payment_petshops as pp')
                ->join('price_item_pet_shops as pip', 'pp.price_item_pet_shop_id', '=', 'pip.id')
                ->join('users', 'pp.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->select(
                    'branches.id as branch_id',
                    DB::raw("DATE(pp.created_at) as day"),
                    DB::raw("SUM(pip.selling_price * pp.total_item) as total")
                )
                ->whereIn('branches.id', $branchIds)
                ->whereBetween(DB::raw("DATE(pp.created_at)"), [$dateFrom, $dateTo])
                ->groupBy('branches.id', DB::raw("DATE(pp.created_at)"))
                ->get();

            // --- discount: medicine items (preserves original join chain for consistent filtering) ---
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
                    DB::raw("DATE(lopm.updated_at) as day"),
                    DB::raw("SUM(lopm.amount_discount) as total")
                )
                ->whereIn('branches.id', $branchIds)
                ->whereBetween(DB::raw("DATE(lopm.updated_at)"), [$dateFrom, $dateTo])
                ->groupBy('branches.id', DB::raw("DATE(lopm.updated_at)"))
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
                    DB::raw("DATE(list_of_payment_services.updated_at) as day"),
                    DB::raw("SUM(list_of_payment_services.amount_discount) as total")
                )
                ->whereIn('branches.id', $branchIds)
                ->whereBetween(DB::raw("DATE(list_of_payment_services.updated_at)"), [$dateFrom, $dateTo])
                ->groupBy('branches.id', DB::raw("DATE(list_of_payment_services.updated_at)"))
                ->get();

            // Build in-memory lookup maps
            $rev  = [];
            $disc = [];

            foreach ([$itemRows, $svcRows, $shopClinicRows, $shopRows] as $rows) {
                foreach ($rows as $row) {
                    $rev[$row->branch_id][$row->day] = ($rev[$row->branch_id][$row->day] ?? 0) + (float) $row->total;
                }
            }
            foreach ([$discItemRows, $discSvcRows] as $rows) {
                foreach ($rows as $row) {
                    $disc[$row->branch_id][$row->day] = ($disc[$row->branch_id][$row->day] ?? 0) + (float) $row->total;
                }
            }

            foreach ($connBranches as $branch) {
                foreach ($dates as $date) {
                    $netMap[$branch->id][$date] = ($rev[$branch->id][$date] ?? 0) - ($disc[$branch->id][$date] ?? 0);
                }
            }
        }

        return $netMap;
    }

    public function index(Request $request)
    {
        [$dates, $labels] = $this->buildDateRange($request);
        $branches = $this->getBranches($request);
        $netMap   = $this->fetchNetByBranchAndDate($branches, $dates);

        $datas = collect();

        if ($request->branch_id) {
            foreach ($dates as $i => $date) {
                $total = 0;
                foreach ($branches as $b) {
                    $total += $netMap[$b->id][$date] ?? 0;
                }
                $datas->push(['dates' => $labels[$i], 'total_omset' => $total]);
            }

            return response()->json(['datas' => $datas->toArray(), 'branches' => $branches], 200);
        }

        foreach ($dates as $i => $date) {
            $row   = ['dates' => $labels[$i]];
            $total = 0;
            foreach ($branches as $b) {
                $net                  = $netMap[$b->id][$date] ?? 0;
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
        [$dates, $labels] = $this->buildDateRange($request);
        $branches = $this->getBranches($request);
        $netMap   = $this->fetchNetByBranchAndDate($branches, $dates);

        $datas = collect();
        foreach ($dates as $i => $date) {
            $total = 0;
            foreach ($branches as $b) {
                $total += $netMap[$b->id][$date] ?? 0;
            }
            $datas->push(['dates' => $labels[$i], 'total_omset' => $total]);
        }

        return response()->json($datas, 200);
    }
}

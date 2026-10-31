<?php

namespace App\Http\Controllers;

use App\Exports\MitraVetItemTemplate;
use App\Exports\MitraVetOrderExport;
use App\Exports\MitraVetStockHistoryExport;
use App\Imports\MitraVetItemImport;
use App\Models\MitraVetCustomer;
use App\Models\MitraVetItem;
use App\Models\MitraVetOrder;
use App\Models\MitraVetOrderItem;
use App\Models\MitraVetStockHistory;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class MitraVetController extends Controller
{
    /**
     * Helper validasi role akses Mitra Vet
     */
    private function validateRoleAccess(Request $request, array $allowedRoles, $deniedMessage = 'Akses tidak diizinkan untuk role Anda.')
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
                'errors'  => ['Silakan login terlebih dahulu.'],
            ], 401);
        }

        $userRole = strtolower($user->role ?? '');
        $allowed = array_map('strtolower', $allowedRoles);

        if (!in_array($userRole, $allowed)) {
            return response()->json([
                'message' => 'Akses tidak diizinkan.',
                'errors'  => [$deniedMessage],
            ], 403);
        }

        return null;
    }

    // =========================================================
    //  STOK BARANG
    // =========================================================

    /**
     * GET /api/mitra-vet/items
     * Daftar barang beserta stok real, harga satuan, nilai aset, total aset
     * Role: admin, dokter, resepsionis
     */
    public function index(Request $request)
    {
        if ($deny = $this->validateRoleAccess($request, ['admin', 'dokter', 'resepsionis'], 'Hanya Admin, Dokter, dan Resepsionis yang dapat mengakses Mitra Vet.')) {
            return $deny;
        }

        $items_per_page = 50;
        $page           = $request->page ?? 1;

        $query = MitraVetItem::where('is_deleted', false)
            ->select(
                'id',
                'nama_barang',
                'satuan',
                DB::raw('TRIM(harga_satuan)+0 as harga_satuan'),
                'stok',
                DB::raw('TRIM(harga_satuan)+0 * stok as nilai_aset'),
                'created_by',
                DB::raw("DATE_FORMAT(created_at, '%d %b %Y') as created_at")
            );

        if ($request->keyword) {
            $query->where('nama_barang', 'like', '%' . $request->keyword . '%');
        }

        if ($request->orderby && $request->column) {
            $query->orderBy($request->column, $request->orderby);
        } else {
            $query->orderBy('nama_barang', 'asc');
        }

        $count_data  = $query->count();
        $offset      = ($page - 1) * $items_per_page;
        $total_paging = ceil($count_data / $items_per_page);

        $items = $query->offset($offset)->limit($items_per_page)->get();

        // Hitung total nilai aset keseluruhan
        $total_nilai_aset = MitraVetItem::where('is_deleted', false)
            ->selectRaw('SUM(harga_satuan * stok) as total')
            ->value('total') ?? 0;

        return response()->json([
            'total_paging'     => $total_paging,
            'data'             => $items,
            'total_nilai_aset' => $total_nilai_aset,
        ], 200);
    }

    /**
     * GET /api/mitra-vet/items/dropdown
     * Dropdown list barang aktif (untuk form order)
     */
    public function itemDropdown(Request $request)
    {
        if ($deny = $this->validateRoleAccess($request, ['admin', 'dokter', 'resepsionis'])) {
            return $deny;
        }

        $items = MitraVetItem::where('is_deleted', false)
            ->where('stok', '>', 0)
            ->select('id', 'nama_barang', 'satuan', 'harga_satuan', 'stok')
            ->orderBy('nama_barang')
            ->get();

        return response()->json($items, 200);
    }

    /**
     * POST /api/mitra-vet/items
     * Tambah barang baru
     * Role: admin, dokter
     */
    public function createItem(Request $request)
    {
        if ($deny = $this->validateRoleAccess($request, ['admin', 'dokter'], 'Hanya Admin dan Dokter yang dapat menambah barang baru. Resepsionis hanya dapat order barang.')) {
            return $deny;
        }

        $validate = Validator::make($request->all(), [
            'nama_barang'  => 'required|string|max:150',
            'satuan'       => 'required|string|max:50',
            'harga_satuan' => 'required|numeric|min:0',
            'stok'         => 'required|integer|min:0',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors'  => $validate->errors()->all(),
            ], 422);
        }

        // Cek duplikasi nama barang
        $exists = MitraVetItem::whereRaw('LOWER(nama_barang) = ?', [strtolower(trim($request->nama_barang))])
            ->where('is_deleted', false)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Data sudah ada.',
                'errors'  => ['Barang dengan nama "' . $request->nama_barang . '" sudah terdaftar!'],
            ], 422);
        }

        $item = MitraVetItem::create([
            'nama_barang'  => trim($request->nama_barang),
            'satuan'       => trim($request->satuan),
            'harga_satuan' => $request->harga_satuan,
            'stok'         => $request->stok,
            'created_by'   => $request->user()->id,
        ]);

        // Catat riwayat stok masuk awal
        if ($request->stok > 0) {
            MitraVetStockHistory::create([
                'item_id'    => $item->id,
                'quantity'   => $request->stok,
                'type'       => 'masuk',
                'keterangan' => 'Stok awal saat barang ditambahkan',
                'created_by' => $request->user()->id,
            ]);
        }

        return response()->json(['message' => 'Barang berhasil ditambahkan.'], 200);
    }

    /**
     * PUT /api/mitra-vet/items
     * Edit data barang (nama, satuan, harga) — tidak mengubah stok
     * Role: admin, dokter
     */
    public function updateItem(Request $request)
    {
        if ($deny = $this->validateRoleAccess($request, ['admin', 'dokter'], 'Hanya Admin dan Dokter yang dapat mengubah data barang. Resepsionis hanya dapat order barang.')) {
            return $deny;
        }

        $validate = Validator::make($request->all(), [
            'id'           => 'required|integer',
            'nama_barang'  => 'required|string|max:150',
            'satuan'       => 'required|string|max:50',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors'  => $validate->errors()->all(),
            ], 422);
        }

        $item = MitraVetItem::find($request->id);
        if (!$item || $item->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan.',
                'errors'  => ['Barang tidak ditemukan.'],
            ], 404);
        }

        // Cek duplikasi nama (kecuali dirinya sendiri)
        $exists = MitraVetItem::whereRaw('LOWER(nama_barang) = ?', [strtolower(trim($request->nama_barang))])
            ->where('is_deleted', false)
            ->where('id', '!=', $request->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Data sudah ada.',
                'errors'  => ['Barang dengan nama "' . $request->nama_barang . '" sudah terdaftar!'],
            ], 422);
        }

        $item->nama_barang  = trim($request->nama_barang);
        $item->satuan       = trim($request->satuan);
        $item->harga_satuan = $request->harga_satuan;
        $item->updated_by   = $request->user()->id;
        $item->save();

        return response()->json(['message' => 'Barang berhasil diubah.'], 200);
    }

    /**
     * POST /api/mitra-vet/tambah-stok
     * Tambah jumlah stok barang yang sudah ada
     * Role: admin, dokter
     */
    public function tambahStok(Request $request)
    {
        if ($deny = $this->validateRoleAccess($request, ['admin', 'dokter'], 'Hanya Admin dan Dokter yang dapat menambah stok barang. Resepsionis hanya dapat order barang.')) {
            return $deny;
        }

        $validate = Validator::make($request->all(), [
            'item_id'    => 'required|integer',
            'quantity'   => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors'  => $validate->errors()->all(),
            ], 422);
        }

        $item = MitraVetItem::find($request->item_id);
        if (!$item || $item->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan.',
                'errors'  => ['Barang tidak ditemukan.'],
            ], 404);
        }

        $item->stok      += $request->quantity;
        $item->updated_by = $request->user()->id;
        $item->save();

        MitraVetStockHistory::create([
            'item_id'    => $item->id,
            'quantity'   => $request->quantity,
            'type'       => 'masuk',
            'keterangan' => $request->keterangan ?? 'Penambahan stok manual',
            'created_by' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Stok berhasil ditambahkan.'], 200);
    }

    /**
     * DELETE /api/mitra-vet/items
     * Hapus barang (soft delete)
     * Role: admin only
     */
    public function deleteItem(Request $request)
    {
        if ($deny = $this->validateRoleAccess($request, ['admin'], 'Hanya Admin yang dapat menghapus barang.')) {
            return $deny;
        }

        $item = MitraVetItem::find($request->id);
        if (!$item || $item->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan.',
                'errors'  => ['Barang tidak ditemukan.'],
            ], 404);
        }

        $item->is_deleted = true;
        $item->updated_by = $request->user()->id;
        $item->save();

        return response()->json(['message' => 'Barang berhasil dihapus.'], 200);
    }

    // =========================================================
    //  UPLOAD EXCEL
    // =========================================================

    /**
     * GET /api/mitra-vet/items/download-template
     * Download template Excel untuk upload massal
     */
    public function downloadTemplate(Request $request)
    {
        if ($deny = $this->validateRoleAccess($request, ['admin', 'dokter'], 'Hanya Admin dan Dokter yang dapat mendownload template barang.')) {
            return $deny;
        }

        return Excel::download(new MitraVetItemTemplate, 'Template Produk Mitra Vet.xlsx');
    }

    /**
     * POST /api/mitra-vet/items/upload
     * Upload Excel untuk input massal produk
     * Role: admin, dokter
     */
    public function uploadItems(Request $request)
    {
        if ($deny = $this->validateRoleAccess($request, ['admin', 'dokter'], 'Hanya Admin dan Dokter yang dapat mengupload barang baru. Resepsionis hanya dapat order barang.')) {
            return $deny;
        }

        $validate = Validator::make($request->all(), [
            'file' => 'required|mimes:xls,xlsx|max:5120',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'File tidak valid.',
                'errors'  => $validate->errors()->all(),
            ], 422);
        }

        try {
            $import = new MitraVetItemImport($request->user()->id);

            // Baca dulu untuk preview & validasi sebelum import
            $rows = Excel::toArray(new MitraVetItemImport($request->user()->id), $request->file('file'));
            $data = $rows[0] ?? [];

            if (empty($data)) {
                return response()->json([
                    'message' => 'File kosong.',
                    'errors'  => ['File Excel tidak memiliki data.'],
                ], 422);
            }

            // Validasi kolom wajib ada
            $requiredColumns = ['nama_barang', 'satuan', 'harga_satuan', 'stok'];
            $firstRow        = array_keys($data[0] ?? []);
            $missingCols     = array_diff($requiredColumns, $firstRow);

            if (!empty($missingCols)) {
                return response()->json([
                    'message' => 'Format file tidak sesuai.',
                    'errors'  => ['Kolom berikut tidak ditemukan: ' . implode(', ', $missingCols)],
                ], 422);
            }

            // Validasi tiap baris
            $validationErrors = [];
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 karena baris 1 adalah header

                if (empty(trim($row['nama_barang'] ?? ''))) {
                    $validationErrors[] = "Baris {$rowNumber}: nama_barang wajib diisi.";
                }
                if (empty(trim($row['satuan'] ?? ''))) {
                    $validationErrors[] = "Baris {$rowNumber}: satuan wajib diisi.";
                }
                if (!is_numeric($row['harga_satuan'] ?? '') || $row['harga_satuan'] < 0) {
                    $validationErrors[] = "Baris {$rowNumber}: harga_satuan harus berupa angka >= 0.";
                }
                if (!is_numeric($row['stok'] ?? '') || (int) $row['stok'] < 0) {
                    $validationErrors[] = "Baris {$rowNumber}: stok harus berupa angka bulat >= 0.";
                }
            }

            if (!empty($validationErrors)) {
                return response()->json([
                    'message' => 'Terdapat kesalahan pada data.',
                    'errors'  => $validationErrors,
                ], 422);
            }

            // Proses import
            $inserted = 0;
            $updated  = 0;
            $updatedNames = [];
            $insertedNames = [];

            DB::beginTransaction();
            try {
                foreach ($data as $row) {
                    $namaBarangClean = trim(preg_replace('/\s+/', ' ', $row['nama_barang']));
                    $qtyTambah       = (int) $row['stok'];
                    $hargaSatuan     = (float) str_replace(',', '', $row['harga_satuan']);
                    $satuan          = trim($row['satuan']);

                    // Cek apakah barang sudah ada di tabel (case-insensitive)
                    $existing = MitraVetItem::whereRaw('LOWER(nama_barang) = ?', [strtolower($namaBarangClean)])
                        ->where('is_deleted', false)
                        ->first();

                    if ($existing) {
                        // JIKA SUDAH ADA: TAMBAH STOK KE BARANG TERSEBUT
                        $stokSebelumnya       = $existing->stok;
                        $existing->stok      += $qtyTambah;
                        $existing->satuan     = $satuan ?: $existing->satuan;
                        if ($hargaSatuan > 0) {
                            $existing->harga_satuan = $hargaSatuan;
                        }
                        $existing->updated_by = $request->user()->id;
                        $existing->save();

                        if ($qtyTambah > 0) {
                            MitraVetStockHistory::create([
                                'item_id'    => $existing->id,
                                'quantity'   => $qtyTambah,
                                'type'       => 'masuk',
                                'keterangan' => "Penambahan stok via upload Excel (Stok: {$stokSebelumnya} -> {$existing->stok})",
                                'created_by' => $request->user()->id,
                            ]);
                        }
                        $updated++;
                        $updatedNames[] = "{$existing->nama_barang} (+{$qtyTambah})";
                    } else {
                        // JIKA BELUM ADA: BUAT BARANG BARU BESERTA STOK AWALNYA
                        $item = MitraVetItem::create([
                            'nama_barang'  => $namaBarangClean,
                            'satuan'       => $satuan,
                            'harga_satuan' => $hargaSatuan,
                            'stok'         => $qtyTambah,
                            'created_by'   => $request->user()->id,
                        ]);

                        if ($qtyTambah > 0) {
                            MitraVetStockHistory::create([
                                'item_id'    => $item->id,
                                'quantity'   => $qtyTambah,
                                'type'       => 'masuk',
                                'keterangan' => 'Stok awal via upload Excel',
                                'created_by' => $request->user()->id,
                            ]);
                        }
                        $inserted++;
                        $insertedNames[] = "{$item->nama_barang} ({$qtyTambah} {$item->satuan})";
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Gagal menyimpan data ke database.',
                    'errors'  => [$e->getMessage()],
                ], 500);
            }

            $message = "Upload berhasil!";
            if ($inserted > 0) {
                $message .= " {$inserted} barang baru ditambahkan.";
            }
            if ($updated > 0) {
                $message .= " {$updated} barang sudah terdaftar & stoknya otomatis ditambahkan.";
            }

            return response()->json([
                'message'        => $message,
                'inserted'       => $inserted,
                'updated'        => $updated,
                'updated_items'  => $updatedNames,
                'inserted_items' => $insertedNames,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses file.',
                'errors'  => [$e->getMessage()],
            ], 500);
        }
    }

    // =========================================================
    //  ORDERAN
    // =========================================================

    /**
     * GET /api/mitra-vet/orders
     * Daftar orderan dengan filter bulan/tahun
     */
    public function getOrders(Request $request)
    {
        if ($deny = $this->validateRoleAccess($request, ['admin', 'dokter', 'resepsionis'])) {
            return $deny;
        }

        $items_per_page = 50;
        $page           = $request->page ?? 1;

        $query = DB::table('mitra_vet_orders as o')
            ->leftJoin('users as u', 'o.created_by', '=', 'u.id')
            ->select(
                'o.id',
                'o.nomor_order',
                'o.nama_pembeli',
                'o.alamat',
                'o.no_hp',
                DB::raw('TRIM(o.biaya_ongkir)+0 as biaya_ongkir'),
                DB::raw('TRIM(o.total_harga)+0 as total_harga'),
                DB::raw('TRIM(o.grand_total)+0 as grand_total'),
                'o.catatan',
                'u.fullname as created_by',
                DB::raw("DATE_FORMAT(o.created_at, '%d %b %Y %H:%i') as created_at")
            );

        if ($request->keyword) {
            $query->where(function ($q) use ($request) {
                $q->where('o.nama_pembeli', 'like', '%' . $request->keyword . '%')
                    ->orWhere('o.nomor_order', 'like', '%' . $request->keyword . '%')
                    ->orWhere('o.no_hp', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('o.created_at', $request->month)
                ->whereYear('o.created_at', $request->year);
        } elseif ($request->filled('year')) {
            $query->whereYear('o.created_at', $request->year);
        } elseif ($request->filled('month')) {
            $query->whereMonth('o.created_at', $request->month);
        }

        $query->orderBy('o.id', 'desc');

        $count_data   = $query->count();
        $offset       = ($page - 1) * $items_per_page;
        $total_paging = ceil($count_data / $items_per_page);

        $orders = $query->offset($offset)->limit($items_per_page)->get();

        // Ambil rincian barang untuk setiap order yang ditampilkan
        $orderIds = $orders->pluck('id')->toArray();
        if (!empty($orderIds)) {
            $orderItems = DB::table('mitra_vet_order_items as oi')
                ->leftJoin('mitra_vet_items as i', 'oi.item_id', '=', 'i.id')
                ->whereIn('oi.order_id', $orderIds)
                ->select(
                    'oi.order_id',
                    'oi.quantity',
                    DB::raw('TRIM(oi.harga_satuan)+0 as harga_satuan'),
                    DB::raw('TRIM(oi.subtotal)+0 as subtotal'),
                    'i.nama_barang',
                    'i.satuan'
                )
                ->get()
                ->groupBy('order_id');

            foreach ($orders as $order) {
                $order->items = $orderItems->get($order->id, collect())->values();
            }
        }

        return response()->json([
            'total_paging' => $total_paging,
            'data'         => $orders,
        ], 200);
    }

    /**
     * POST /api/mitra-vet/orders
     * Buat orderan baru (stok berkurang otomatis)
     */
    public function createOrder(Request $request)
    {
        if ($deny = $this->validateRoleAccess($request, ['admin', 'dokter', 'resepsionis'], 'Hanya Admin, Dokter, dan Resepsionis yang dapat membuat orderan.')) {
            return $deny;
        }

        $validate = Validator::make($request->all(), [
            'nama_pembeli'  => 'required|string|max:150',
            'alamat'        => 'required|string',
            'no_hp'         => 'required|string|max:20',
            'biaya_ongkir'  => 'required|numeric|min:0',
            'catatan'       => 'nullable|string',
            'items'         => 'required|array|min:1',
            'items.*.item_id'  => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors'  => $validate->errors()->all(),
            ], 422);
        }

        // Cek stok semua item sebelum proses
        $itemsData  = $request->items;
        $orderItems = [];
        $totalHarga = 0;

        foreach ($itemsData as $orderItem) {
            $item = MitraVetItem::find($orderItem['item_id']);

            if (!$item || $item->is_deleted) {
                return response()->json([
                    'message' => 'Data barang tidak ditemukan.',
                    'errors'  => ["Barang dengan ID {$orderItem['item_id']} tidak ditemukan."],
                ], 422);
            }

            if ($item->stok < $orderItem['quantity']) {
                return response()->json([
                    'message' => 'Stok tidak mencukupi.',
                    'errors'  => ["Stok {$item->nama_barang} tidak mencukupi. Stok tersedia: {$item->stok}, diminta: {$orderItem['quantity']}."],
                ], 422);
            }

            $subtotal     = $item->harga_satuan * $orderItem['quantity'];
            $totalHarga  += $subtotal;
            $orderItems[] = [
                'item'      => $item,
                'quantity'  => $orderItem['quantity'],
                'subtotal'  => $subtotal,
            ];
        }

        $grandTotal  = $totalHarga + $request->biaya_ongkir;
        $nomorOrder  = 'MV-' . Carbon::now()->format('Ymd') . '-' . str_pad(
            (MitraVetOrder::whereDate('created_at', Carbon::today())->count() + 1),
            4,
            '0',
            STR_PAD_LEFT
        );

        DB::beginTransaction();
        try {
            $customerId = $request->customer_id;
            if (!$customerId) {
                // Buat atau cari customer secara otomatis
                $cust = MitraVetCustomer::firstOrCreate(
                    ['nama' => trim($request->nama_pembeli), 'is_deleted' => false],
                    [
                        'no_hp'      => trim($request->no_hp),
                        'alamat'     => trim($request->alamat),
                        'created_by' => $request->user()->id,
                    ]
                );
                $customerId = $cust->id;
            } else {
                // Update no_hp dan alamat jika berbeda
                $cust = MitraVetCustomer::find($customerId);
                if ($cust) {
                    $cust->no_hp  = trim($request->no_hp);
                    $cust->alamat = trim($request->alamat);
                    $cust->save();
                }
            }

            $order = MitraVetOrder::create([
                'nomor_order'  => $nomorOrder,
                'customer_id'  => $customerId,
                'nama_pembeli' => $request->nama_pembeli,
                'alamat'       => $request->alamat,
                'no_hp'        => $request->no_hp,
                'biaya_ongkir' => $request->biaya_ongkir,
                'total_harga'  => $totalHarga,
                'grand_total'  => $grandTotal,
                'catatan'      => $request->catatan,
                'created_by'   => $request->user()->id,
            ]);

            foreach ($orderItems as $oi) {
                MitraVetOrderItem::create([
                    'order_id'    => $order->id,
                    'item_id'     => $oi['item']->id,
                    'quantity'    => $oi['quantity'],
                    'harga_satuan' => $oi['item']->harga_satuan,
                    'subtotal'    => $oi['subtotal'],
                ]);

                // Kurangi stok
                $oi['item']->stok      -= $oi['quantity'];
                $oi['item']->updated_by = $request->user()->id;
                $oi['item']->save();

                // Catat riwayat stok keluar
                MitraVetStockHistory::create([
                    'item_id'    => $oi['item']->id,
                    'quantity'   => $oi['quantity'],
                    'type'       => 'keluar',
                    'keterangan' => "Order {$nomorOrder} - {$request->nama_pembeli}",
                    'created_by' => $request->user()->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal membuat orderan.',
                'errors'  => [$e->getMessage()],
            ], 500);
        }

        return response()->json([
            'message'      => 'Orderan berhasil dibuat.',
            'nomor_order'  => $nomorOrder,
            'order_id'     => $order->id,
        ], 200);
    }

    /**
     * DELETE /api/mitra-vet/orders
     * Hapus orderan (stok dikembalikan) — admin only
     */
    public function deleteOrder(Request $request)
    {
        if ($deny = $this->validateRoleAccess($request, ['admin'], 'Hanya Admin yang dapat membatalkan dan menghapus orderan.')) {
            return $deny;
        }

        $order = MitraVetOrder::with('items.item')->find($request->id);
        if (!$order) {
            return response()->json([
                'message' => 'Data tidak ditemukan.',
                'errors'  => ['Orderan tidak ditemukan.'],
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Kembalikan stok
            foreach ($order->items as $oi) {
                if ($oi->item) {
                    $oi->item->stok      += $oi->quantity;
                    $oi->item->updated_by = $request->user()->id;
                    $oi->item->save();

                    MitraVetStockHistory::create([
                        'item_id'    => $oi->item->id,
                        'quantity'   => $oi->quantity,
                        'type'       => 'masuk',
                        'keterangan' => "Pembatalan order {$order->nomor_order}",
                        'created_by' => $request->user()->id,
                    ]);
                }
            }

            $order->items()->delete();
            $order->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menghapus orderan.',
                'errors'  => [$e->getMessage()],
            ], 500);
        }

        return response()->json(['message' => 'Orderan berhasil dihapus dan stok dikembalikan.'], 200);
    }

    /**
     * GET /mitra-vet/nota/{id}
     * Tampilkan nota order (HTML printable / downloadable)
     */
    public function notaOrder($id)
    {
        $order = MitraVetOrder::with(['items.item'])->find($id);

        if (!$order) {
            abort(404, 'Order tidak ditemukan.');
        }

        return view('mitra-vet.nota', compact('order'));
    }

    // =========================================================
    //  GRAFIK
    // =========================================================

    /**
     * GET /api/mitra-vet/chart
     * Data grafik rekap aset masuk/keluar per bulan
     */
    public function chartData(Request $request)
    {
        if ($deny = $this->validateRoleAccess($request, ['admin', 'dokter', 'resepsionis'])) {
            return $deny;
        }

        $year  = $request->year  ?? Carbon::now()->year;
        $month = $request->month ?? Carbon::now()->month;

        // Aset masuk: stok yang masuk dalam bulan tersebut (dari history)
        $masuk = DB::table('mitra_vet_stock_histories as h')
            ->join('mitra_vet_items as i', 'h.item_id', '=', 'i.id')
            ->select(
                DB::raw('DAY(h.created_at) as hari'),
                DB::raw('SUM(h.quantity) as total_qty'),
                DB::raw('SUM(h.quantity * i.harga_satuan) as total_nilai')
            )
            ->where('h.type', 'masuk')
            ->whereMonth('h.created_at', $month)
            ->whereYear('h.created_at', $year)
            ->groupBy(DB::raw('DAY(h.created_at)'))
            ->orderBy('hari')
            ->get();

        // Aset keluar: stok yang keluar dalam bulan tersebut
        $keluar = DB::table('mitra_vet_stock_histories as h')
            ->join('mitra_vet_items as i', 'h.item_id', '=', 'i.id')
            ->select(
                DB::raw('DAY(h.created_at) as hari'),
                DB::raw('SUM(h.quantity) as total_qty'),
                DB::raw('SUM(h.quantity * i.harga_satuan) as total_nilai')
            )
            ->where('h.type', 'keluar')
            ->whereMonth('h.created_at', $month)
            ->whereYear('h.created_at', $year)
            ->groupBy(DB::raw('DAY(h.created_at)'))
            ->orderBy('hari')
            ->get();

        // Summary total bulan ini
        $summary = [
            'total_masuk_qty'   => $masuk->sum('total_qty'),
            'total_masuk_nilai' => $masuk->sum('total_nilai'),
            'total_keluar_qty'  => $keluar->sum('total_qty'),
            'total_keluar_nilai' => $keluar->sum('total_nilai'),
        ];

        return response()->json([
            'masuk'   => $masuk,
            'keluar'  => $keluar,
            'summary' => $summary,
        ], 200);
    }

    // =========================================================
    //  CUSTOMER / MITRA PEMBELI
    // =========================================================

    /**
     * GET /api/mitra-vet/customers
     * Daftar customer untuk dropdown (nama, no_hp, alamat)
     */
    public function getCustomers(Request $request)
    {
        if ($deny = $this->validateRoleAccess($request, ['admin', 'dokter', 'resepsionis'])) {
            return $deny;
        }

        $customers = MitraVetCustomer::where('is_deleted', false)
            ->select('id', 'nama', 'no_hp', 'alamat')
            ->orderBy('nama', 'asc')
            ->get();

        return response()->json($customers, 200);
    }

    /**
     * POST /api/mitra-vet/customers
     * Tambah customer baru secara langsung
     */
    public function createCustomer(Request $request)
    {
        if ($deny = $this->validateRoleAccess($request, ['admin', 'dokter', 'resepsionis'])) {
            return $deny;
        }

        $validate = Validator::make($request->all(), [
            'nama'   => 'required|string|max:150',
            'no_hp'  => 'required|string|max:30',
            'alamat' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors'  => $validate->errors()->all(),
            ], 422);
        }

        $customer = MitraVetCustomer::create([
            'nama'       => trim($request->nama),
            'no_hp'      => trim($request->no_hp),
            'alamat'     => trim($request->alamat),
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'message'  => 'Pelanggan berhasil ditambahkan.',
            'customer' => $customer,
        ], 200);
    }

    // =========================================================
    //  EXPORT EXCEL ORDERAN
    // =========================================================

    /**
     * GET /api/mitra-vet/orders/export-excel
     * Download tarikan data orderan ke Excel
     * HASIL DISORT BERDASARKAN NAMA ORANG YANG ORDER
     */
    public function exportOrders(Request $request)
    {
        if ($deny = $this->validateRoleAccess($request, ['admin', 'dokter', 'resepsionis'])) {
            return $deny;
        }

        $month   = $request->month;
        $year    = $request->year;
        $keyword = $request->keyword;

        $fileName = 'Laporan Orderan Mitra Vet';
        if ($year) $fileName .= " {$year}";
        if ($month) $fileName .= " Bln {$month}";
        $fileName .= '.xlsx';

        return Excel::download(new MitraVetOrderExport($month, $year, $keyword), $fileName);
    }

    /**
     * GET /api/mitra-vet/stock-histories/export-excel
     * Download log riwayat penambahan stok masuk ke Excel
     * Role: admin, dokter
     */
    public function exportStockHistories(Request $request)
    {
        if ($deny = $this->validateRoleAccess($request, ['admin', 'dokter'], 'Hanya Admin dan Dokter yang dapat mendownload log stok.')) {
            return $deny;
        }

        $month   = $request->month;
        $year    = $request->year;
        $keyword = $request->keyword;

        $fileName = 'Log Mutasi Stok Mitra Vet';
        if ($year) $fileName .= " {$year}";
        if ($month) $fileName .= " Bln {$month}";
        $fileName .= '.xlsx';

        return Excel::download(new MitraVetStockHistoryExport($month, $year, $keyword), $fileName);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MitraVetOrder extends Model
{
    protected $table = 'mitra_vet_orders';

    protected $fillable = [
        'nomor_order',
        'customer_id',
        'nama_pembeli',
        'alamat',
        'no_hp',
        'biaya_ongkir',
        'total_harga',
        'grand_total',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'biaya_ongkir' => 'decimal:2',
        'total_harga'  => 'decimal:2',
        'grand_total'  => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(MitraVetCustomer::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(MitraVetOrderItem::class, 'order_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

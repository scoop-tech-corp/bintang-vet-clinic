<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MitraVetOrderItem extends Model
{
    protected $table = 'mitra_vet_order_items';

    protected $fillable = [
        'order_id',
        'item_id',
        'quantity',
        'harga_satuan',
        'subtotal',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'subtotal'     => 'decimal:2',
        'quantity'     => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(MitraVetOrder::class, 'order_id');
    }

    public function item()
    {
        return $this->belongsTo(MitraVetItem::class, 'item_id');
    }
}

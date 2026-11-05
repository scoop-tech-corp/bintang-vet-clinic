<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MitraVetStockHistory extends Model
{
    protected $table = 'mitra_vet_stock_histories';

    protected $fillable = [
        'item_id',
        'quantity',
        'type',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(MitraVetItem::class, 'item_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

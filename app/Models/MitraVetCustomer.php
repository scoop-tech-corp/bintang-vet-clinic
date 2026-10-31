<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MitraVetCustomer extends Model
{
    protected $table = 'mitra_vet_customers';

    protected $fillable = [
        'nama',
        'no_hp',
        'alamat',
        'created_by',
        'updated_by',
        'is_deleted',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(MitraVetOrder::class, 'customer_id');
    }
}

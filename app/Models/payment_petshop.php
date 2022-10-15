<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment_petshop extends Model
{
    protected $table = "payment_petshops";

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = ['price_item_pet_shop_id', 'master_payment_petshop_id',
        'total_item', 'user_id', 'user_update_id'];
}

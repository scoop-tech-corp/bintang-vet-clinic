<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment_petshop_with_clinic extends Model
{
    protected $table = "payment_petshop_with_clinics";

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = ['price_item_pet_shop_id','list_of_payment_id','payment_method_id',
        'total_item', 'user_id', 'user_update_id'];
}

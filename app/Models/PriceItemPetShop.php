<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceItemPetShop extends Model
{
    protected $table = "price_item_pet_shops";

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = ['list_of_item_pet_shop_id','selling_price','capital_price',
    'profit','branch_id','user_id'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListofPaymentItem extends Model
{
    protected $table = "list_of_payment_items";

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = ['list_of_payment_medicine_group_id', 'price_item_id', 'price_overall', 'quantity', 'user_id'];
}

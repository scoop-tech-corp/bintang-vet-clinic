<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    protected $table = "expenses";

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = ['date_spend', 'user_id_spender', 'item_name', 'quantity', 'amount', 'amount_overall', 'user_id', 'user_update_id'];
}
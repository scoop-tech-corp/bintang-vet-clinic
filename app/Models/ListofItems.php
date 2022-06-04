<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListofItems extends Model
{
    protected $table = "list_of_items";

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = ['item_name',
        'total_item',
        'unit_item_id',
        'category_item_id',
        'branch_id',
        'user_id',
        'limit_item',
        'expired_date',
        'diff_expired_days',
        'diff_item'];
}
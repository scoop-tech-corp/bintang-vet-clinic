<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListofItemsPetShop extends Model
{
    protected $table = "list_of_item_pet_shops";

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = ['item_name',
        'total_item',
        'branch_id',
        'user_id',
        'limit_item',
        'expired_date',
        'diff_expired_days',
        'diff_item'];
}

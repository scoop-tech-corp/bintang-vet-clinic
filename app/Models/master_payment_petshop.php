<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class master_payment_petshop extends Model
{
    protected $table = "master_payment_petshops";

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = ['payment_number', 'user_id','branch_id', 'user_update_id'];
}

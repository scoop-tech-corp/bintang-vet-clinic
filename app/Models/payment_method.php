<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class payment_method extends Model
{
    protected $table = "payment_methods";

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = ['payment_name', 'user_id'];
}

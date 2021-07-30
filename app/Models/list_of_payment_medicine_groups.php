<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class list_of_payment_medicine_groups extends Model
{
    protected $table = "list_of_payment_medicine_groups";

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = ['detail_medicine_group_check_up_result_id','list_of_payment_id', 'medicine_group_id', 'user_id'];
}

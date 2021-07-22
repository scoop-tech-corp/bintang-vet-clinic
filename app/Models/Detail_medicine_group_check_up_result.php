<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail_medicine_group_check_up_result extends Model
{
    protected $table = "detail_medicine_group_check_up_results";

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = ['check_up_result_id', 'medicine_group_id', 'status_paid_off', 'user_id'];
}

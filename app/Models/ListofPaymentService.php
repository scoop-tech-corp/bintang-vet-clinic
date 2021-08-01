<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListofPaymentService extends Model
{
    protected $table = "list_of_payment_services";

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = ['detail_service_patient_id', 'check_up_result_id','list_of_payment_id',
        'user_id'];
}

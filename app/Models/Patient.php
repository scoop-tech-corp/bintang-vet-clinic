<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{

    use SoftDeletes;

    protected $table = "patients";

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = ['branch_id',
        'id_member',
        'pet_category',
        'pet_name',
        'pet_gender',
        'pet_year_age',
        'pet_month_age',
        'owner_name',
        'owner_address',
        'owner_phone_number',
        'owner_id',
        'user_id',
        'update_by',
        'deleted_by',
        'deleted_at'];
}

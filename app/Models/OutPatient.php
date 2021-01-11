<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutPatient extends Model
{
    protected $table = "out_patients";

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = ['id_register','patient_id','complaint',
    'registrant','user_id'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
  protected $table = "registrations";

  protected $dates = ['deleted_at'];

  protected $guarded = ['id'];

  protected $fillable = [
    'id_number', 'patient_id', 'complaint',
    'registrant', 'user_id', 'doctor_user_id', 'acceptance_status', 'is_hide_from_drop_down'
  ];

  // public function check_up_results()
  // {
  //     return $this->hasMany('App\Models\CheckUpResult','patient_registration_id');
  // }
}

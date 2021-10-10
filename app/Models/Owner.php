<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
  protected $table = "owners";

  protected $dates = ['deleted_at'];

  protected $guarded = ['id'];

  protected $fillable = ['branch_id','owner_name','owner_address','owner_phone_number',
      'user_id'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
  protected $fillable = ['hour', 'minute', 'time', 'social_account_id'];

  public function social_account() {
       return $this->belongsTo('App\Models\SocialAccount', 'social_account_id');
   }

}

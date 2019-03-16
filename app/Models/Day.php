<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
  protected $fillable = ['name', 'active', 'social_account_id' ];

  public function social_account() {
    return $this->belongsTo('App\Models\SocialAccount', 'social_account_id');
  }

}

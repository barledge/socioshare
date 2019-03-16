<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
  protected $fillable = ['url', 'position', 'is_valid', 'twitter', 'facebook', 'instagram', ];

}

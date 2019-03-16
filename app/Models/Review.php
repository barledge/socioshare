<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
  use SoftDeletes;

  protected $fillable = ['user_id', 'post_id', 'message', 'status'];

  protected $appends = ['created_at_human'];



  public function user() {
      return $this->belongsTo('App\Models\User');
  }

  public function getCreatedAtHumanAttribute() {
        if($this->created_at)
          return $this->created_at->diffForHumans();
        return "-";
    }

}

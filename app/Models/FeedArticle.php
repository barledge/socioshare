<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedArticle extends Model
{
  protected $fillable = ['link'];

  protected $dates = [
        'published_at',
    ];


}

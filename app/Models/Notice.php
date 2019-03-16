<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{

  protected $fillable = ['type', 'platform', 'type_id', 'social_account_id', 'error', 'message', 'social_account_label'];
  protected $appends = array('human_time', 'human_day', 'url');

  public function getHumanTimeAttribute() {
        return $this->created_at->format('g:i A');
  }

  public function getHumanDayAttribute() {
      return $this->created_at->format('jS M');
  }

  public function getUrlAttribute() {
      if($this->type == 'post')
        return "/content/".$this->type_id."/edit";
      if($this->type == 'feed')
        return "/feeds/";
  }

}

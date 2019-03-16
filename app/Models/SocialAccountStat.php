<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccountStat extends Model
{
  protected $fillable = ['platform', 'username', 'access_token', 'platform_id'];

  protected $casts = [
      'access_token' => 'array',
      'auth_data' => 'array',
  ];

  protected $appends = array('name');

  public function getNameAttribute() {
        if($this->platform == 'facebook') {

          if($this->auth_data['type'] == 'page')
            return $this->label . " (page)";

          if($this->auth_data['type'] == 'profile')
            return $this->username . " (profile)";

          if($this->auth_data['type'] == 'group')
            return $this->label . " (group)";

        }

        return $this->label;
    }

    public function social_account() {
         return $this->hasMany('App\Models\ScheduledPost');
     }


}

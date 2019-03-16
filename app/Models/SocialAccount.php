<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
  protected $fillable = ['platform', 'username', 'access_token', 'platform_id'];

  protected $casts = [
      'needs_reauth' => 'boolean',
      'access_token' => 'array',
      'auth_data' => 'array',
      'social_account_list' => 'array',
  ];

  protected $appends = array('name', 'platform_type');

  public function getPlatformTypeAttribute() {
        if($this->platform == 'facebook') {

          if($this->auth_data['type'] == 'page')
            return $this->platform . " (page)";

          if($this->auth_data['type'] == 'profile')
            return $this->platform . " (profile)";

          if($this->auth_data['type'] == 'group')
            return $this->platform . " (group)";

        }

        return $this->platform;
    }

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

    public function days() {
         return $this->hasMany('App\Models\Day');
     }

    public function times() {
         return $this->hasMany('App\Models\Time');
     }



}

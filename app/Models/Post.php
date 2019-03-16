<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
  use SoftDeletes;
  use \Rutorika\Sortable\SortableTrait;

  protected $fillable = ['url', 'position', 'is_valid'];
  protected $casts = [
    'files' => 'array',
    'social_account_list' => 'array',

    'is_valid' => 'boolean',
    'fine_tune' => 'boolean',
    'requires_approval' => 'boolean',
  ];
  protected $dates = [
   'created_at',
   'updated_at',
   'deleted_at',
   'scheduled_at'
];
  protected $appends = array('human_time', 'human_day', 'scheduled_at_formatted', 'type', 'approval_status_text');

  public function getTypeAttribute() {
        if($this->is_draft)
          return 'draft';
        return '';
  }

  public function getScheduledAtFormattedAttribute() {
        if($this->scheduled_at)
          return $this->scheduled_at->format('Y-m-d H:i');
        return $this->scheduled_at;
  }

  public function getGroupsAttribute($value) {
        if(strlen($value) > 0) {
          return collect(explode(',', $value))->map(function ($name) {
              return (int) $name;
          });
        }
        return [];
  }

  public function getApprovalStatusTextAttribute() {
        if($this->approval_status == 'PENDING_APPROVAL')
          return 'Pending approval';
        if($this->approval_status == 'APPROVED')
          return 'Approved';
        if($this->approval_status == 'CHANGES_REQUESTED')
          return 'Changes requested';
        if($this->approval_status == 'REVIEW_CHANGES')
          return 'Re-review required';
        return "-";
  }

  public function getHumanTimeAttribute() {
        if($this->scheduled_at)
          return $this->scheduled_at->format('g:i A');
        return "-";
  }

  public function getHumanDayAttribute() {
        if($this->scheduled_at) {
          if($this->scheduled_at->isToday()) {
              return "Today";
          }
          if($this->scheduled_at->isTomorrow()) {
              return "Tomorrow";
          }
          return $this->scheduled_at->format('jS M');
        }
        return "-";
    }

  public function scheduled_posts() {
       return $this->hasMany('App\Models\ScheduledPost');
   }

   // this is a recommended way to declare event handlers
   protected static function boot() {
       parent::boot();

       static::deleting(function($post) { // before delete() method call this
            $post->scheduled_posts()->delete();
       });

   }
}

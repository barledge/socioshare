<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model {
  protected $fillable = ['name', 'user_id'];

  protected $casts = [
    'selection' => 'array',
  ];
  protected $appends = array('selection_list');

  public function getSelectionListAttribute() {
      if(!$this->selection)
        return [];
      $selection = [];
      foreach($this->selection as $row => $value) {
        if($value) {
          $selection[] = $row;
        }
      }
      return $selection;
  }

  public function user() {
      return $this->belongsTo('App\Models\User');
  }

}

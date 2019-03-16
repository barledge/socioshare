<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduledPost extends Model
{

    use SoftDeletes;
    use \Rutorika\Sortable\SortableTrait;

    protected $fillable = ['url', 'position', 'is_valid', 'post_id', 'social_account_id'];
    protected $casts = [
        'files' => 'array',
        'active' => 'boolean',
    ];
    protected $dates = [
        'queued_for',
        'created_at',
        'updated_at',
        'deleted_at',
        'scheduled_at'
    ];
    protected $appends = array('thumb', 'snippet', 'human_time', 'human_day', 'scheduled_at_human', 'scheduled_at_formatted', 'queued_for_human', 'can_post', 'status_text', 'post_date');

    public function getThumbAttribute()
    {

        $thumb = false;
        if ($this->files && count($this->files) > 0) {
            if ($this->files[0]->response->media_type == 'video') {
                $thumb = '/uploads/' . $this->files[0]->response->thumb;
            } else {
                $thumb = '/uploads/' . $this->files[0]->response->path;
            }
        }

        return $thumb;
    }

    public function setMetaAttribute($value)
    {
        if ($value)
            $this->attributes['meta'] = json_encode($value, JSON_FORCE_OBJECT);
    }

    public function getMetaAttribute($value)
    {
        if ($value) {
            return json_decode($value);
        }

        return (object)[];
    }

    public function getSnippetAttribute()
    {

        $snippet = $this->message;
        preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $this->message, $match);
        $urls = $match[0];
        foreach ($urls as $k => $v) {
            $snippet = str_replace($v, url_shorten($v), $snippet);
        }

        $brevity = new \Kylewm\Brevity\Brevity();
        $snippet = $brevity->shorten($snippet);

        return $snippet;
    }

    function get_excerpt($content, $length = 40, $more = '...')
    {
        $excerpt = strip_tags(trim($content));
        $words = str_word_count($excerpt, 2);
        if (count($words) > $length) {
            $words = array_slice($words, 0, $length, true);
            end($words);
            $position = key($words) + strlen(current($words));
            $excerpt = substr($excerpt, 0, $position) . $more;
        }
        return $excerpt;
    }

    public function getFilesAttribute($value)
    {
        if (!$value)
            return [];
        return json_decode($value);
    }

    public function getScheduledAtFormattedAttribute()
    {
        if ($this->scheduled_at)
            return $this->scheduled_at->format('Y-m-d H:i');
        return $this->scheduled_at;
    }

    public function getPostDateAttribute()
    {
        if ($this->scheduled_at)
            return $this->scheduled_at;
        if ($this->queued_for)
            return $this->queued_for;
        return $this->scheduled_at;
    }

    public function getStatusTextAttribute()
    {

        if (!$this->active) {
            return "Draft";
        }
        if ($this->status == 'UNSENT') {
            if ($this->scheduled_at)
                return "Scheduled";
            if ($this->queued_for)
                return "Queued";
        }
        if ($this->status == 'SENT')
            return "Sent";
        if ($this->status == 'CANCELLED')
            return "Cancelled";
        if ($this->status == 'FAILED')
            return "Failed";
        if ($this->status == 'SENDING')
            return "Sending";

        if ($this->status == 'SENDING' && $this->updated_at->diffInMinutes(\Carbon\Carbon::now()) > 1)
            return "Stuck";
        return "-";
    }

    public function getHumanTimeAttribute()
    {
        if ($this->scheduled_at)
            return $this->scheduled_at->format('g:i A');
        return "-";
    }

    public function getQueuedForHumanAttribute()
    {
        if ($this->queued_for) {
            if ($this->queued_for->isToday()) {
                return 'Today ' . $this->queued_for->format('H:i') . '';
            }
            return $this->queued_for->format('jS M H:i');
        }
        return "-";
    }

    public function getScheduledAtHumanAttribute()
    {
        if ($this->scheduled_at) {
            if ($this->scheduled_at->isToday()) {
                return 'Today ' . $this->scheduled_at->format('H:i') . '';
            }
            return $this->scheduled_at->format('jS M H:i');
        }
        return "-";
    }

    public function social_account()
    {
        return $this->belongsTo('App\Models\SocialAccount', 'social_account_id');
    }

    public function getCanPostAttribute()
    {

        if ($this->scheduled_at) {
            if ($this->scheduled_at->isPast())
                return true;
        }

        if ($this->queued_for) {
            if ($this->queued_for->isPast())
                return true;
        }

        return false;

    }

    public function getHumanDayAttribute()
    {
        if ($this->scheduled_at) {
            if ($this->scheduled_at->isToday()) {
                return "Today";
            }
            if ($this->scheduled_at->isTomorrow()) {
                return "Tomorrow";
            }
            return $this->scheduled_at->format('jS M');
        }
        return "-";
    }

    public function post()
    {
        return $this->belongsTo('App\Models\Post');
    }

}

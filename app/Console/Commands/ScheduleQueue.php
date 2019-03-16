<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduledPost;
use App\Models\Post;
use App\Models\Setting;
use App\Helper;
use \Carbon\Carbon;

use App\Models\SocialAccount;
use App\Models\Day;
use App\Models\Time;

class ScheduleQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warbler:schedule_queue {social_account_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To set scheduled dates';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * This runs when a new post has been added, updated
     *
     * @return mixed
     */
    public function handle()
    {
        //go through each social network,
        //  make sure default dates are set
        //  set the scheduled_at date

        //set default dates
        $this->setDefaultDates();

        //set the scheduled_at date
        $this->setSchedules();

    }

    private function setSchedules() {

      if($this->argument('social_account_id')) {
        $social_accounts = SocialAccount::where('id', $this->argument('social_account_id'))->get();
      } else {
        $social_accounts = SocialAccount::get();
      }
      foreach($social_accounts as $social_account) {
        $this->setSchedule($social_account);
      }

    }

    private function setSchedule($social_account) {

        $schedule = \App\Helper::getPostingTimes($social_account->id);
        $total_times = count($schedule);

        $next = $schedule->reject(function ($value, $key) {
          return $value->lt(Carbon::now());
        })->first();

        //get all queued posts
        $scheduled_posts = ScheduledPost::where('social_account_id', $social_account->id)
                              ->where('status', 'UNSENT')
                              ->where('active', true)
                              ->whereNull('scheduled_at')
                              ->orderBy('position', 'ASC')
                              ->get();
        $i = 0;
        foreach($scheduled_posts as $scheduled_post) {
            $this->line($scheduled_post->id . ' "' . $scheduled_post->message . '" ' . $scheduled_post->position);

            //no we don't have any times set, we just ignore (maybe user doesn't want it to be queued?)
            if($total_times == 0)
              continue;

            //check if post is draft
            if($scheduled_post->post->is_draft)
              continue;

            //now figure out queue
            $current = $i%$total_times;
            $start_index = $schedule->search($next);

            $scheduled_post->queued_for = $schedule[$start_index + $current];
            #$scheduled_post->queued_for = $schedule[$start_index + $current]->format('jS M H:i');
            $scheduled_post->save();

            $i++;
        }

    }

    private function setDefaultDates() {

      if($this->argument('social_account_id')) {
        $social_accounts = SocialAccount::where('id', $this->argument('social_account_id'))->get();
      } else {
        $social_accounts = SocialAccount::get();
      }

      foreach($social_accounts as $social_account) {
        $this->setDefaultDate($social_account);
      }

    }

    private function setDefaultDate($social_account) {

      if(count($social_account->days) < 7) {

        foreach(config('warbler.posting_days') as $posting_day) {
            Day::firstOrCreate(['social_account_id' => $social_account->id, 'name' => $posting_day, 'active' => true]);
        }

        foreach(config('warbler.posting_times') as $posting_time) {
            Time::firstOrCreate(['social_account_id' => $social_account->id, 'hour' => $posting_time['hour'], 'minute' => $posting_time['minute']]);
        }

      }

    }


}

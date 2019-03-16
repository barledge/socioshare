<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\ScheduledPost;
use App\Models\SocialAccount;
use App\Models\Notice;
use Carbon\Carbon;
use Log;
use Artisan;

class ProcessPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warbler:process_posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post scheduled items';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$this->line(__LINE__);

      if(env('DEMO')) {
		  $this->line(__LINE__);
        return false;
      }

      $this->line(__LINE__);
	    Log::info('ProcessPosts running');

        //go through each scheduled post
        $social_accounts = SocialAccount::get();
        foreach($social_accounts as $social_account) {
          $this->processPosts($social_account);
        }

        //post to social media


    }

    private function processPosts($social_account) {
		/*
			SELECT * FROM `scheduled_posts` WHERE 1=1
			AND (queued_for IS NOT NULL AND queued_for <= NOW()
			OR scheduled_at IS NOT NULL AND scheduled_at <= NOW())
			AND active = 1
			AND STATUS = 'UNSENT'
			ORDER BY `position`
		*/
		$scheduled_posts = ScheduledPost::where('social_account_id', $social_account->id)
          	  					->where(function ($q) {
          								$q->where(function ($query) {
          									$query->where('scheduled_at', '<=', Carbon::now())->whereNotNull('scheduled_at');
          								})
          								->orWhere(function ($query) {
          									$query->orWhere('queued_for', '<=', Carbon::now())->whereNotNull('queued_for');
          								});
          							})
                        ->where('active', true)
                        ->where('status', 'UNSENT')
                        ->orderBy('position', 'ASC')
          							->get();

		    $this->line("Posts to process: " . count($scheduled_posts));
        if(count($scheduled_posts) == 0) {
			       Log::info("$social_account->label: Nothing to process.");
        }

        foreach($scheduled_posts as $scheduled_post) {

          if($scheduled_post->can_post) {

              if($scheduled_post->social_account->needs_reauth) {
                  continue;
              }

              if($scheduled_post->post->requires_approval && $scheduled_post->post->approval_status != 'APPROVED') {
                  continue;
              }

			  $this->line("warbler:process_scheduled_post " . $scheduled_post->id);

              //post to social media (Queue)
              Artisan::queue('warbler:process_scheduled_post', [
                'id' => $scheduled_post->id
              ]);
          }

      }

    }

}

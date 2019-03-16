<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\ScheduledPost;
use App\Models\SocialAccount;
use App\Models\Notice;
use Carbon\Carbon;
use Log;

class ProcessScheduledPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warbler:process_scheduled_post {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled post to social media';

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

      if(env('DEMO')) {
        return false;
      }
	    Log::info('warbler:process_scheduled_post ' . $this->argument('id'));

        //go through each scheduled post
        $scheduled_post = ScheduledPost::find($this->argument('id'));
        if($scheduled_post && $scheduled_post->can_post) {
            if(!$scheduled_post->social_account->needs_reauth && $scheduled_post->active && $scheduled_post->status == 'UNSENT') {
              //great we can post
              $this->line("Processing " . $scheduled_post->id);
              $result = $this->processPost($scheduled_post);
            }
        } else {
          $this->line("Unable to process " . $scheduled_post->id);
        }

    }

    private function processPost($scheduled_post) {
      $result = null;

      //post to social media
      if($scheduled_post->social_account->platform == 'instagram') {
        $result = $this->postToInstagram($scheduled_post);
      }

      if($scheduled_post->social_account->platform == 'twitter') {
        $result = $this->postToTwitter($scheduled_post);
      }

      if($scheduled_post->social_account->platform == 'facebook') {
        $result = $this->postToFacebook($scheduled_post);
      }

      if($scheduled_post->social_account->platform == 'linkedin') {
        $result = $this->postToLinkedin($scheduled_post);
      }

      if($scheduled_post->social_account->platform == 'pinterest') {
        $result = $this->postToPinterest($scheduled_post);
      }

      //add to history
      Notice::create([
        'platform' => $scheduled_post->platform,
        'type' => 'post',
        'type_id' => $scheduled_post->id,
        'social_account_id' => $scheduled_post->social_account_id,
        'social_account_label' => $scheduled_post->social_account->label,
        'message' => $scheduled_post->message,
        'error' => @$result['error']
      ]);

      return $result;
    }

	private function postToTwitter($scheduled_post) {
      #dd($scheduled_post);
      #$scheduled_post->status = 'SENDING';
      #$scheduled_post->save();

      //post to twitter
      $helper = new \App\Helper();
      $result = $helper->postToTwitter($scheduled_post);
      if($result['status']) {
          $scheduled_post->status = 'SENT'; //if successfull save
		  try {
			  $result['response'] = json_decode($result['response'], true);
			  $scheduled_post->external_id = $result['response']['id_str'];
		  } catch(\Exception $e) {

		  }
      } else {
          $scheduled_post->error_log = $result['error'];
          $scheduled_post->status = 'FAILED';
      }

      $scheduled_post->save();
      return $scheduled_post;
    }


    private function postToFacebook($scheduled_post) {
      $scheduled_post->status = 'SENDING';
      $scheduled_post->save();

      //post to twitter
      $helper = new \App\Helper();
      $result = $helper->postToFacebook($scheduled_post);

      if($result['status']) {
        $scheduled_post->status = 'SENT'; //if successfull save
			try {
			  $scheduled_post->external_id = $result['response']->getDecodedBody()['id'];
		  } catch(\Exception $e) {

		  }
      } else {
        $scheduled_post->error_log = $result['error'];
        $scheduled_post->status = 'FAILED';
      }
      $scheduled_post->save();
      return $scheduled_post;

    }


    private function postToInstagram($scheduled_post) {
      $scheduled_post->status = 'SENDING';
      $scheduled_post->save();

      //post to twitter
      $helper = new \App\Helper();
      $result = $helper->postToInstagram($scheduled_post);
      if($result['status']) {
        //if successfull save
        $scheduled_post->status = 'SENT';

    		try {
    			$scheduled_post->external_id = $result['response']->getMedia()->getId();
    		} catch(\Exception $e) {

    		}
      } else {
        $scheduled_post->error_log = $result['error'];
        $scheduled_post->status = 'FAILED';
      }
      $scheduled_post->save();
      return $scheduled_post;
    }

    private function postToLinkedin($scheduled_post) {
      $scheduled_post->status = 'SENDING';
      $scheduled_post->save();

      //post to twitter
      $helper = new \App\Helper();
      $result = $helper->postToLinkedin($scheduled_post);
      if($result['status']) {
        //if successfull save
        $scheduled_post->status = 'SENT';
            try {
                $scheduled_post->external_id = $result['response']['updateKey'];
            } catch(\Exception $e) {

            }
      } else {
        $scheduled_post->error_log = $result['error'];
        $scheduled_post->status = 'FAILED';
      }
      $scheduled_post->save();
      return $scheduled_post;
    }

    private function postToPinterest($scheduled_post) {
      $scheduled_post->status = 'SENDING';
      $scheduled_post->save();

      //post to twitter
      $helper = new \App\Helper();
      $result = $helper->postToPinterest($scheduled_post);
      if($result['status']) {
        //if successfull save
        $scheduled_post->status = 'SENT';
        try {
            $scheduled_post->external_id = $result['response']->id;
        } catch(\Exception $e) {

        }
      } else {
        $scheduled_post->error_log = $result['error'];
        $scheduled_post->status = 'FAILED';
      }
      $scheduled_post->save();
      return $scheduled_post;
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SocialAccount;
use App\Models\SocialAccountStat;
use App\Models\ScheduledPost;
use App\Models\Group;
use App\Models\Post;
use App\Models\Setting;
use Carbon\Carbon;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        #dd((file_upload_max_size()));

        if(Auth::user()->hasrole('client')) {
          return redirect('/client');
        }

        $data = [];
        $data['days'] = ['Sunday',  'Monday',  'Tuesday', 'Wednesday', 'Thursday','Friday', 'Saturday'];
        $platforms = [
          'facebook' => ['accounts' => 0, 'failed' => '-', 'posted' => 0],
          'twitter' => ['accounts' => 0, 'failed' => '-', 'posted' => 0],
          'instagram' => ['accounts' => 0, 'failed' => '-', 'posted' => 0],
        ];

        $posts = ScheduledPost::orderByRaw('GREATEST(
          COALESCE(scheduled_at, 0),
          COALESCE(queued_for, 0)
        ) DESC')->paginate(10);


        $data['platforms'] = $platforms;
        $data['posts'] = $posts;

        $data['social_accounts'] = SocialAccount::orderBy('created_at', 'DESC')->get();
        $data['group'] = Group::count();

        $data['posts_count'] = Post::count();
        $data['schedule_setup'] = Setting::where('key', 'schedule_setup')->count();
        $cron_last_run = Setting::where('key', 'cron_last_run')->first();
        $data['cron_running'] = false;
        if($cron_last_run) {
          $last_run = new Carbon(  ($cron_last_run->value) );
          $data['cron_running'] = ($last_run->gt(Carbon::now()->subMinutes(5)));
        }

        $jumbo = '';
        if(count($data['social_accounts']) == 0) {
            $jumbo = 'connect';
		        return redirect('social-accounts');
        }
        if(count($data['schedule_setup']) == 0) {
          $jumbo = 'schedule';
        }
        if(count($data['posts_count']) == 0) {
          $jumbo = 'add_post';
        }
        $data['jumbo'] = $jumbo;
        #dd($data);
        return view('home', $data);
    }



}

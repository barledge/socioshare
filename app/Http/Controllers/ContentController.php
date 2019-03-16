<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\ScheduledPost;
use App\Models\SocialAccount;
use App\Models\Group;
use Artisan;
use Carbon\Carbon;
use Dusterio\LinkPreview\Client;
use Log;
use InstagramScraper\Model\Media;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $data = [];
        /*$posts = ScheduledPost::orderByRaw('GREATEST(
          COALESCE(scheduled_at, 0),
          COALESCE(queued_for, 0)
        ) DESC');*/
        $posts = ScheduledPost::orderBy('created_at', 'DESC');
        if($request->get('social_account_id'))
            $posts = $posts->where('social_account_id', $request->get('social_account_id'));
        $posts = $posts->where('active', 1);
        $posts = $posts->paginate(50);

        $data['posts'] = $posts;
        $groups = Group::pluck('name', 'id')->prepend('Choose something…', '');
        $data['group'] = $groups;
        $data['social_accounts'] = SocialAccount::pluck('label', 'id')->prepend('-- SELECT --', '');


        return view('content.index', $data);
    }

    public function postLinkPreview(Request $request)
    {
      $url = $request->get('url');
      return ['status' => true, 'url' => $url, 'preview' => $this->_getLinkPreview($request->get('url'))];
    }

    private function _getLinkPreview($url) {
      $preview = [];
      try {
          $previewClient = new Client($url);
          $preview = $previewClient->getPreview('general');
          $url = $previewClient->getUrl();
          $preview = $preview->toArray();
          $preview['url'] = $url->getHost();
          $brevity = new \Kylewm\Brevity\Brevity();
          $preview['description'] = $brevity->shorten($preview['description']);
      } catch(\Exception $e) {
        Log::info($e->getMessage());
      }
      return $preview;
    }

    public function getDrafts()
    {
        //
        $posts = Post::where('is_draft', true)->orderBy('created_at', 'DESC')->paginate(15);
        return view('content.drafts', ['posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $social_accounts = SocialAccount::orderBy('created_at', 'DESC')->get();
        $groups = Group::orderBy('created_at', 'DESC')->get();

        return view('content.create', ['social_accounts' => $social_accounts, 'groups' => $groups]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if($request->get('scheduled_at') && $request->get('scheduled_at') != 'NOW') {
          $now = Carbon::now();
          $now = $now->addMinute();
          if($now->gt(Carbon::createFromTimestamp(strtotime($request->get('scheduled_at'))))) {
            return response()->json(['error' => "Error: The date/time is in the past"], 500);
          }
        }
        $social_accounts = $request->get('social_accounts');
        $files = collect($request->get('files'));
        $files->transform(function ($item, $key) {
            $row = $item;
            $row['id'] = $item['id'];
            $row['name'] = $item['name'];
            $row['size'] = $item['size'];
            $row['progress'] = 100;
            $row['success'] = true;
            $row['type'] = $item['type'];
            $row['path'] = $item['response']['path'];
            $row['thumb'] = $item['response']['thumb'];
            $row['media_type'] = $item['response']['media_type'];
            return $row;
        });

        $post = new Post();
        $post->message = $request->get('message');
        if($request->get('scheduled_at')) {
          $post->scheduled_at = strtotime($request->get('scheduled_at'));
        }

        $post->files = $files;
        $post->groups = implode(",", $request->get('groups'));
        $post->fine_tune = $request->get('fine_tune');
        $post->is_draft = $request->get('is_draft');
        $post->requires_approval = $request->get('requires_approval');
        $post->social_account_list = collect($request->get('social_accounts'))->where('active', true)->pluck('id');
        $post->save();

        if(!$post->scheduled_at && !$post->is_draft) {
          $post->position = $post->id;
          $post->save();
        }

        foreach($social_accounts as $social_account) {
          if(!isset($social_account['active']) || !$social_account['active'])
            continue;
          $scheduled_post = new ScheduledPost();
          $scheduled_post->post_id = $post->id;
          $scheduled_post->message = $request->get('message');
          $scheduled_post->social_account_id = $social_account['id'];
          $scheduled_post->files = $post->files;
          $scheduled_post->scheduled_at = $post->scheduled_at;
          $scheduled_post->active = true;

          #$this->_getLinkPreview($request->get('url'));

          $scheduled_post->save();
        }

        Artisan::call('warbler:schedule_queue');

        return ['status' => true, 'post' => $post];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        //
        $post = Post::with(['scheduled_posts', 'scheduled_posts.social_account'])->find($id);
        #dd($post->scheduled_posts->toArray());
        $social_accounts = SocialAccount::orderBy('created_at', 'DESC')->get();
        $groups = Group::orderBy('created_at', 'DESC')->get();

        return view('content.create', ['post' => $post, 'social_accounts' => $social_accounts, 'groups' => $groups]);

    }

    public function add(Request $request)
    {
        $social_account_id = $request->get('social_account_id');
        $post_id = $request->get('post_id');

        $post = Post::with('scheduled_posts')->find($post_id);
        $scheduled_post = ScheduledPost::firstOrCreate(['post_id' => $post->id, 'social_account_id' => $social_account_id]);
        if($scheduled_post->wasRecentlyCreated) {
          $scheduled_post->active = true;
          $scheduled_post->message = $post->message;
          $scheduled_post->files = $post->files;
          $scheduled_post->scheduled_at = $post->scheduled_at;
          $scheduled_post->save();

          $post->social_account_list = array_unique(array_merge($post->social_account_list, [$social_account_id]));
          $post->save();
        }

        $post = Post::with(['scheduled_posts', 'scheduled_posts.social_account'])->find($post_id);
        return ['status' => true, 'post' => $post];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

      //dd(collect($request->get('social_accounts'))->where('active', true)->pluck('id'));
        //
        /*if !fine_tune
          copy same content to each scheduled_post
        if fine_tune
          unique content
        if draft
          save is_draft = 1, scheduled_at = null
        if add to queue
          save draft = 0*/
        $files = collect($request->get('files'));
        $files = $files->transform(function ($item, $key) {
            $row = $item;
            $row['id'] = $item['id'];
            $row['name'] = $item['name'];
            $row['type'] = $item['type'];
            $row['size'] = $item['size'];
            $row['progress'] = 100;
            $row['success'] = true;
            $row['path'] = $item['response']['path'];
            $row['thumb'] = $item['response']['thumb'];
            $row['media_type'] = $item['response']['media_type'];
            return $row;
        });
        #dd($files);

        $post = Post::with('scheduled_posts')->find($id);
        $post->fine_tune = $request->get('fine_tune');
        $post->is_draft = $request->get('is_draft');
        $post->requires_approval = $request->get('requires_approval');
        $post->message = $request->get('message');
        $post->scheduled_at = (strtotime($request->get('scheduled_at')))?strtotime($request->get('scheduled_at')):NULL;
        $post->files = $files;
        $post->groups = ($request->get('groups'))?implode(",", $request->get('groups')):"";
        $post->social_account_list = collect($request->get('social_accounts'))->where('active', true)->pluck('id');
        $post->save();

        $social_accounts = $request->get('social_accounts');
        $scheduled_posts = collect($request->get('scheduled_posts'));
        foreach($social_accounts as $social_account) {

            $scheduled_post = ScheduledPost::firstOrCreate(['post_id' => $post->id, 'social_account_id' => $social_account['id']]);
            $scheduled_post->active = (bool) $social_account['active'];

            if(!$request->get('fine_tune')) {
                $scheduled_post->message = $request->get('message');
                $scheduled_post->files = $post->files;
                $scheduled_post->scheduled_at = $post->scheduled_at;
            } else {
              $new_post = $scheduled_posts->where('social_account_id', $social_account['id'])->first();
              #print_r($new_post);die();
              if($new_post['files']) {
                $new_post['files'] = collect($new_post['files'])->transform(function ($item, $key) {
                    $row = $item;
                    $row['id'] = $item['id'];
                    $row['name'] = $item['name'];
                    $row['type'] = $item['type'];
                    $row['size'] = $item['size'];
                    $row['progress'] = 100;
                    $row['success'] = true;
                    $row['path'] = $item['response']['path'];
                    $row['thumb'] = $item['response']['thumb'];
                    $row['media_type'] = $item['response']['media_type'];
                    return $row;
                });
              }
              $scheduled_post->active = $new_post['active'];
              $scheduled_post->message = $new_post['message'];
              $scheduled_post->files = $new_post['files'];
              $scheduled_post->meta = $new_post['meta'];
              if($new_post['scheduled_at_formatted'])
                $scheduled_post->scheduled_at = strtotime($new_post['scheduled_at_formatted']);
              elseif($new_post['scheduled_at'])
                $scheduled_post->scheduled_at = strtotime($new_post['scheduled_at']);
              else
                $scheduled_post->scheduled_at = null;
            }

            $scheduled_post->save();
        }

        /*
        if(!$request->get('fine_tune')) {

          foreach($platforms as $platform) {
            if(!$platform['active'])
              continue;
            $scheduled_post = new ScheduledPost();
            $scheduled_post->post_id = $post->id;
            $scheduled_post->message = $request->get('message');
            $scheduled_post->social_account_id = $platform['id'];
            $scheduled_post->files = $post->files;
            $scheduled_post->scheduled_at = $post->scheduled_at;
            $scheduled_post->active = true;
            $scheduled_post->save();
          }

          foreach($post->scheduled_posts as $scheduled_post) {
            $scheduled_post->message = $request->get('message');
            $scheduled_at = (strtotime($request->get('scheduled_at')))?strtotime($request->get('scheduled_at')):NULL;
            $scheduled_post->scheduled_at = $scheduled_at;
            $scheduled_post->files = $request->get('files');
            $scheduled_post->active = $request->get($scheduled_post->platform);
            $scheduled_post->save();
          }
        }*/
        /*
        if($request->get('fine_tune')) {
          foreach($request->get('scheduled_posts') as $new_scheduled_post) {
            $scheduled_post = ScheduledPost::where('post_id', $id)->where('platform', $new_scheduled_post['platform'])->first();
            $scheduled_post->message = $new_scheduled_post['message'];
            $scheduled_at = (strtotime($new_scheduled_post['scheduled_at']))?strtotime($new_scheduled_post['scheduled_at']):NULL;
            $scheduled_post->scheduled_at = $scheduled_at;
            $scheduled_post->active = $new_scheduled_post['active'];
            $scheduled_post->files = $new_scheduled_post['files'];
            $scheduled_post->save();

            //remove scheduled_at if not scheduled
            if(!$scheduled_post->scheduled_at && !$post->is_draft) { //not a draft and not scheduled at specific time
              $scheduled_post->scheduled_at = NULL;
              $scheduled_post->save();
            }
          }
        }
        */



        $post = Post::with('scheduled_posts')->find($id);
        if($post->fine_tune) {

          $sorted = $post->scheduled_posts->sortBy('scheduled_at');
          $sorted = $sorted->reject(function ($value, $key) {
              return is_null($value->scheduled_at);
          });

          //do we have an empty date?
          $has_empty_date = !$post->scheduled_posts->every(function ($value, $key) {
              if($value->active && is_null($value->scheduled_at))
                return false;
              return true;
          });

          //if has an empty date, make posts scheduled_at null (so queue takes over)
          if($has_empty_date) {
            $post->scheduled_at = NULL;
          } else {
            $post->scheduled_at = $sorted->first()->scheduled_at;
          }

          $post->save();
        }

        Artisan::call('warbler:schedule_queue');

        $post = Post::with(['scheduled_posts', 'scheduled_posts.social_account'])->find($id);
        return ['status' => true, 'post' => $post];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $post = Post::find($id);
        $post->delete();

        return ['status' => 'true'];

    }

	public function redirect($id)
    {
        //
        $post = ScheduledPost::find($id);
		$url = "/";
        if($post->social_account->platform == 'instagram') {
            $url = $this->instagram_id_to_url($post->external_id);
        }

		if($post->social_account->platform == 'facebook') {
            $url = "https://www.facebook.com/".$post->external_id;
        }

		if($post->social_account->platform == 'twitter') {
            $url = "https://twitter.com/statuses/".$post->external_id;
        }

        if($post->social_account->platform == 'linkedin') {
            $url = "https://www.linkedin.com/feed/update/urn:li:activity:".@explode('-', $post->external_id)[2];
        }

        if($post->social_account->platform == 'pinterest') {
            $url = "https://www.pinterest.com/pin/".$post->external_id.'/';
        }

		#dd($url);
        return redirect($url);

    }

    function instagram_id_to_url($instagram_id){

    	$url_prefix = "https://www.instagram.com/p/";

        if(!empty(strpos($instagram_id, '_'))){
            $parts = explode('_', $instagram_id);
            $instagram_id = $parts[0];
            $userid = $parts[1];
        }

        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';
		$url_suffix = '';
        while($instagram_id > 0){

			if(function_exists('bcmod')) {
				$remainder = bcmod($instagram_id, 64);
				$instagram_id = bcdiv(bcsub($instagram_id, $remainder), 64);
			} else {
				$remainder = $instagram_id % 64;
				$instagram_id = ($instagram_id-$remainder) / 64;
			}

			$url_suffix = $alphabet{$remainder} . $url_suffix;

        };

        return $url_prefix.$url_suffix;

    }
}

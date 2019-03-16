<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use Storage;
use Image;
use Response;
use Redirect;
use Twitter;
use Session;
use Mail;
use Artisan;
use Carbon\Carbon;
use App\Models\Review;
use App\Models\Post;
use App\Models\Group;
use App\Models\SocialAccount;
use App\Http\Requests\StoreInstagram;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class ReviewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        ##$this->middleware('auth');
    }

    public function index(Request $request) {
      $group = Group::where('user_id', auth()->user()->id)->first();

      $data = [];
      $posts = Post::whereRaw('FIND_IN_SET(?,groups)', [$group->id])
                    ->whereNull('completed_at')
                    ->orderBy('created_at', 'DESC')
                    ->with(['scheduled_posts', 'scheduled_posts.social_account'])
                    ->get();
      $data['posts'] = $posts;

      return view('review.index', $data);
    }

    public function show($id)
    {
        //check if this guy can really read this post
        $group = Group::where('user_id', auth()->user()->id)->first();
        $post = Post::whereRaw('FIND_IN_SET(?,groups)', [$group->id])
                      ->where('id', $id)
                      ->first();

        $reviews = [];
        if( $post ) {
            $reviews = Review::where('post_id', $id)->with('user')->orderBy('created_at', 'DESC')->limit(10)->get();
        }

        return ['status' => 'true', 'reviews' => $reviews];
    }

    public function update($id, Request $request)
    {
        //

        $group = Group::where('user_id', auth()->user()->id)->first();
        $post = Post::whereRaw('FIND_IN_SET(?,groups)', [$group->id])
                      ->where('id', $id)
                      ->first();

        if( $post ) {
            $review = Review::create([
                'user_id' => auth()->user()->id,
                'post_id' => $id,
                'message' => $request->get('message'),
                'status' => $request->get('selection'),
            ]);

            $post->approval_status_changed_at = Carbon::now();
            if($request->get('selection') == 'approve') {
                //if approve then set the post to approved
                $post->approval_status = 'APPROVED';
                Artisan::call('warbler:schedule_queue');
            }

            if($request->get('selection') == 'request_changes') {
                //if approve then set the post to approved
                $post->approval_status = 'CHANGES_REQUESTED';
            }
            $post->save();
        }

        $post = Post::whereRaw('FIND_IN_SET(?,groups)', [$group->id])
                      ->where('id', $id)
                      ->with(['scheduled_posts', 'scheduled_posts.social_account'])
                      ->first();

        return ['status' => 'true', 'post' => $post];
    }

}

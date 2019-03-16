<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use Image;
use Response;
use Redirect;
use Twitter;
use Session;
use Mail;
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
        $this->middleware('auth');
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
        $reviews = Review::where('post_id', $id)->with('user')->orderBy('created_at', 'DESC')->limit(10)->get();
        return ['status' => 'true', 'reviews' => $reviews];
    }

    public function update($id, Request $request)
    {
        //
        $post = Post::where('id', $id)
                      ->with(['scheduled_posts', 'scheduled_posts.social_account'])
                      ->first();

        $review = Review::create([
            'user_id' => auth()->user()->id,
            'post_id' => $id,
            'message' => $request->get('message'),
            'status' => ($request->get('selection'))?'review_changes':'comment',
        ]);
        
        if($request->get('selection')) {
            $post->approval_status = 'REVIEW_CHANGES';
        }
        $post->save();

        return ['status' => 'true', 'post' => $post];
    }

}

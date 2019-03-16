<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\ScheduledPost;
use App\Models\SocialAccount;
use App\Models\Group;
use App\Models\Day;
use App\Models\Time;
use Carbon\Carbon;
use Artisan;

class QueueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with('scheduled_posts')->where('is_draft', false)->orderBy('created_at', 'DESC')->get();
        $schedule = \App\Helper::getPostingTimes();

        $social_accounts = SocialAccount::orderBy('created_at', 'DESC')->get();
        $groups = Group::orderBy('created_at', 'DESC')->get();

        return ['posts' => $posts, 'schedule' => $schedule, 'groups' => $groups, 'social_accounts' => $social_accounts];
    }

    public function listings($type = 'queue', Request $request)
    {
        if ($type == 'queue') {
            $posts = ScheduledPost::with(['social_account', 'post'])
                ->whereNotNull('queued_for')
                ->where('queued_for', '>=', Carbon::now())
                ->where('status', 'UNSENT')
                ->where('active', true)
                ->orderBy('queued_for', 'ASC');
        } else {
            $dt = Carbon::now();
            $posts = ScheduledPost::with(['social_account', 'post'])
                ->whereNotNull('scheduled_at')
                ->where('scheduled_at', '>=', $dt->subDay())
                ->where('status', 'UNSENT')
                ->where('active', true)
                ->orderBy('scheduled_at', 'ASC');
        }

        if ((int)$request->get('group') > 0 && (int)$request->get('social_account_id') == 0) {
            $group = Group::find($request->get('group'));
            if ($group->selection && count($group->selection_list) > 0)
                $posts->whereIn('social_account_id', $group->selection_list);
        }
        if ((int)$request->get('social_account_id') > 0) {
            $posts->where('social_account_id', $request->get('social_account_id'));
        }
        $posts = $posts->get();

        return ['posts' => $posts];
    }

    public function move($id, Request $request)
    {
        $post = ScheduledPost::find($id);
        $post_after = $post->next()->whereNotNull('queued_for')->where('social_account_id', $post->social_account_id)->where('status', 'UNSENT')->first();
        $post_before = $post->previous()->whereNotNull('queued_for')->where('social_account_id', $post->social_account_id)->where('status', 'UNSENT')->first();

        if ($request->get('direction') == 'up' && $post_before) {
            $post->moveBefore($post_before);
        }

        if ($request->get('direction') == 'down' && $post_after) {
            $post->moveAfter($post_after);
        }

        Artisan::call('warbler:schedule_queue', ['social_account_id' => $post->social_account_id]);

        return ['success' => true];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $social_accounts = SocialAccount::orderBy('created_at', 'DESC')->get();
        $groups = Group::orderBy('created_at', 'DESC')->get();

        return view('queue.index', ['groups' => $groups, 'social_accounts' => $social_accounts, 'tab' => $request->get('tab', 'in_queue')]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $scheduled_post = ScheduledPost::find($id);
        $scheduled_post->active = false;
        $scheduled_post->save();
        return ['status' => 'true', 'scheduled_post' => $scheduled_post];
    }
}

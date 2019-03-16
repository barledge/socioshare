<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\ScheduledPost;
use App\Models\SocialAccount;
use App\Models\Group;
use App\Http\Requests\StoreGroup;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $platforms = SocialAccount::orderBy('created_at', 'DESC')->get();
        return view('groups.index', ['platforms' => $platforms]);
    }

    public function getList()
    {
        //
        $groups = Group::with('user')->orderBy('created_at', 'DESC')->get();
        return ['groups' => $groups];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGroup $request)
    {
        //
        $group = Group::create(['name' => $request->get('name')]);
        $groups = Group::with('user')->orderBy('created_at', 'DESC')->get();
        return ['status' => true, 'groups' => $groups, 'group' => $group];
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
        #dd($post->toArray());
        $platforms = SocialAccount::orderBy('created_at', 'DESC')->get();

        return view('content.create', ['post' => $post, 'platforms' => $platforms]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $group = Group::find($id);
        $group->selection = $request->get('checked');
        $group->save();
        return ['status' => true, 'group' => $group];
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


        $group = Group::find($id);
        if($group->user) {
          $group->user->delete();
        }
        $group->delete();

        $groups = Group::with('user')->orderBy('created_at', 'DESC')->get();
        return ['status' => true, 'groups' => $groups];

    }
}

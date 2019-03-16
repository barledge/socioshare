<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SocialAccount;
use App\Models\Group;

class SocialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        if(auth()->user()->hasrole('client')) {
          $group = Group::where('user_id', auth()->user()->id)->first();
          $data['social_accounts'] = SocialAccount::whereIn('id', $group->selection_list)->orderBy('created_at', 'DESC')->get();
        } else {
          $data['social_accounts'] = SocialAccount::orderBy('created_at', 'DESC')->get();
        }
        return view('social.index', $data);
    }

    public function getList()
    {
        //
        if(auth()->user()->hasrole('admin')) {
          $social_accounts = SocialAccount::orderBy('created_at', 'DESC')->get();
          $platform_types = $social_accounts->sortBy('platform_type')->groupBy('platform_type');
        } else {
          $group = Group::where('user_id', auth()->user()->id)->first();
          $social_accounts = SocialAccount::whereIn('id', $group->selection_list)->orderBy('created_at', 'DESC')->get();
          $platform_types = $social_accounts->sortBy('platform_type')->groupBy('platform_type');
        }
        return ['social_accounts' => $social_accounts, 'platform_types' => $platform_types];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
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
        $social = SocialAccount::find($id);
        if(auth()->user()->hasrole('client')) {
          $group = Group::where('user_id', auth()->user()->id)->first();
          if($group && in_array($id, $group->selection_list)) {
            $social->delete();
          }
        }

        if(auth()->user()->hasrole('admin')) {
          $social->delete();
        }

        return ['status' => 'true'];
    }
}

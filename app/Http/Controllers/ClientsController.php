<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\ScheduledPost;
use App\Models\SocialAccount;
use App\Models\Group;
use App\Models\User;
use App\Http\Requests\StoreGroup;
use Kris\LaravelFormBuilder\FormBuilder;
use Hash;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $clients = User::orderBy('created_at', 'DESC')->paginate(15);
        return view('clients.index', ['clients' => $clients]);
    }

    public function getList()
    {
        //
        $groups = Group::orderBy('created_at', 'DESC')->get();
        return ['groups' => $groups];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, FormBuilder $formBuilder)
    {
        //
        $form = $formBuilder->create(\App\Forms\ClientForm::class, [
           'method' => 'POST',
           'url' => route('clients.store'),

       ]);

       return view('clients.create', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FormBuilder $formBuilder, Request $request)
    {
        //

        $form = $formBuilder->create(\App\Forms\ClientForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        //test facebook token
        if($request->get('facebook_app_key')) {
          if(!$this->validFacebookToken( $request->get('facebook_app_key'), $request->get('facebook_app_secret') )) {
            $errors = [
              "facebook_app_key" => ["The Facebook app is INVALID"]
            ];
            return redirect()->back()->withErrors($errors)->withInput();
          }
        }

        //create user
        $user = User::create([
          'email' => $request->get('email'),
          'name' => $request->get('name'),
          'password' => Hash::make($request->get('password')),
        ]);
        $user->assignRole('client');

        $user->facebook_app_key = $request->get('facebook_app_key');
        $user->facebook_app_secret = $request->get('facebook_app_secret');
        $user->save();

        //create group
        $group = Group::firstOrCreate(['name' => $request->get('name')]);
        if(!$group->user_id) {
          $group->user_id = $user->id;
          $group->save();
        }
        $message = 'Successfully saved. A group was <strong>'.$request->get('name').'</strong> also created for the user.';
        if(!$group->wasRecentlyCreated) {
          $message = 'Successfully saved. User <strong>'.$request->get('name').'</strong> is using the group <strong>'.$request->get('name').'</strong>.';
        }
        return redirect('groups')->with('message', $message);
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
    public function edit($id, FormBuilder $formBuilder)
    {
        //
        $form = $formBuilder->create(\App\Forms\ClientFormEdit::class, [
           'method' => 'PUT',
           'url' => route('clients.update', $id),
           'model' => User::find($id)->toArray()
        ]);

        return view('clients.edit', compact('form'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, FormBuilder $formBuilder, Request $request)
    {


        $form = $formBuilder->create(\App\Forms\ClientFormEdit::class);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $user = User::find($id);

        //test facebook token
        if($request->get('facebook_app_key')) {
          if($this->validFacebookToken( $request->get('facebook_app_key'), $request->get('facebook_app_secret') )) {
            $user->facebook_app_key = $request->get('facebook_app_key');
            $user->facebook_app_secret = $request->get('facebook_app_secret');
            $user->save();
          } else {
            $errors = [
              "facebook_app_key" => ["The Facebook app is INVALID"]
            ];
            return redirect()->back()->withErrors($errors)->withInput();
          }
        }

        if($request->get('password')) {
            $user->password = Hash::make($request->get('password'));
            $user->save();
        }

        return redirect('groups')->with('message', 'Successfully saved!');
    }

    private function validFacebookToken($app_key, $app_secret) {
      return true;
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
        $user = User::find($id);
        $user->delete();
        return ['status' => true];
    }
}

<?php

namespace App\Http\Controllers\Social;

use Illuminate\Http\Request;
use Storage;
use Image;
use Response;
use Redirect;
use Twitter;
use Facebook;
use Session;
use App\Models\SocialAccount;
use App\Models\User;
use App\Http\Controllers\Controller;

class FacebookController extends Controller
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

    public function createAccount($username, $platform_id, $token)
    {
        $social = SocialAccount::firstOrCreate(
            ['platform' => 'facebook', 'username' => $username, 'platform_id' => $platform_id],
            ['access_token' => ['token' => (string)$token]]
        );
        return $social;
    }

    public function select_custom($type, \SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb, Request $request)
    {
        $access_token = Session::get('fb_user_access_token');

        if (Session::get('fb_local_user_id')) {
            $user = User::find(Session::get('fb_local_user_id'));
            $fb = $fb->newInstance([
                'app_id' => $user->facebook_app_key,
                'app_secret' => $user->facebook_app_secret,
            ]);
        }

        if ($type == 'pages') {
            $response = $fb->get($request->get('custom_id') . '/?fields=access_token,name,id', $access_token);
            $response = $response->getGraphNode();

            $fid = $response['id'];

            $social = $this->createAccount(Session::get('fb_username'), $fid, $access_token);
            $social->access_token = ['token' => (string)$response->getProperty('access_token')];
            $social->auth_data = ['type' => 'page', 'user_id' => Session::get('fb_local_user_id', 0)];
            $social->platform_id = $fid;
            $social->label = (string)$response->getProperty('name');
            $social->needs_reauth = false;
            $social->user_id = auth()->user()->id;
            $social->save();

            $helper = new \App\Helper();
            $helper->syncAccountToClientGroup($social->id, auth()->user()->id);

        }

        if ($type == 'groups') {

            $response = $fb->get($request->get('custom_id') . '/?fields=name,id', $access_token);
            $item = $response->getGraphNode();
            #dd($item);
            $fid = $item['id'];

            $social = $this->createAccount(Session::get('fb_username'), $fid, $access_token);
            $social->access_token = ['token' => $access_token];
            $social->auth_data = ['type' => 'group', 'user_id' => Session::get('fb_local_user_id', 0)];
            $social->label = (string)$item->getProperty('name');
            $social->platform_id = $fid;
            $social->needs_reauth = false;
            $social->user_id = auth()->user()->id;
            $social->save();

            $helper = new \App\Helper();
            $helper->syncAccountToClientGroup($social->id, auth()->user()->id);

        }


        return redirect('/social-accounts')->with('alert-success', 'Successfully added Facebook ' . $type);
    }

    public function select($type, $fid, \SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb, Request $request)
    {
        $access_token = Session::get('fb_user_access_token');

        if (Session::get('fb_local_user_id')) {
            $user = User::find(Session::get('fb_local_user_id'));
            $fb = $fb->newInstance([
                'app_id' => $user->facebook_app_key,
                'app_secret' => $user->facebook_app_secret,
            ]);
        }

        if ($type == 'pages') {
            $response = $fb->get('/me/accounts/', $access_token);
            $response = $response->getGraphEdge();
            foreach ($response as $item) {
                if ($item->getProperty('id') == $fid) {
                    #dd( $access_token,  $item->getProperty('access_token'));
                    #$social->access_token = ['token' => (string) $item->getProperty('access_token')];
                    $social = $this->createAccount(Session::get('fb_username'), $fid, (string)$item->getProperty('access_token'));
                    $social->access_token = ['token' => (string)$item->getProperty('access_token')];
                    $social->auth_data = ['type' => 'page', 'user_id' => Session::get('fb_local_user_id', 0)];
                    $social->platform_id = $fid;
                    $social->label = (string)$item->getProperty('name');
                    $social->needs_reauth = false;
                    $social->user_id = auth()->user()->id;
                    $social->save();

                    $helper = new \App\Helper();
                    $helper->syncAccountToClientGroup($social->id, auth()->user()->id);
                }
            }
        }


        if ($type == 'groups') {
            $response = $fb->get('/me/groups', $access_token);
            $response = $response->getGraphEdge();
            foreach ($response as $item) {
                if ($item->getProperty('id') == $fid) {
                    #$social->access_token = ['token' => (string) $item->getProperty('access_token')];
                    $social = $this->createAccount(Session::get('fb_username'), $fid, $access_token);
                    $social->access_token = ['token' => $access_token];
                    $social->auth_data = ['type' => 'group', 'user_id' => Session::get('fb_local_user_id', 0)];
                    $social->label = (string)$item->getProperty('name');
                    $social->platform_id = $fid;
                    $social->needs_reauth = false;
                    $social->user_id = auth()->user()->id;
                    $social->save();

                    $helper = new \App\Helper();
                    $helper->syncAccountToClientGroup($social->id, auth()->user()->id);
                }
            }
        }

        return redirect('/social-accounts')->with('alert-success', 'Successfully added Facebook ' . $type);
    }

    public function choice(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb, Request $request)
    {

        $type = Session::get('fb_last_method');
        $access_token = Session::get('fb_user_access_token');

        if (Session::get('fb_local_user_id')) {
            $user = User::find(Session::get('fb_local_user_id'));
            $fb = $fb->newInstance([
                'app_id' => $user->facebook_app_key,
                'app_secret' => $user->facebook_app_secret,
            ]);
        }

        if ($type == 'profile') {
            $social = $this->createAccount(Session::get('fb_username'), Session::get('fb_id'), $access_token);
            $social->auth_data = ['type' => 'profile', 'user_id' => Session::get('fb_local_user_id', 0)];
            $social->label = Session::get('fb_username');
            $social->needs_reauth = false;
            $social->user_id = auth()->user()->id;
            $social->save();

            $helper = new \App\Helper();
            $helper->syncAccountToClientGroup($social->id, auth()->user()->id);

            return redirect('/social-accounts')->with('alert-success', 'Successfully added Facebook profile');
        }
        if ($type == 'pages') {
            $response = $fb->get('/me/accounts', $access_token);
        }
        if ($type == 'groups') {
            $response = $fb->get('/me/groups', $access_token);
        }
        $options = $response->getGraphEdge();

        $platform_ids = SocialAccount::get()->pluck('platform_id')->all();

        #dd($options);
        return view('social.facebook', ['options' => $options, 'platform_ids' => $platform_ids, 'type' => $type]);
    }

    public function preSelect(Request $request)
    {

        if (env('DEMO')) {
            return view('errors.disabled');
        }

        if (!env('FACEBOOK_APP_ID') || !env('FACEBOOK_APP_SECRET')) {
            $request->session()->flash('alert', 'Please enter your facebook credentials before continuing.');
            return redirect('settings#api_keys');
        }

        $select = $request->get('select');
        Session::forget('fb_local_user_id');

        if (auth()->user()->hasrole('client')) {
            $user = User::find(auth()->user()->id);
            if ($user->facebook_app_key) {
                Session::put('fb_local_user_id', $user->id);
            }
            return redirect('/facebook/' . $select);
        }

        //get all the clients that have a facebook access token
        $users = User::whereNotNull('facebook_app_key')->orWhere('facebook_app_key', '!=', '')->get();
        $user_count = User::whereNotNull('facebook_app_key')->orWhere('facebook_app_key', '!=', '')->count();
        if ($user_count == 0) {
            return redirect('/facebook/' . $select);
        }
        return view('social.facebook_preselect', ['users' => $users, 'select' => $select]);
    }

    public function preSelectById($user_id, $select, Request $request)
    {

        //get all the clients that have a facebook access token
        $user = User::find($user_id);
        Session::put('fb_local_user_id', $user_id);

        //now redirect it to preselect

        return redirect('/facebook/' . $select);
        dd($user, $select);

        return view('social.facebook_preselect', ['users' => $users, 'select' => $select]);
    }

    public function profile(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
    {
        Session::put('fb_last_method', 'profile');
        if (Session::get('fb_local_user_id')) {
            $user = User::find(Session::get('fb_local_user_id'));
            $fb = $fb->newInstance([
                'app_id' => $user->facebook_app_key,
                'app_secret' => $user->facebook_app_secret,
            ]);
        }
        $login_url = $fb->getLoginUrl(['email', 'manage_pages', 'publish_actions', 'user_posts', 'user_photos']);
        return redirect($login_url);
    }

    public function pages(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
    {
        Session::put('fb_last_method', 'pages');
        if (Session::get('fb_local_user_id')) {
            $user = User::find(Session::get('fb_local_user_id'));
            $fb = $fb->newInstance([
                'app_id' => $user->facebook_app_key,
                'app_secret' => $user->facebook_app_secret,
            ]);
        }
        $login_url = $fb->getLoginUrl(['email', 'manage_pages', 'publish_pages']);
        return redirect($login_url);
    }

    public function groups(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
    {
        Session::put('fb_last_method', 'groups');
        if (Session::get('fb_local_user_id')) {
            $user = User::find(Session::get('fb_local_user_id'));
            $fb = $fb->newInstance([
                'app_id' => $user->facebook_app_key,
                'app_secret' => $user->facebook_app_secret,
            ]);
        }
        $login_url = $fb->getLoginUrl(['email', 'user_managed_groups', 'publish_actions']);
        return redirect($login_url);
    }

    public function callback(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb, Request $request)
    {
        // Obtain an access token.
        if (Session::get('fb_local_user_id')) {
            $user = User::find(Session::get('fb_local_user_id'));
            $fb = $fb->newInstance([
                'app_id' => $user->facebook_app_key,
                'app_secret' => $user->facebook_app_secret,
            ]);
        }
        try {
            $token = $fb->getAccessTokenFromRedirect();
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }

        // Access token will be null if the user denied the request
        // or if someone just hit this URL outside of the OAuth flow.
        if (!$token) {
            // Get the redirect helper
            $helper = $fb->getRedirectLoginHelper();

            if (!$helper->getError()) {
                abort(403, 'Unauthorized action.');
            }

            // User denied the request
            dd(
                $helper->getError(),
                $helper->getErrorCode(),
                $helper->getErrorReason(),
                $helper->getErrorDescription()
            );
        }

        if (!$token->isLongLived()) {
            // OAuth 2.0 client handler
            $oauth_client = $fb->getOAuth2Client();

            // Extend the access token.
            try {
                $token = $oauth_client->getLongLivedAccessToken($token);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                dd($e->getMessage());
            }
        }

        $fb->setDefaultAccessToken($token);

        // Save for later
        Session::put('fb_user_access_token', (string)$token);

        // Get basic info on the user from Facebook.
        try {
            $response = $fb->get('/me?fields=id,name,email');
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }

        // Convert the response to a `Facebook/GraphNodes/GraphUser` collection
        $facebook_user = $response->getGraphUser();
        Session::put('fb_id', $facebook_user->getId());
        Session::put('fb_username', $facebook_user->getName());

        // Create the user if it does not exist or update the existing entry.
        // This will only work if you've added the SyncableGraphNodeTrait to your User model.
        #$user = App\User::createOrUpdateGraphNode($facebook_user);

        // Log the user into Laravel
        #Auth::login($user);
        return redirect('/facebook/choice/?type=' . $request->get('type'))->with('alert-success', 'Successfully logged in with Facebook');
    }

}

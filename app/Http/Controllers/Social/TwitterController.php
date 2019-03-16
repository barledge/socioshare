<?php

namespace App\Http\Controllers\Social;

use Illuminate\Http\Request;
use Storage;
use Image;
use Response;
use Redirect;
use Twitter;
use Session;
use App\Models\SocialAccount;
use App\Models\User;
use App\Http\Controllers\Controller;

class TwitterController extends Controller
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

    public function login(Request $request) {
      	$sign_in_twitter = true;
      	$force_login = false;

        if(env('DEMO')) {
          return view('errors.disabled');
        }

        if(!env('TWITTER_CONSUMER_KEY') || !env('TWITTER_CONSUMER_SECRET') || !env('TWITTER_ACCESS_TOKEN') || !env('TWITTER_ACCESS_TOKEN_SECRET')) {
            $request->session()->flash('alert', 'Please enter your facebook credentials before continuing.');
            return redirect('settings#api_keys');
        }

      	// Make sure we make this request w/o tokens, overwrite the default values in case of login.
      	Twitter::reconfig(['token' => '', 'secret' => '']);
      	$token = Twitter::getRequestToken(route('twitter.callback'));
      	if (isset($token['oauth_token_secret']))
      	{
      		$url = Twitter::getAuthorizeURL($token, $sign_in_twitter, $force_login);

      		Session::put('oauth_state', 'start');
      		Session::put('oauth_request_token', $token['oauth_token']);
      		Session::put('oauth_request_token_secret', $token['oauth_token_secret']);

      		return Redirect::to($url);
      	}

      	return Redirect::route('twitter.error');

    }

    public function callback(Request $request) {
      // You should set this route on your Twitter Application settings as the callback
    	// https://apps.twitter.com/app/YOUR-APP-ID/settings
    	if (Session::has('oauth_request_token')) {
    		$request_token = [
    			'token'  => Session::get('oauth_request_token'),
    			'secret' => Session::get('oauth_request_token_secret'),
    		];

    		Twitter::reconfig($request_token);

    		$oauth_verifier = false;

    		if ($request->has('oauth_verifier')) {
    			$oauth_verifier = $request->get('oauth_verifier');
    			// getAccessToken() will reset the token for you
    			$token = Twitter::getAccessToken($oauth_verifier);
    		}

    		if (!isset($token['oauth_token_secret']))
    		{
          $request->session()->flash('alert-danger', 'Oops! We could not log you in on Twitter.');
          return redirect()->route("social-accounts.index");
    		}

    		$credentials = Twitter::getCredentials();

    		if (is_object($credentials) && !isset($credentials->error))
    		{
    			// $credentials contains the Twitter user object with all the info about the user.
    			// Add here your own user logic, store profiles, create new users on your tables...you name it!
    			// Typically you'll want to store at least, user id, name and access tokens
    			// if you want to be able to call the API on behalf of your users.

          $social = SocialAccount::firstOrCreate(
             ['platform' => 'twitter', 'username' => $credentials->screen_name],
             ['access_token' => $token]
          );
          $social->access_token = $token;
          $social->label = $credentials->screen_name;
          $social->needs_reauth = false;
          $social->user_id = auth()->user()->id;
          $social->save();

          $helper = new \App\Helper();
          $helper->syncAccountToClientGroup($social->id, auth()->user()->id);

          #dd($request_token);

    			// This is also the moment to log in your users if you're using Laravel's Auth class
    			// Auth::login($user) should do the trick.

    			Session::put('access_token', $token);
          $request->session()->flash('alert-success', 'Congrats! Your Twitter account was added!');

    			return redirect()->route("social-accounts.index");
    		}

        $request->session()->flash('alert-danger', 'Oops! Something went wrong while adding your twitter account!');
        return redirect()->route("social-accounts.index");
    	}
    }

    public function error(Request $request) {
      dd("Twitter Error");
    }

    public function logout(Request $request) {
      dd("Twitter logout");
    }

}

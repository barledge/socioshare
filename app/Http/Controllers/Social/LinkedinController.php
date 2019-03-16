<?php

namespace App\Http\Controllers\Social;

use Illuminate\Http\Request;
use Storage;
use Image;
use Response;
use Redirect;
use LinkedIn;
use Session;
use URL;
use App\Models\ScheduledPost;
use App\Models\SocialAccount;
use App\Models\User;
use App\Http\Controllers\Controller;

class LinkedInController extends Controller
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

    public function accept(Request $request) {

		if (LinkedIn::isAuthenticated()) {
			//we know that the user is authenticated now. Start query the API
			$user = LinkedIn::get('v1/people/~');
			$username = $user['firstName'] . ' ' . $user['lastName'];
			$access_token = LinkedIn::getAccessToken();

			if(Session::get('linkedin_type') == 'page') {
				Session::put('linkedin_access_token', $access_token);
				return Redirect::to('linkedin/choice');
			}

			$social = SocialAccount::firstOrCreate(
				['platform' => 'linkedin', 'username' => $username, 'platform_id' => $user['id']]
			);
			$social->label = $username;
			$social->needs_reauth = false;
			$social->user_id = auth()->user()->id;
			$social->access_token = ['token' => $access_token->__toString()];
			$social->auth_data = ['type' => 'profile', 'user_id' => auth()->user()->id];
			$social->save();

		} elseif (LinkedIn::hasError()) {
			$request->session()->flash('alert-danger', 'Oops! We could not log you in on Linkedin.');
			return Redirect::to('social-accounts');
		}

		$request->session()->flash('alert-success', 'Congrats! Your Linkedin account was added!');
		return Redirect::to('social-accounts');
	}

    public function cancel(Request $request) {
		return Redirect::to('social-accounts');
	}

    public function select($company_id, Request $request) {
		$access_token = Session::get('linkedin_access_token');
		LinkedIn::setAccessToken(Session::get('linkedin_access_token'));
		$company = LinkedIn::get('v1/companies/'.$company_id.'?format=json');

		$username = $company['name'];
		$social = SocialAccount::firstOrCreate(
			['platform' => 'linkedin', 'username' => $username, 'platform_id' => $company['id']]
		);
		$social->label = $username;
		$social->needs_reauth = false;
		$social->user_id = auth()->user()->id;
		$social->access_token = ['token' => $access_token->__toString()];
		$social->auth_data = ['type' => 'company', 'user_id' => auth()->user()->id, 'company_name' => $company['name'], 'company_id' => $company['id']];
		$social->save();

		$request->session()->flash('alert-success', 'Congrats! Your Linkedin account was added!');
		return Redirect::to('social-accounts');
	}

    public function choice(Request $request) {
		LinkedIn::setAccessToken(Session::get('linkedin_access_token'));
		$companies = [];
		if (LinkedIn::isAuthenticated()) {
			$companies = LinkedIn::get('v1/companies?format=json&is-company-admin=true');
			if($companies) {
				$companies = $companies['values'];
			}
		}
        
		return view('social.linkedin_choice', ['companies' => $companies]);
    }

	public function pages(Request $request) {
        if(env('DEMO')) {
          return view('errors.disabled');
        }
        if(!env('LINKEDIN_KEY') || !env('LINKEDIN_SECRET')) {
            $request->session()->flash('alert', 'Please enter your LinkedIn credentials before continuing.');
            return redirect('settings#api_keys');
        }
		Session::put('linkedin_type', 'page');
		$url = LinkedIn::getLoginUrl(array('scope'=>'r_basicprofile,w_share,rw_company_admin', 'redirect_uri'=> URL::to('/linkedin/callback')));
		return Redirect::to($url);
    }

    public function profile(Request $request) {
        if(env('DEMO')) {
          return view('errors.disabled');
        }
        if(!env('LINKEDIN_KEY') || !env('LINKEDIN_SECRET')) {
            $request->session()->flash('alert', 'Please enter your LinkedIn credentials before continuing.');
            return redirect('settings#api_keys');
        }
		Session::put('linkedin_type', 'profile');
		$url = LinkedIn::getLoginUrl(array('scope'=>'r_basicprofile,w_share,rw_company_admin', 'redirect_uri'=> URL::to('/linkedin/callback')));
		return Redirect::to($url);
    }

	public function image($id, Request $request) {
		$post = ScheduledPost::find($id);

		$image_url = null;
		if($post->files) {
			foreach($post->files as $i => $file) {
				if($file->media_type == 'image') {
					$image_url = storage_path('app/'.$file->path);
				}
			}
		}

		$img = Image::make($image_url);
		$img->fit(698, 400, function ($constraint) {
			$constraint->upsize();
		});

		return $img->response('jpg', 75);
    }

}

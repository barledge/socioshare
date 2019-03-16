<?php

namespace App\Http\Controllers\Social;

use Illuminate\Http\Request;
use Storage;
use Image;
use Response;
use Redirect;
use Session;
use URL;
use App\Models\ScheduledPost;
use App\Models\SocialAccount;
use App\Models\User;
use App\Http\Controllers\Controller;
use DirkGroenen\Pinterest\Pinterest;

class PinterestController extends Controller
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

    public function callback(Request $request) {
		$pinterest = new Pinterest(config('pinterest.id'), config('pinterest.secret'));
		if(isset($_GET["code"])){
			try {
				$token = $pinterest->auth->getOAuthToken($_GET["code"]);
			} catch (\Exception $e) {
				$request->session()->flash('alert-danger', 'Oops! We could not log you in on Pinterest.');
				return Redirect::to('social-accounts');
			}

			Session::put('pinterest_token', $token);
			return Redirect::to('pinterest/boards');

		} else {
			$request->session()->flash('alert-danger', 'Oops! We could not log you in on Pinterest.');
		}

		return Redirect::to('social-accounts');
	}

    public function save(Request $request) {

		$pinterest = new Pinterest(config('pinterest.id'), config('pinterest.secret'));
		$pinterest->auth->setOAuthToken(Session::get('pinterest_token')->access_token);

		$me = $pinterest->users->me(['fields' => 'username,first_name,last_name']);

		if($me->username) {
			$username = $me->username;
		} else {
			$username = $me->first_name .' ' .$me->last_name;
		}

		foreach($request->get('boards') as $board_id => $board_name) {
			$board_id  = explode("|", $board_id);

			$social = SocialAccount::firstOrCreate(
				['platform' => 'pinterest', 'username' => $username, 'platform_id' => $me->id.'_'.$board_id[0]]
			);
			$social->label = $board_name;
			$social->needs_reauth = false;
			$social->user_id = auth()->user()->id;
			$social->access_token = ['token' => Session::get('pinterest_token')->access_token];
			$social->auth_data = ['type' => 'profile', 'user_id' => auth()->user()->id, 'board_id' => $board_id[0], 'board_path' => $board_id[1]];
			$social->save();
		}

		$request->session()->flash('alert-success', 'Congrats! Your Pinterest board/s was added!');
		return Redirect::to('social-accounts');
	}

    public function boards(Request $request) {

		$pinterest = new Pinterest(config('pinterest.id'), config('pinterest.secret'));
		$pinterest->auth->setOAuthToken(Session::get('pinterest_token')->access_token);

		//get list of boards
		$boards = $pinterest->users->getMeBoards();
		return view('social.pinterest_boards', ['boards' => $boards->all()]);
	}

    public function cancel(Request $request) {
		return Redirect::to('social-accounts');
	}

    public function profile(Request $request) {
        if(env('DEMO')) {
          return view('errors.disabled');
        }
        if(!env('PINTEREST_ID') || !env('PINTEREST_SECRET')) {
          //abort(400, 'Please add your pinterest credentials to the .env file.');
            $request->session()->flash('alert', 'Please enter your pinterest credentials before continuing.');
           return redirect('settings#api_keys');
        }

		$pinterest = new Pinterest(config('pinterest.id'), config('pinterest.secret'));
		$loginurl = $pinterest->auth->getLoginUrl(URL::to('/pinterest/callback'), ['read_public', 'write_public']);
		return Redirect::to($loginurl);
    }

}

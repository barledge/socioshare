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
use App\Http\Requests\StoreInstagram;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use \Curl\Curl;
use Goutte\Client;

class InstagramController extends Controller
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

		$data = [];
		$data['missing_library'] = false;

		//check if user has added the mgp25/instagram-php module
		$directory = base_path("vendor/mgp25/instagram-php");
		if(!file_exists($directory.'/README.md')) {
			$data['missing_library'] = true;
		}

		return view('social.instagram', $data);
    }

    public function postCheckpoint() {
        //validate the thing and login



    }

    public function getCheckpoint() {
        //show the form
        $insta_response = Session::get('insta_response');
        dd($insta_response->challengeType);

        if($insta_response->challengeType == "VerifyEmailCodeForm") {
            $text = "To protect your account, Instagram will send you a security code to verify your identity.";
        }

        return view('social.instagram_checkpoint');
    }

    public function store(StoreInstagram $request)
    {

        $insta_username = strtolower($request->get('instagram_username'));
        $insta_password = $request->get('instagram_password');

        if(!env('DEMO')) {

			\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
		     $ig = new \InstagramAPI\Instagram();

          if($request->get('instagram_proxy')) {
            $ig->setProxy($request->get('instagram_proxy'));
          }
          $result = false;
         /* try {
		    $ig->login($insta_username, $insta_password);
          } catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
              $request->session()->flash('alert-danger', '<strong>Verification required.</strong> Please login to instagram and validate access to this account.');
              return redirect()->route("instagram.index");
          } catch (\Exception $e) {
              $request->session()->flash('alert-danger', '<strong>Oh snap!</strong> Your Instagram login details are incorrect OR your proxy is failing.<br />'.$e->getMessage());
              return redirect()->route("instagram.index");
          }*/


            try {
               $result = $ig->login($insta_username, $insta_password);
            } catch (\InstagramAPI\Exception\IncorrectPasswordException $e) {
                $request->session()->flash('alert-danger', '<strong>Oh snap!</strong> Your Instagram login details are incorrect.');
                return redirect()->route("instagram.index");
            } catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
                $challenge_url = @$e->fullResponse->challenge->url;
                $request->session()->flash('alert-danger', '<strong>Verification required.</strong> Please login to instagram and confirm you recognize the login from our location to validate access to this account.<br />'.$challenge_url);
                return redirect()->route("instagram.index");
                #return redirect()->route("instagram.checkpoint");
            } catch (\InstagramAPI\Exception\ChallengeRequiredException  $e) {
                $challenge_url = @$e->fullResponse->challenge->url;
                $request->session()->flash('alert-danger', '<strong>Verification required.</strong> Please login to instagram and confirm you recognize the login from our location to validate access to this account.<br />'.$challenge_url);
                return redirect()->route("instagram.index");
            } catch (\InstagramAPI\Exception\LoginRequiredException   $e) {
                $request->session()->flash('alert-danger', "Error establishing connection with this account.");
                return redirect()->route("instagram.index");
            } catch (\InstagramAPI\Exception\EndpointException  $e) {
                $request->session()->flash('alert-danger', $endpoint_ex->getMessage());
                return redirect()->route("instagram.index");
            } catch (\Exception $e) {
                $request->session()->flash('alert-danger', '<strong>Oh snap!</strong> Your Instagram login details are incorrect.<br />'.$e->getMessage());
                return redirect()->route("instagram.index");
            }

        }

        $encrypted = Crypt::encryptString($insta_password);

        $social = SocialAccount::firstOrCreate(
           ['platform' => 'instagram', 'username' => $insta_username],
           ['access_token' => ['password' => $encrypted]]
        );
        if($request->get('instagram_proxy'))
          $social->proxy = $request->get('instagram_proxy');
        $social->access_token = ['password' => $encrypted];
        $social->label = $insta_username;
        $social->needs_reauth = false;
        $social->user_id = auth()->user()->id;
        $social->save();

        $helper = new \App\Helper();
        $helper->syncAccountToClientGroup($social->id, auth()->user()->id);

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(base_path("vendor/mgp25/instagram-php/sessions/" . strtolower($insta_username))));
        foreach($iterator as $item) {
          chmod($item, 0777);
        }

        $request->session()->flash('alert-success', 'Congrats! Your Instagram account was added!');
        return redirect()->route("social-accounts.index");
    }


}

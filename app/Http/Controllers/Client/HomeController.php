<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use Storage;
use Image;
use Response;
use Redirect;
use Twitter;
use Session;
use Mail;
use App\Models\Group;
use App\Models\SocialAccount;
use App\Http\Requests\StoreInstagram;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class HomeController extends Controller
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
      $group = Group::where('user_id', auth()->user()->id)->first();
      $data['social_accounts'] = SocialAccount::whereIn('id', $group->selection_list)->orderBy('created_at', 'DESC')->get();
      return view('social.index', $data);
    }

    public function destroy($id)
    {
        //
        $social = SocialAccount::find($id);
        $group = Group::where('user_id', auth()->user()->id)->first();
        if($group && in_array($id, $group->selection_list)) {
          $social->delete();
        }
        return ['status' => 'true'];
    }

}

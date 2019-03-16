<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\ScheduledPost;
use App\Models\SocialAccount;
use App\Models\Group;
use Artisan;
use Carbon\Carbon;
use Dusterio\LinkPreview\Client;
use Log;

class DebugController extends Controller
{

    public function phpinfo(Request $request)
    {
        phpinfo();
    }

    public function cron(Request $request)
    {
        Artisan::call('warbler:process_posts');
        return "OK";
    }


}

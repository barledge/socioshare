<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\ScheduledPost;
use App\Models\SocialAccount;
use App\Models\Group;
use App\Models\Day;
use App\Models\Time;
use Carbon\Carbon;
use Artisan;
use Twitter;

class AnalyticsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //
        $social_accounts = SocialAccount::orderBy('created_at', 'DESC')->get();
        $groups = Group::orderBy('created_at', 'DESC')->get();

        return view('analytics.index', ['groups' => $groups, 'social_accounts' => $social_accounts]);

    }

    public function getInstagramStats($platform, $request)
    {
        $instagram = new \InstagramScraper\Instagram();
        $medias = $instagram->getMedias($platform->username, 25);

        $data = [];
        foreach ($medias as $media) {
            // Let's look at $media

            $carbon = Carbon::createFromTimestamp($media->getCreatedTime());
            $row = [];
            $row['id'] = $media->getId();
            $row['created_date'] = $carbon->format('jS M');
            $row['created_time'] = $carbon->format('H:i');
            $row['text'] = str_limit($media->getCaption(), 140);
            $row['image'] = $media->getImageThumbnailUrl();
            $row['link'] = $media->getLink();
            $row['type'] = $media->getType();
            $row['platform'] = $platform->platform;
            $row['likes'] = $media->getLikesCount();
            $row['comments'] = $media->getCommentsCount();
            $data[] = $row;

        }
        $headers = ['type', 'likes', 'comments'];
        return [
            'status' => true,
            'headers' => $headers,
            'items' => $data,
            'request' => $request->all(),
        ];
    }

    public function getTwitterStats($platform, $request)
    {
        Twitter::reconfig(['token' => $platform->access_token['oauth_token'], 'secret' => $platform->access_token['oauth_token_secret']]);
        $result = Twitter::getUserTimeline(['exclude_replies' => false]);
        #print_r($result);die();
        $data = [];
        foreach ($result as $item) {
            $carbon = Carbon::createFromTimestamp(strtotime($item->created_at));
            $row = [];
            $row['id'] = $item->id;
            $row['created_date'] = $carbon->format('jS M');
            $row['created_time'] = $carbon->format('H:i');
            $row['text'] = str_limit($item->text, 140);
            $row['retweets'] = $item->retweet_count;
            $row['likes'] = $item->favorite_count;
            $row['link'] = 'https://twitter.com/mic/status/' . $item->id;
            $row['platform'] = $platform->platform;
            $data[] = $row;
        }

        $headers = ['retweets', 'likes'];
        return [
            'status' => true,
            'headers' => $headers,
            'items' => $data,
            'request' => $request->all(),
        ];
    }

    public function getFacebookStats($social_account, $request)
    {

        $data = [];
        $fb = \App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');
        if (isset($social_account->auth_data["user_id"]) && (int)$social_account->auth_data["user_id"]) {
            $user = User::find($social_account->auth_data["user_id"]);
            if ($user && $user->facebook_app_key && $user->facebook_app_secret) {
                $fb = $fb->newInstance([
                    'app_id' => $user->facebook_app_key,
                    'app_secret' => $user->facebook_app_secret,
                ]);
            }
        }

        $fb->setDefaultAccessToken($social_account->access_token['token']);
        if ($social_account->auth_data['type'] == 'page') {
            $response = $fb->get("/" . $social_account->platform_id . "/feed?fields=reactions.type(LIKE).limit(0).summary(1).as(like),reactions.type(LOVE).limit(0).summary(1).as(love),reactions.type(HAHA).limit(0).summary(1).as(haha),reactions.type(WOW).limit(0).summary(1).as(wow),reactions.type(SAD).limit(0).summary(1).as(sad),reactions.type(ANGRY).limit(0).summary(1).as(angry),message,created_time,shares,likes.summary(true),comments.summary(true)")->getGraphEdge();
        } else {
            $response = $fb->get("/" . $social_account->platform_id . "/feed?fields=message,created_time,shares,likes.summary(true),comments.summary(true)")->getGraphEdge();
        }

        $data = [];
        $reactions = ['NONE', 'LIKE', 'LOVE', 'WOW', 'HAHA', 'SAD', 'ANGRY', 'THANKFUL'];
        foreach ($response as $item) {
            $total_reactions = 0;
            foreach ($reactions as $reaction) {
                if ($item->getField(strtolower($reaction)))
                    $total_reactions += $item->getField(strtolower($reaction))->getTotalCount();
            }

            $carbon = Carbon::instance($item['created_time']);

            $row = [];
            $row['id'] = $item['id'];
            $row['created_date'] = $carbon->format('jS M');
            $row['created_time'] = $carbon->format('H:i');
            $row['text'] = str_limit($item->getField('message'), 140);
            #$row['retweets'] = $item->retweet_count;
            if ($social_account->auth_data['type'] == 'page') {
                $row['reactions'] = $total_reactions;
            }
            $row['platform'] = $social_account->platform;
            $row['link'] = 'https://facebook.com/' . $item['id'];
            $row['shares'] = 0;
            if ($item->getField('shares')) {
                $row['shares'] = $item->getField('shares')->getField('count');
            }
            $row['likes'] = $item['likes']->getTotalCount();
            $row['comments'] = $item['comments']->getTotalCount();
            $data[] = $row;
        }

        $headers = ['reactions', 'shares', 'likes', 'comments'];
        return [
            'status' => true,
            'headers' => $headers,
            'items' => $data,
            'request' => $request->all(),
        ];
    }

    public function store(Request $request)
    {

        $posts = ScheduledPost::orderBy('created_at', 'DESC')->get();
        $social_account = SocialAccount::find($request->get('social_account_id'));
        if ($social_account->platform == 'twitter') {
            $data = $this->getTwitterStats($social_account, $request);
        } elseif ($social_account->platform == 'facebook') {
            $data = $this->getFacebookStats($social_account, $request);
        } elseif ($social_account->platform == 'instagram') {
            $data = $this->getInstagramStats($social_account, $request);
        } else {
            return [
                'status' => true,
                'headers' => [],
                'items' => [],
                'request' => $request->all(),
            ];
        }
        return $data;
    }


}

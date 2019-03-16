<?php

namespace App;
use App\Models\Day;
use App\Models\Time;
use App\Models\SocialAccount;
use Carbon\Carbon;
use Twitter;
use Facebook;
use File;
use Log;
use Crypt;
use App\Models\User;
use App\Models\Group;
use LinkedIn;
use DirkGroenen\Pinterest\Pinterest;

class Helper {
    public static function getPostingTimes($id){

      $days = Day::where('social_account_id', $id)->where('active', true)->get();
      $times = Time::where('social_account_id', $id)->get();

      $schedule = collect([]);
      foreach($days as $day) {
        $date = date('Y-m-d', strtotime('this '.$day->name));
        foreach($times as $time) {
          $schedule->push(new Carbon($date.' '.$time->hour.':'.$time->minute.':00'));
        }
      }

      $schedule = $schedule->sortBy(function($date) {
        return $date->timestamp;
      });
      $schedule = collect($schedule->values()->all());
      return $schedule;
    }

    public static function syncAccountToClientGroup($social_account_id, $user_id) {

      $user = User::find($user_id);
      $social_account = SocialAccount::find($social_account_id);
      $group = Group::where('user_id', $user_id)->first();

      if(!$group && auth()->user()->hasrole('client')) {
        $group = Group::firstOrCreate(['name' => $user->name]);
        if(!$group->user_id) {
          $group->user_id = $user->id;
          $group->save();
          $group = Group::where('user_id', $user_id)->first();
        }
      }

      if($group) {
          //if there is a group we must add the social account into the group
          if(!in_array($user_id, $group->selection_list)) {
              $selection = $group->selection;
              $selection[$social_account_id] = true;
              $group->selection = $selection;
              $group->save();
          }
      }

    }

    public static function getFFConfig() {
      $ffmpeg = trim(shell_exec('which ffmpeg'));
      $ffprobe = trim(shell_exec('which ffprobe'));
      if (!empty($ffmpeg) && !empty($ffprobe)) {
		$config = [
			'ffmpeg.binaries'  => $ffmpeg,
			'ffprobe.binaries' => $ffprobe,
		];
        return $config;
      }
      if (stripos(PHP_OS, 'win') === 0) {
          // code for windows
          $config = [
            'ffmpeg.binaries'  => resource_path('ffmpeg/windows/ffmpeg.exe'),
            'ffprobe.binaries' => resource_path('ffmpeg/windows/ffprobe.exe'),
          ];
      } elseif (stripos(PHP_OS, 'darwin') === 0) {
          // code for OS X
          $config = [
            'ffmpeg.binaries'  => resource_path('ffmpeg/darwin/ffmpeg'),
            'ffprobe.binaries' => resource_path('ffmpeg/darwin/ffprobe'),
          ];
      } elseif (stripos(PHP_OS, 'linux') === 0) {
          // code for Linux
          $config = [
            'ffmpeg.binaries'  => resource_path('ffmpeg/linux/ffmpeg'),
            'ffprobe.binaries' => resource_path('ffmpeg/linux/ffprobe'),
          ];
      }
      return $config;
    }

    public function getPlatformStats($platform) {
      if($platform->platform == 'twitter') {
        Twitter::reconfig(['token' => $platform->access_token['oauth_token'], 'secret' => $platform->access_token['oauth_token_secret']]);
        $result = Twitter::getUsersLookup(['screen_name' => $platform->username]);
        $data = [];
        $data['followers'] = $result->followers_count;
        $data['following'] = $result->friends_count;
        $data['posts'] = $result->statuses_count;
        return $data;
      }

      if($platform->platform == 'facebook') {

        $fb = \App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');
        $fb->setDefaultAccessToken($platform->access_token['token']);
        try {
            $response = $fb->get('/me/feed');
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }


      $data = [
        'message' => 'My awesome photo upload example.',
        'source' =>$fb->fileToUpload(storage_path('app/test.png')),
        'published' => false,
      ];
      try {
        $response = $fb->post('/me/photos', $data);
      } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
      } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
      }
      $graphNode = $response->getGraphNode();

      echo 'Photo ID: ' . $graphNode['id'];

        $data = [
            "message" => "Wonderful message",
            'attached_media[0]' => '{"media_fbid":"'.$graphNode['id'].'"}',
        ];
        try {
          $fb->post("/me/feed", $data);
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }
        #$facebook_user = $response->getGraphUser();
        dd($response);

        Twitter::reconfig(['token' => $platform->access_token['oauth_token'], 'secret' => $platform->access_token['oauth_token_secret']]);
        $result = Twitter::getUsersLookup(['screen_name' => $platform->username]);
        $data = [];
        $data['posts'] = $result->statuses_count;
        return $data;
      }

      return null;
    }

    private function getVideoDuration($local_path) {
      $ffmpeg_config = self::getFFConfig();
      $ffprobe = \FFMpeg\FFProbe::create($ffmpeg_config);
      $duration = $ffprobe
          ->format($local_path) // extracts file informations
          ->get('duration');
      return $duration;
    }

    public function postToTwitter($post) {
      $result = ['status' => false];

      $platform = SocialAccount::find($post->social_account_id);
      $request_token = [
        'token'  => $platform->access_token['oauth_token'],
        'secret' => $platform->access_token['oauth_token_secret'],
      ];
      Twitter::reconfig($request_token);

      $media_ids = [];
      if($post->files) {
          foreach($post->files as $i => $file) {

              if($file->media_type == 'image') {
                  $uploaded_media = Twitter::uploadMedia(['media' => File::get(storage_path('app/'.$file->path), true)]);
              } else {
                  $duration = $this->getVideoDuration(storage_path('app/'.$file->path));
                  try {
                      $uploaded_media = Twitter::uploadMediaChunked([
                        'token'  => $platform->access_token['oauth_token'],
                        'secret' => $platform->access_token['oauth_token_secret'],
                        'media_file' => storage_path('app/'.$file->path), 'media_category' => 'tweet_video'
                      ]);
                  } catch (\Exception $e) {
                    if (strpos($result['error'], '[32]') !== false) {
                        $this->invalidateAaccount($post->social_account_id);
                    }
					throw $e;
                    Log::info($e->getMessage());
                    Log::info(Twitter::logs());
                  }
              }

              $media_ids[] = $uploaded_media->media_id_string;
          }
      }

      try {
        $twitter = Twitter::postTweet(['status' => $post->message, 'media_ids' => $media_ids, 'format' => 'json']);
        $result['status'] = true;
        $result['response'] = $twitter;
      } catch (\Exception $e) {
        $result['error'] = (string) $e->getMessage();
        if (strpos($result['error'], '[32]') !== false) {
            $this->invalidateAaccount($post->social_account_id);
        }
		throw $e;
        Log::info($e->getMessage());
        Log::info(Twitter::logs());
      }

      return $result;
    }


    public function invalidateAaccount($id) {
      $platform = SocialAccount::find($id);
      $platform->needs_reauth = true;
      $platform->save();
    }

    public function postToFacebookProfile($fb, $platform, $post) {
          $media = [];
          if($post->files) {
              foreach($post->files as $i => $file) {
                  $data = [
                    'message' => '',
                    'source' => $fb->fileToUpload(storage_path('app/'.$file->path)),
                    'published' => false,
                  ];
                  try {
                    $response = $fb->post('/me/photos', $data);
                  } catch(Facebook\Exceptions\FacebookResponseException $e) {
                    Log::info('Graph returned an error: ' . $e->getMessage());
                    return false;
                  } catch(Facebook\Exceptions\FacebookSDKException $e) {
                    Log::info('Facebook SDK returned an error: ' . $e->getMessage());
                    return false;
                  }

                  $graphNode = $response->getGraphNode();
                  $media['attached_media['.$i.']'] = '{"media_fbid":"'.$graphNode['id'].'"}';
              }
          }

          $data = $media;
          $data["message"] = $post->message;
          try {
              $result = $fb->post("/me/feed", $data);
              return $result;
          } catch (Facebook\Exceptions\FacebookSDKException $e) {
              Log::info($e->getMessage());
              return false;
          }

    }

    public function postToFacebookGeneric($fb, $platform, $post) {
      $result = ['status' => false];
      $media = [];

      if($post->files) {
          foreach($post->files as $i => $file) {
              $endpoint = 'photos';
              $data = [
                'message' => '',
                'source' => $fb->fileToUpload(storage_path('app/'.$file->path)),
                'published' => false,
              ];

              if($file->media_type == 'video') {
                $endpoint = 'videos';

                $data = [
                  'message' => $post->message,
                  'source' => $fb->fileToUpload(storage_path('app/'.$file->path)),
                  'published' => true,
                  'title'       => '',
                  'description' => $post->message,
                ];
              }

              try {
                $response = $fb->post('/'.$platform->platform_id.'/'.$endpoint, $data);
                if($endpoint == 'videos') {
                  $result = ['status' => true, 'response' => $response];
                  return $result;
                }
              } catch(Facebook\Exceptions\FacebookSDKException $e) {
                $result = [
                  'status' => false,
                  'error' => (string) $e->getMessage(),
                ];
                if (strpos($result['error'], 'Invalid OAuth access token') !== false) {
                    $this->invalidateAaccount($post->social_account_id);
                }
                Log::info('Facebook SDK returned an error: ' . $e->getMessage());
                return $result;
              }

              $graphNode = $response->getGraphNode();
              $media['attached_media['.$i.']'] = '{"media_fbid":"'.$graphNode['id'].'"}';
              if($endpoint == 'videos')
                continue;
          }
          //dd($post->files);
      }

      $data = $media;
      $data["message"] = $post->message;

      try {
            if($post->files && count($post->files) > 0) {
                //do nothing
            } else {
                preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $post->message, $match);
                $data["link"] = $match[0][0];
            }
	  } catch (\Exception $e) {
	  }

      try {
        $response = $fb->post("/".$platform->platform_id."/feed", $data);
        $result = ['status' => true, 'response' => $response];
      } catch (Facebook\Exceptions\FacebookSDKException $e) {
          $result = [
            'status' => false,
            'error' => (string) $e->getMessage(),
          ];
          if (strpos($result['error'], 'Invalid OAuth access token') !== false) {
              $this->invalidateAaccount($post->social_account_id);
          }
          Log::info($e->getMessage());
      }
      return $result;

    }

    public function postToFacebook($post) {
      $result = ['status' => false];

      $fb = \App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');
	  if(isset($post->social_account->auth_data["user_id"]) && (int) $post->social_account->auth_data["user_id"]) {
		  $user = User::find($post->social_account->auth_data["user_id"]);
		  if($user && $user->facebook_app_key && $user->facebook_app_secret) {
			$fb = $fb->newInstance([
				'app_id' => $user->facebook_app_key,
				'app_secret' => $user->facebook_app_secret,
			]);
		  }
	  }
      $fb->setDefaultAccessToken($post->social_account->access_token['token']);

      if($post->social_account->auth_data['type'] == 'profile')
        $result = $this->postToFacebookGeneric($fb, $post->social_account, $post);

      if($post->social_account->auth_data['type'] == 'page' || $post->social_account->auth_data['type'] == 'group') {
        $result = $this->postToFacebookGeneric($fb, $post->social_account, $post);
      }

      return $result;
    }


    private function resizeVideo($input) {

      $path_data = pathinfo($input);
      $output_dir = storage_path('encodes/' .pathinfo($path_data['dirname'], PATHINFO_BASENAME ) . DIRECTORY_SEPARATOR . $path_data['filename']);
      File::makeDirectory($output_dir, 0775, true, true);

	  $ffmpeg_config = self::getFFConfig();

      //cut the video
      $cut = $ffmpeg_config['ffmpeg.binaries']." -y -threads 2 -ss 00:00:00 -t 00:00:59 -i $input -vcodec copy -acodec copy $output_dir/cut.mp4";
      exec($cut);

      //crop
      $cut = $ffmpeg_config['ffmpeg.binaries'].' -y -i '.$output_dir.'/cut.mp4 -vf "scale=\'min(640,iw)\':min\'(640,ih)\':force_original_aspect_ratio=decrease,pad=640:640:(ow-iw)/2:(oh-ih)/2" -strict -2 '.$output_dir.'/cropped-square.mp4';
      exec($cut);

      //transcode
      $cut = $ffmpeg_config['ffmpeg.binaries']." -y -i $output_dir/cropped-square.mp4 -vcodec libx264 -r 30 -vb 1024k -minrate 1024k -maxrate 1024k -bufsize 1024k -acodec aac -b:a 128k -ar 44100 -strict experimental -qscale 0 $output_dir/output-mpeg4.mp4";
      exec($cut);

	  $ffmpeg_path = dirname($ffmpeg_config['ffmpeg.binaries']);
	  putenv('PATH=$PATH:'.$ffmpeg_path);

      return "$output_dir/output-mpeg4.mp4";

    }

    public function postToInstagram($post) {
      $result = ['status' => false];
      $insta_username = $post->social_account->username;
      $insta_password = Crypt::decryptString($post->social_account->access_token['password']);

	  \InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
      $ig = new \InstagramAPI\Instagram();

      try {
        if($post->social_account->proxy)
          $ig->setProxy($post->social_account->proxy);
        $ig->login($insta_username, $insta_password);
      } catch (\Exception $e) {
        $result = [
          'status' => false,
          'error' => "auth ".$e->getMessage()
        ];
        return $result;
      }

      $is_story = false;

       try {
          if($post->meta->instagram_story) {
               $is_story = true;
          }
      } catch (\Exception $e) {

      }

      //login and post
      if($post->files) {

          if(count($post->files) == 1) {

              $file = $post->files[0];

              if($file->media_type == 'image') {

                try {
                    if( $is_story) {
						$photo = new \InstagramAPI\Media\Photo\InstagramPhoto(storage_path('app/'.$file->path), ['targetFeed' => \InstagramAPI\Constants::FEED_STORY]);
						$response = $ig->story->uploadPhoto($photo->getFile(), ['caption' => $post->message]);
                    } else {
                        $photo = new \InstagramAPI\Media\Photo\InstagramPhoto( storage_path('app/'.$file->path) );
						$response = $ig->timeline->uploadPhoto($photo->getFile(), ['caption' => $post->message]);
                    }
					#dd($photo->getFile(), $response);
                  $result = ['status' => true, 'response' => $response];
                } catch (\Exception $e) {
				#dd($e->getMessage());
                  $result = ['status' => false, 'error' => $e->getMessage()];
                }
              }

              if($file->media_type == 'video') {
                  $video = storage_path('app/'.$file->path);
                  //$resized = $this->resizeVideo($video);

                  try {
                      if( $is_story) {
                        $video = new \InstagramAPI\Media\Video\InstagramVideo($video, ['targetFeed' => \InstagramAPI\Constants::FEED_STORY]);
						$response = $ig->story->uploadVideo($video->getFile(), ['caption' => $post->message]);
                      } else {
						$video = new \InstagramAPI\Media\Video\InstagramVideo($video);
						$response = $ig->timeline->uploadVideo($video->getFile(), ['caption' => $post->message]);
                      }
                      $result = ['status' => true, 'response' => $response];
                  } catch (\Exception $e) {
                      $result = ['status' => false, 'error' => $e->getMessage()];
                  }

              }

          } else {

            $media = [];
            foreach($post->files as $i => $file) {

              if($file->media_type == 'image') {
					$photo = new \InstagramAPI\Media\Photo\InstagramPhoto( storage_path('app/'.$file->path) );
					$media[] = [
						'type'     => 'photo',
						'file'     => $photo->getFile(),
					];
              }

              if($file->media_type == 'video') {
					$video = storage_path('app/'.$file->path);
					$resized = $this->resizeVideo($video);
					$media[] = [
						'type'     => 'video',
						'file'     => $resized,
					];
              }

              if(count($media) == 10)
                  break;

            }

            try {
				$response = $ig->timeline->uploadAlbum($media, ['caption' => $post->message]);
                //$response = $ig->uploadTimelineAlbum($media, ['caption' => $post->message]);
				$result = ['status' => true, 'response' => $response];
            } catch (\Exception $e) {
              $result = ['status' => false, 'error' => $e->getMessage()];
            }

          }
      } else {
        $result = ['status' => false, 'error' => 'No media to post'];
      }

      return $result;
    }

    public function getNumberOfPosts($platform) {
      $posts = 0;
      return $posts;
    }

    public function postToLinkedin($post) {
		$result = ['status' => false];

		$platform = SocialAccount::find($post->social_account_id);
		$access_token = $platform->access_token['token'];
		LinkedIn::setAccessToken($access_token);

		$image_url = null;
		if($post->files) {
			foreach($post->files as $i => $file) {
				if($file->media_type == 'image') {
					$image_url = \URL::to('/linkedin/'.$post->id.'.jpg');  //submitted-url
					break;
				}
			}
		}

		//the post
		$options = [];
		$options['json'] = ['comment' => $post->message, 'visibility' => ['code' => 'anyone']];
		if($image_url) {
			$options['json']['content'] = ['submitted-url' => $image_url];
		}

		if($platform->auth_data["type"] == 'profile') {
			$response = LinkedIn::post('v1/people/~/shares', $options);
		} else {
			$response = LinkedIn::post('v1/companies/'.$platform->auth_data["company_id"].'/shares', $options);
		}

		if(@$response["status"] == 400) {
			$result['error'] = $response["message"];
			Log::info($response["message"]);
		} else {
			$result['status'] = true;
			$result['response'] = $response;
		}

		return $result;
    }


	public function postToPinterest($post) {
		$result = ['status' => false];

		$platform = SocialAccount::find($post->social_account_id);
		$pinterest = new Pinterest(config('pinterest.id'), config('pinterest.secret'));
	  	$pinterest->auth->setOAuthToken($platform->access_token['token']);

		//get the first image
		$image = null;
		if($post->files) {
			foreach($post->files as $i => $file) {
				if($file->media_type == 'image') {
					$image = storage_path('app/'.$file->path);  //submitted-url
					break;
				}
			}
		}

		if($image) {
			try {
				$response = $pinterest->pins->create(array(
					"note"          => $post->message,
					"image"         => $image,
					"board"         => $platform->auth_data['board_path']
				));
				$result['status'] = true;
				$result['response'] = $response;
            } catch (\Exception $e) {
                $result = ['status' => false, 'error' => $e->getMessage()];
            }
		} else {
			$result['error'] = "No image selected";
		}

		return $result;
    }

}

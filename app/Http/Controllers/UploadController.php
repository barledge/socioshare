<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use Image;
use Response;
use App\Http\Requests\UploadRequest;

class UploadController extends Controller
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

    public function getUpload($thumb, Request $request) {

      $storage_path  = Storage::getDriver()->getAdapter()->getPathPrefix();
      $local_path = $storage_path . '/' . $thumb;
      
      if(@is_array(getimagesize($local_path))){
          $img = Image::make($local_path);

          // prevent possible upsizing
          $img->fit(320, 320, function ($constraint) {
              $constraint->aspectRatio();
              $constraint->upsize();
          });

          return $img->response();
        } else {
          return response()->file($local_path);
        }


    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function postUpload(UploadRequest $request) {

        $path = $request->file->store('images/'.date('Y-m-d'));

        $storage_path  = Storage::getDriver()->getAdapter()->getPathPrefix();
    		$local_path = $storage_path . '/' . $path;
        $mime = mime_content_type($local_path);
        $media_type = '';
        //sleep(5);
        if(strstr($mime, "video/")){

            $media_type = 'video';
            $ffmpeg_config = \App\Helper::getFFConfig();
            $ffprobe = \FFMpeg\FFProbe::create($ffmpeg_config);
            $duration = $ffprobe
                ->format($local_path) // extracts file informations
                ->get('duration');

            $image_path = preg_replace('/\\.[^.\\s]{3,4}$/', '', $path).'.jpg';
            $ffmpeg = \FFMpeg\FFMpeg::create($ffmpeg_config);
            $video = $ffmpeg->open($local_path);

            $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(ceil($duration/4)))->save($storage_path . '/' . $image_path);

            $img = Image::make($storage_path . '/' . $image_path);
            $img->fit(120, 120, function ($constraint) {
                $constraint->upsize();
            });
            $img->resizeCanvas(120, 120, 'center', false, '#000000');

            $img = (string) $img->encode('jpg', 90);
            $thumb = Storage::disk('public')->put($image_path, $img, 'public');
            $thumb_path = $image_path;

        } else if(strstr($mime, "image/")){

            $media_type = 'image';
            $img = Image::make($local_path);
            $img->fit(120, 120, function ($constraint) {
              $constraint->upsize();
            });
            $img->resizeCanvas(120, 120, 'center', false, '#000000');

            $img = (string) $img->encode('jpg', 90);
            $thumb = Storage::disk('public')->put($path, $img, 'public');
            $thumb_path = $path;

        } else {
            $returnData = ['status' => 'error', 'message' => 'Invalid filetype'];
            return Response::json($returnData, 500);
        }

        return ['status' => true, 'path' => $path, 'thumb' => $thumb_path, 'media_type' => $media_type];
    }

}

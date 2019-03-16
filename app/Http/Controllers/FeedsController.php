<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Feed;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class FeedsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('feeds.index', ['feeds' => Feed::get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        foreach($request->get('feeds') as $i => $feed) {

          $validator = Validator::make($feed, array(
              'url' => 'required|url',
          ));

          $feed['valid'] = false;
          if(!$validator->fails()) {
            try {
              $client = new Client();
              $result = $client->get($feed['url']);
              if($result->getStatusCode() == 200) {
                $feed['valid'] = true;
              }
            } catch(\Exception $e) {

            }
          }

            $feed_entry = Feed::updateOrCreate(
              ['position' => $i+1],
              ['is_valid' => $feed['valid'], 'url' => $feed['url']]
            );

        }

        return ['status' => true, 'feeds' => Feed::get()];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

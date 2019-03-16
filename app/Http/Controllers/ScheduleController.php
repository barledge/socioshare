<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Day;
use App\Models\Time;
use App\Models\Setting;
use App\Models\SocialAccount;
use Carbon\Carbon;
use Artisan;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return redirect('/social-accounts');
        return view('schedule.index', ['days' => Day::get(), 'times' => Time::get()]);
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
        foreach($request->get('days') as $i => $day) {
            Day::updateOrCreate(
              ['name' => $day['name']],
              ['active' => $day['active']]
            );
        }

        Time::truncate();
        foreach($request->get('times') as $i => $time) {
          Time::firstOrCreate(
            ['hour' => $time['hour'], 'minute' => $time['minute']]
          );
        }

        Setting::firstOrCreate(
          ['key' => 'schedule_setup', 'value' => Carbon::now()]
        );

        return ['status' => true, 'days' => Day::get(), 'times' => Time::get()];

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
        dd('show');
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
        //dd(config('warbler'));
        $data = [];
        $data['days'] = Day::where('social_account_id', $id)->get();
        $data['times'] = Time::where('social_account_id', $id)->get();
        $data['account'] = SocialAccount::find($id);

        if(count($data['days']) < 7) {
          foreach(config('warbler.posting_days') as $posting_day) {
              Day::firstOrCreate(['social_account_id' => $id, 'name' => $posting_day, 'active' => true]);
          }

          foreach(config('warbler.posting_times') as $posting_time) {
              Time::firstOrCreate(['social_account_id' => $id, 'hour' => $posting_time['hour'], 'minute' => $posting_time['minute']]);
          }
        }

        $data['days'] = Day::where('social_account_id', $id)->get();
        $data['times'] = Time::where('social_account_id', $id)->get();
        $data['account'] = SocialAccount::find($id);
        return view('schedule.edit', $data);
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

        foreach($request->get('days') as $i => $day) {
            Day::updateOrCreate(
              ['name' => $day['name'], 'social_account_id' => $id],
              ['active' => $day['active']]
            );
        }

        Time::where('social_account_id', $id)->delete();
        foreach($request->get('times') as $i => $time) {
          Time::firstOrCreate(
            ['hour' => $time['hour'], 'minute' => $time['minute'], 'social_account_id' => $id]
          );
        }

        $data = [];
        $data['days'] = Day::where('social_account_id', $id)->get();
        $data['times'] = Time::where('social_account_id', $id)->get();
        $data['account'] = SocialAccount::find($id);
        $data['status'] = true;

        Artisan::call('warbler:schedule_queue', ['social_account_id' => $id]);

        return $data;
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

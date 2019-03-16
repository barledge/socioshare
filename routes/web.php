<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('home');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => ['auth', 'role:admin']], function () {
    #dd(auth()->user());
    Route::resource('groups', 'GroupsController');
    Route::resource('content', 'ContentController');
    Route::post('content/add', 'ContentController@add');
    Route::get('content/redirect/{id}', 'ContentController@redirect');
    Route::resource('feeds', 'FeedsController');
    Route::resource('queue', 'QueueController');
    Route::any('queue/listings/{type}', 'QueueController@listings');
    Route::post('queue/move/{id}', 'QueueController@move');
    Route::resource('analytics', 'AnalyticsController');
    Route::resource('schedule', 'ScheduleController');
    Route::resource('history', 'HistoryController');
    Route::resource('clients', 'ClientsController');
    Route::get('/uploads/{thumb?}', 'UploadController@getUpload')->where('thumb', '.*');
    Route::post('/upload', 'UploadController@postUpload');
    Route::get('/groups-list', 'GroupsController@getList');
    Route::get('/social-accounts-list', 'SocialController@getList');
    Route::get('/drafts', 'ContentController@getDrafts');
    Route::post('/link-preview', 'ContentController@postLinkPreview');
    Route::resource('review', 'ReviewController');
});

Route::group(['middleware' => ['auth']], function () {
  Route::get('/uploads/{thumb?}', 'UploadController@getUpload')->where('thumb', '.*');
  Route::post('/upload', 'UploadController@postUpload');
});

Route::group(['middleware' => ['auth', 'role:client']], function () {
  Route::get('/client', function () {
      return redirect('social-accounts');
  });
});

Route::group(['prefix' => 'client', 'middleware' => ['auth', 'role:client']], function()
{
    Route::resource('review', 'Client\ReviewController');
});

Route::group(['middleware' => ['auth']], function () {
  Route::resource('settings', 'SettingsController');
  Route::post('/api/upload', 'UploadController@postUpload');
  Route::resource('social-accounts', 'SocialController');
});

Route::get('/twitter/login', 'Social\TwitterController@login')->name('twitter.login');
Route::get('/twitter/callback', 'Social\TwitterController@callback')->name('twitter.callback');
Route::get('/twitter/error', 'Social\TwitterController@error')->name('twitter.error');
Route::get('/twitter/logout', 'Social\TwitterController@logout')->name('twitter.logout');

Route::get('/instagram/checkpoint', 'Social\InstagramController@getCheckpoint')->name('instagram.checkpoint');
Route::post('/instagram/checkpoint', 'Social\InstagramController@postCheckpoint')->name('instagram.post-checkpoint');
Route::resource('instagram', 'Social\InstagramController');

Route::get('/facebook/profile', 'Social\FacebookController@profile')->name('facebook.profile');
Route::get('/facebook/pages', 'Social\FacebookController@pages')->name('facebook.pages');
Route::get('/facebook/groups', 'Social\FacebookController@groups')->name('facebook.groups');
Route::get('/facebook/select/{type}/{fid}', 'Social\FacebookController@select')->name('facebook.select');
Route::get('/facebook/select_custom/{type}', 'Social\FacebookController@select_custom')->name('facebook.select_custom');
Route::get('/facebook/callback', 'Social\FacebookController@callback')->name('facebook.callback');
Route::get('/facebook/choice', 'Social\FacebookController@choice')->name('facebook.choice');
Route::get('/facebook/preselect', 'Social\FacebookController@preSelect')->name('facebook.preselect');
Route::get('/facebook/preselect/{user_id}/{select}', 'Social\FacebookController@preSelectById')->name('facebook.preselect');

Route::get('/linkedin/profile', 'Social\LinkedinController@profile')->name('linkedin.profile');
Route::get('/linkedin/pages', 'Social\LinkedinController@pages')->name('linkedin.pages');
Route::get('/linkedin/choice', 'Social\LinkedinController@choice')->name('linkedin.choice');
Route::get('/linkedin/select/{id}', 'Social\LinkedinController@select')->name('linkedin.select');
Route::get('/linkedin/callback', 'Social\LinkedinController@accept')->name('linkedin.accept');
Route::get('/linkedin/cancel', 'Social\LinkedinController@cancel')->name('linkedin.cancel');
Route::get('linkedin/{id}.jpg', 'Social\LinkedinController@image');

Route::get('/pinterest/profile', 'Social\PinterestController@profile')->name('pinterest.profile');
Route::get('/pinterest/boards', 'Social\PinterestController@boards')->name('pinterest.boards');
Route::post('/pinterest/save', 'Social\PinterestController@save')->name('pinterest.save');
Route::get('/pinterest/callback', 'Social\PinterestController@callback')->name('pinterest.callback');

Route::get('/install', 'Install\InstallController@index');
Route::get('/install/create', 'Install\InstallController@create'); // private use only
Route::get('/install/update', 'Install\InstallController@createUpdate'); // private use only
Route::get('/install/admin', 'Install\InstallController@admin');
Route::get('/install/finish', 'Install\InstallController@finish');
Route::post('/install', 'Install\InstallController@postIndex');
Route::post('/install/admin', 'Install\InstallController@postAdmin');
Route::post('/install/finish', 'Install\InstallController@postFinish');
Route::get('/install/upgrade', 'Install\InstallController@upgrade');

Route::get('/debug/phpinfo', 'DebugController@phpinfo');
Route::get('/debug/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::get('/debug/cron', 'DebugController@cron');

/**
 * Teamwork routes
 */
Route::group(['prefix' => 'teams', 'namespace' => 'Teamwork'], function()
{
    Route::get('/', 'TeamController@index')->name('teams.index');
    Route::get('create', 'TeamController@create')->name('teams.create');
    Route::post('teams', 'TeamController@store')->name('teams.store');
    Route::get('edit/{id}', 'TeamController@edit')->name('teams.edit');
    Route::put('edit/{id}', 'TeamController@update')->name('teams.update');
    Route::delete('destroy/{id}', 'TeamController@destroy')->name('teams.destroy');
    Route::get('switch/{id}', 'TeamController@switchTeam')->name('teams.switch');

    Route::get('members/{id}', 'TeamMemberController@show')->name('teams.members.show');
    Route::get('members/resend/{invite_id}', 'TeamMemberController@resendInvite')->name('teams.members.resend_invite');
    Route::post('members/{id}', 'TeamMemberController@invite')->name('teams.members.invite');
    Route::delete('members/{id}/{user_id}', 'TeamMemberController@destroy')->name('teams.members.destroy');

    Route::get('accept/{token}', 'AuthController@acceptInvite')->name('teams.accept_invite');
});

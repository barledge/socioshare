@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-sm-12">


                              <? if(!$cron_running && !env('DEMO')) : ?>
                              <div class="alert alert-danger" role="alert" style="background: #DD2C00; color: #fff">
                                <strong>Alert:</strong> Your cron Job is not running. Enter the following in your crontab:<br />
                                <code>* * * * * php <?= base_path('artisan') ?> schedule:run >> /dev/null 2>&1</code>
                              </div>
                              <? endif; ?>

					<? if ($jumbo) : ?>

                    <? if ($jumbo == 'connect') : ?>
                    <div class="alert alert-info clearfix" role="alert"  >
                      <div class="col-sm-9"><p class="form-control-static"><strong>Starter Tip:</strong> Please add at least one social media account before continuing.</p></div>
                      <div class="col-sm-3"><a class="btn btn-primary pull-right" href="<?= url('/social-accounts')?>" role="button">Connect Account</a></div>
                    </div>
                    <? endif; ?>

                    <? if ($jumbo == 'schedule') : ?>
                    <div class="alert alert-info clearfix" role="alert"  >
                      <div class="col-sm-6"><p class="form-control-static"><strong>Note:</strong> Set-up the times you want to post at.</p></div>
                      <div class="col-sm-6"><a class="btn btn-primary pull-right" href="<?= url('/schedule')?>" role="button">Set-up posting schedule</a></div>
                    </div>
                    <? endif; ?>

                    <? if ($jumbo == 'add_post') : ?>
                    <div class="alert alert-info clearfix" role="alert"  >
                      <div class="col-sm-6"><p class="form-control-static"><strong>Get started:</strong> Add your first post</p></div>
                      <div class="col-sm-6"><a class="btn btn-primary pull-right" href="<?= url('/content/create')?>" role="button">Create new post</a></div>
                    </div>
                    <? endif; ?>

                    <? endif; ?>


          <div class="panel panel-default">
              <div class="panel-body">
                <div class="col-sm-12">

                  <div class="row">
                    <div class="col-sm-12">

                      <h4>Welcome to your dashboard</h4>
                      <p>What would you like to do today?</p>

                    <hr />


                    </div>
                  </div>
                  <br />
                  </div>


                  <div class="col-sm-12">

                  <div class="row">


                    <div class="col-sm-6   mb-l">
                      <div class="row">

                        <div class="col-sm-3">
                          <img class="uk-comment-avatar" src="<?= asset('img/content.png') ?>" alt="">
                        </div>
                        <div class="col-sm-9">
                          <ul class="uk-nav uk-nav-default">
                            <li class="uk-active"><a href="<?= url('/content/create') ?>">Content</a></li>
                  						<li><a href="<?= url('/content/create') ?>">Create new content</a></li>
                  						<li><a href="<?= url('/drafts') ?>">View drafts</a></li>
                  						<li><a href="<?= url('/content') ?>">View all posts</a></li>
                  					</ul>
                        </div>

                      </div>
                    </div>

                    <div class="col-sm-6   mb-l">
                      <div class="row">

                        <div class="col-sm-3">
                          <img class="uk-comment-avatar" src="<?= asset('img/calendar.png')?>" alt="">
                        </div>
                        <div class="col-sm-9">
                          <ul class="uk-nav uk-nav-default">
                  						<li class="uk-active"><a href="<?= url('/queue/create?tab=in_queue') ?>">Queue</a></li>
                  						<li><a href="<?= url('/queue/create?tab=in_queue') ?>">Queued posts</a></li>
                  						<li><a href="<?= url('/queue/create?tab=custom_schedule') ?>">Scheduled posts</a></li>
                  					</ul>
                        </div>

                      </div>
                    </div>

                  </div>

                  <div class="row">

                    <div class="col-sm-6   mb-l">
                      <div class="row">

                        <div class="col-sm-3">
                          <img class="uk-comment-avatar" src="<?= asset('img/network.png')?>" alt="">
                        </div>
                        <div class="col-sm-9   mb-xl">
                          <ul class="uk-nav uk-nav-default">
                  						<li class="uk-active"><a href="<?= url('/social-accounts') ?>">Social Media</a></li>
                  						<li><a href="<?= url('/social-accounts') ?>">Connect accounts</a></li>
                  						<li><a href="<?= url('/groups') ?>">Manage Groups</a></li>
                  					</ul>
                        </div>

                      </div>
                    </div>

                    <div class="col-sm-6   mb-l">
                      <div class="row">

                        <div class="col-sm-3">
                          <img class="uk-comment-avatar" src="<?= asset('img/services.png')?>" alt="">
                        </div>
                        <div class="col-sm-9">
                          <ul class="uk-nav uk-nav-default">
                  						<li class="uk-active"><a href="<?= url('/settings')?>">Settings</a></li>
                  						<li><a href="<?= url('/settings#password')?>">Change password</a></li>
                  						<li><a href="<?= url('/settings#time')?>">Time and Date</a></li>
                  						<li><a href="<?= url('/history')?>">Logs &amp; History</a></li>
                  					</ul>
                        </div>

                      </div>
                    </div>
                    </div>




                  </div>
              </div>
          </div>


        </div>
    </div>
</div>


@endsection

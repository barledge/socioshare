@extends('layouts.inner')
@section('title', 'Settings')

@section('inner')


          <div class="row">

            <div class="col-md-12">
              <br />
              @if(session()->has('message'))
                  <div class="alert alert-success">
                      {{ session()->get('message') }}
                  </div>
              @endif

              @foreach ($errors->all() as $error)

                <div class="alert alert-danger">{{ $error }}</div>

              @endforeach

              <div class="panel panel-default">
              <div class="panel-body">
                <div class="row">

                  <div class="col-md-5">
                        <h4 id="change_email">Change email</h4>
                        <p>This is used only for logging in. Make sure you use something you remember.</p>
                    </div>

                  <div class="col-md-7">
                    <form method="POST" action="<?= route('settings.store') ?>">
                      {{ csrf_field() }}
                      <div class="form-group">
                        <label for="exampleInputEmail1">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="" value="<?= auth()->user()->email ?>">
                      </div>
                      <button type="submit" class="btn btn-primary">save</button>
                    </form>
                    </div>

                    </div>
                    </div>
                    </div>

              <div class="panel panel-default">
              <div class="panel-body">
                <div class="row">

                  <div class="col-md-5">
<h4 id="password">Change password</h4>
<p>Great passwords use lower and uppercase characters, numbers and symbols such as !"$£%&. Make sure you remember it.</p>
                    </div>

                  <div class="col-md-7">
                    <form method="POST" action="<?= route('settings.store') ?>">
                      {{ csrf_field() }}
                      <div class="form-group">
                        <label for="exampleInputEmail1">New password</label>
                        <input type="password" class="form-control" name="password" placeholder="">
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">Confirm new password</label>
                        <input type="password" class="form-control" name="confirm_password" placeholder="">
                      </div>
                      <button type="submit" class="btn btn-primary">save</button>
                    </form>
                    </div>

                    </div>
                    </div>
                    </div>

                    @hasrole('admin')

                <hr />
                <div class="panel panel-default">
                <div class="panel-body">
                <div class="row">

                  <div class="col-md-5">
<h4 id="time">Time &amp; Date</h4>
<p>Select the timezone you want to post from.</p>
                    </div>

                  <div class="col-md-7">
                    <form method="POST" action="<?= route('settings.store') ?>">
                      {{ csrf_field() }}
                      <div class="form-group">
                        <label for="exampleInputEmail1">Timezone region</label>

                        {{ Form::select('timezone', $tzlist, $current_tz, ['class' => 'form-control']) }}
                      </div>
                      <button type="submit" class="btn btn-primary">save</button>
                    </form>
                    </div>

                    </div>
                    </div>
                    </div>

                <hr />
                <div class="panel panel-default">
                <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4 id="api_keys">API Keys</h4>
                        <p>Please enter your API keys before trying to post any content.</p><br />

                        @if(session()->has('alert'))
                            <div class="alert alert-danger">
                                {{ session()->get('alert') }}
                            </div>
                        @endif

                    <form class="form-horizontal" method="POST" action="<?= route('settings.store') ?>">
                        {{ csrf_field() }}
                        {{ Form::hidden('API_KEYS', 1) }}
                        <h5 class="text-muted"><i class="fa fa-angle-right" aria-hidden="true"></i> Twitter</h5>

                          <div class="form-group">
                            <label class="col-sm-4 control-label">Consumer Key</label>
                            <div class="col-sm-8">
                                {{ Form::text('TWITTER_CONSUMER_KEY', env('TWITTER_CONSUMER_KEY'), ['class' => 'form-control']) }}
                            </div>
                          </div>
                          <div class="form-group">
                              <label class="col-sm-4 control-label">Consumer Secret</label>
                            <div class="col-sm-8">
                                {{ Form::text('TWITTER_CONSUMER_SECRET', env('TWITTER_CONSUMER_SECRET'), ['class' => 'form-control']) }}
                            </div>
                          </div>
                          <div class="form-group">
                              <label class="col-sm-4 control-label">Access Token</label>
                            <div class="col-sm-8">
                                {{ Form::text('TWITTER_ACCESS_TOKEN', env('TWITTER_ACCESS_TOKEN'), ['class' => 'form-control']) }}
                            </div>
                          </div>
                          <div class="form-group">
                              <label class="col-sm-4 control-label">Access Token Secret</label>
                            <div class="col-sm-8">
                                {{ Form::text('TWITTER_ACCESS_TOKEN_SECRET', env('TWITTER_ACCESS_TOKEN_SECRET'), ['class' => 'form-control']) }}
                            </div>
                          </div>

                        <hr />
                        <h5 class="text-muted"><i class="fa fa-angle-right" aria-hidden="true"></i> Facebook</h5>

                          <div class="form-group">
                            <label class="col-sm-4 control-label">App ID</label>
                            <div class="col-sm-8">
                                {{ Form::text('FACEBOOK_APP_ID', env('FACEBOOK_APP_ID'), ['class' => 'form-control']) }}
                            </div>
                          </div>
                          <div class="form-group">
                              <label class="col-sm-4 control-label">App Secret</label>
                            <div class="col-sm-8">
                                {{ Form::text('FACEBOOK_APP_SECRET', env('FACEBOOK_APP_SECRET'), ['class' => 'form-control']) }}
                            </div>
                          </div>

                        <hr />
                        <h5 class="text-muted"><i class="fa fa-angle-right" aria-hidden="true"></i> Pinterest</h5>

                          <div class="form-group">
                            <label class="col-sm-4 control-label">Pinterest ID</label>
                            <div class="col-sm-8">
                                {{ Form::text('PINTEREST_ID', env('PINTEREST_ID'), ['class' => 'form-control']) }}
                            </div>
                          </div>
                          <div class="form-group">
                              <label class="col-sm-4 control-label">Pinterest Secret</label>
                            <div class="col-sm-8">
                                {{ Form::text('PINTEREST_SECRET', env('PINTEREST_SECRET'), ['class' => 'form-control']) }}
                            </div>
                          </div>

                        <hr />
                        <h5 class="text-muted"><i class="fa fa-angle-right" aria-hidden="true"></i> LinkedIn</h5>

                          <div class="form-group">
                            <label class="col-sm-4 control-label">LinkedIn ID</label>
                            <div class="col-sm-8">
                                {{ Form::text('LINKEDIN_KEY', env('LINKEDIN_KEY'), ['class' => 'form-control']) }}
                            </div>
                          </div>
                          <div class="form-group">
                              <label class="col-sm-4 control-label">LinkedIn Secret</label>
                            <div class="col-sm-8">
                                {{ Form::text('LINKEDIN_SECRET', env('LINKEDIN_SECRET'), ['class' => 'form-control']) }}
                            </div>
                          </div>

                          <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                              <button type="submit" class="btn btn-primary pull-right">save</button>
                            </div>
                          </div>
                    </form>
                </div>


                </div>
                </div>
                </div>




                    @endhasrole



                      <br />  <br />
                      <br />  <br />
                      <br />  <br />



</div>



            </div>


@endsection

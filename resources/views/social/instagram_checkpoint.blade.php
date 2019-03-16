@extends('layouts.inner')
@section('title', 'Connect your social accounts')

@section('inner')

<div class="container">
    <div class="row">
        <div class="col-sm-12">

          <div class="row">

            <div class="col-md-12">
              <a href="/social-accounts"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back</a><br /><br />

              <div class="panel panel-default">
                <div class="panel-body  ">
                <h4><img src="/img/instagram.png" style="width: 20px; margin-right: 10px;"/> Enter your Instagram account details</h4>
                <hr />

                <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                      @if(Session::has('alert-' . $msg))

                      <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                      @endif
                    @endforeach
                  </div> <!-- end .flash-message -->

                <div class="row">

                  <div class="col-md-8">
                    @if(!empty($errors->first()))
                        <div class="row col-lg-12">
                            <div class="alert alert-danger">
                                <span>{{ $errors->first() }}</span>
                            </div>
                        </div>
                    @endif
                <form action="/instagram" method="POST">
                   {{ csrf_field() }}
                  <div class="form-group">
                    <label>Verify your email</label>
                    <input type="text" class="form-control" name="instagram_username" placeholder="Your instagram login">
                  </div>
                  <div class="form-group">
                    <label>Password</label>
                    <input type="text" class="form-control" name="instagram_password" placeholder="Your instagram password">
                  </div>
                  <div class="form-group">
                    <label>Proxy</label>
                    <input type="text" class="form-control" name="instagram_proxy" placeholder="e.g. http://user:pass@ipORhost:port">
                    <p class="help-block"><small>Optional: Use a proxy server if you are posting to multiple instagram accounts from the same server. Please make sure the proxy works.</small></p>
                  </div>
                  <button type="submit" class="btn btn-primary"><i class="fa fa-lock" aria-hidden="true"></i> Save securely</button>
                  <br />
                  <br />
                </form>

                <br />
                <br />



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

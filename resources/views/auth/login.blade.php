@extends('layouts.landing')

@section('content')

    <div class="container-fluid" style="margin-top:5%">

        <div class="panel member_signin" style="width: 100%; max-width: 340px;">
            <div class="panel-body">
                <br/>
                <div class="text-center">
                    <div class="fa_user">
                        <i class="fa fa-lock"></i>
                    </div>
                    <p class="member">{{ config('app.name') }} login</p>
                </div>
                <br/>
                <form class="loginform" method="POST" action="{{ route('login') }}">
                    {{ csrf_field() }}

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <div class="input-group">
                            <input id="email" type="email" placeholder="Username" class="form-control" name="email"
                                   value="{{ (env('DEMO')?'admin@example.com':old('email')) }}" required autofocus>
                        </div>
                        @if ($errors->has('email'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <div class="input-group">
                            <input id="password" type="password" placeholder="Password" class="form-control"
                                   name="password" value="{{ (env('DEMO')?'123456':old('password')) }}" required
                                   autofocus>
                        </div>
                        @if ($errors->has('password'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="form-group text-left">
                        <div class="col-md-12s">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    Remember Me
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-l login" ng-hide="loading">LOG IN <i
                                class="fa fa-chevron-right"></i></button>
                    <br/>
                    <br/>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="login-help">
                                <a href="{{ route('password.request') }}">Forgot Password?</a>
                            </div>
                        </div>

                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-12 text-left">
                            <p class="muted">Please use a valid username and password to login to the dashboard</p>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>


@endsection

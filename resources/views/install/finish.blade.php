@extends('layouts.install')
@section('title', 'Congratulations!')

@section('content')


          <div class="flash-message">
              @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                @endif
              @endforeach
            </div> <!-- end .flash-message -->

            <p class="text-left">Warbler has been installed. Please make sure you enter the following details in the .env file:</p>
            <ul style=" list-style-position: inside;">
              <li>Facebook “App ID” and “App Secret”</li>
              <li>Twitter “access token”, “secret”, “consumer key” and “consumer secret”</li>
            </ul>

            <p>...and also set-up the following crontab:</p>
            <pre>* * * * * php <?= base_path('artisan') ?> schedule:run >> /dev/null 2>&1</pre>

            <a href="/home" class="btn btn-primary">Go to dashboard <i class="fa fa-chevron-right" aria-hidden="true"></i></a>



@endsection

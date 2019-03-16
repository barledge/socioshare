<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="file-max-size" content="{{ file_upload_max_size() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->

    <script data-pace-options='{  "ajax": {
                  "trackMethods": ["GET", "POST", "PATCH","PUT", "DELETE"]
            } }' src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-flash.min.css"
          rel="stylesheet"/>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="/public/css/app.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://unpkg.com/css-spaces@0.3.5/dist/spaces.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pretty-checkbox/2.2.1/pretty.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/jquery.datetimepicker.min.css"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.min.css"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-highlighttextarea/3.1.3/jquery.highlighttextarea.min.css"/>


</head>
<body>

<div id="busy"
     style="display: none; position: absolute; top: 50px; border-radius: 0 0 0 8px; right: 0px; width: 100px; height: 30px; line-height: 30px; background: #555; padding: 0 5px; color: #fff;">
    <i class="fa fa-spinner fa-spin fa-fw"></i> Working...
</div>

<div id="app">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    @if (Auth::guest() || auth()->user()->hasrole('client'))
                        &nbsp;
                    @else
                        <li><a href="{{ route('content.create') }}">Content</a></li>
                        <li><a href="{{ route('queue.create') }}">Queue</a></li>
                        <li><a href="{{ route('social-accounts.index') }}">Connect Accounts</a></li>
                    @endif
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ route('login') }}">Login</a></li>

                    @elseif(auth()->user()->hasrole('client'))
                        <li><a href="{{ route('review.index') }}">Review Posts</a></li>
                        <li><a href="{{ route('settings.index') }}#password">Change password</a></li>
                        <li>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                           document.getElementById('logout-form').submit();">
                                <i class="fa fa-power-off" aria-hidden="true"></i>
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('analytics.index') }}"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
                        </li>
                        <li><a href="{{ route('content.index') }}"><i class="fa fa-history" aria-hidden="true"></i></a>
                        </li>
                        <li><a href="{{ route('settings.index') }}"><i class="fa fa-cog" aria-hidden="true"></i></a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false">
                                My Account <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ route('settings.index') }}#change_email">Change email</a></li>
                                <li><a href="{{ route('settings.index') }}#password">Change password</a></li>
                                <li><a href="{{ route('settings.index') }}#time">Time &amp; Date configuration</a></li>
                                <li><a href="{{ route('settings.index') }}#api_keys">Manage API keys</a></li>
                                <li>
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')
</div>

<footer class="footer mb-l mt-s">
    <div class="container  text-center">
        <hr/>
        <p class="text-muted text-center mb-0">
            <small>Date/Time: <?php echo \Carbon\Carbon::now()->format('jS M Y - H:i:s a'); ?></small>
        </p>
        <?php if(!env('DEMO') && !auth()->guest() && auth()->user()->hasrole('admin')) : ?>
        <p class="text-muted text-center">
            <small><?php
                // Memory usage: 4.55 GiB / 23.91 GiB (19.013557664178%)
                $memUsage = getServerMemoryUsage(false);
                echo sprintf("RAM usage: %s/%s (%s%%)",
                    getNiceFileSize($memUsage["total"] - $memUsage["free"]),
                    getNiceFileSize($memUsage["total"]),
                    number_format(getServerMemoryUsage(true), 0)
                );
                ?></small>
        </p>
        <?php  endif; ?>
        <p class="text-muted text-center">
            <small><a href="http://www.urvam.com" target="_blank" class="text-muted">Urvam
                    Technologies&copy;</a> <?php echo date('Y'); ?></small>
        </p>
    </div>
</footer>


<!-- Scripts -->
<script>
    function site_url(path) {
        /*var url = '<?= url('')?>/' + path;
         url = url.replace(/\/\/+/g, '/');*/
        return path;
    }
    function asset(path) {
        return '<?= asset('') ?>' + path;
    }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/alertifyjs-alertify.js/1.0.11/js/alertify.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.full.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.matchHeight/0.7.2/jquery.matchHeight-min.js"></script>

</body>
</html>

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://unpkg.com/css-spaces@0.3.5/dist/spaces.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pretty-checkbox/2.2.1/pretty.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/jquery.datetimepicker.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.min.css"/>

</head>
<body>

    <div id="busy" style="display: none; position: absolute; top: 50px; border-radius: 0 0 0 8px; right: 0px; width: 100px; height: 30px; line-height: 30px; background: #555; padding: 0 5px; color: #fff;"><i class="fa fa-spinner fa-spin fa-fw"></i> Working...</div>

    <div id="app">



        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        Installer
                    </a>
                </div>


            </div>
        </nav>
        <div class="container">
            <div class="row">
              <div class="col-sm-12">
                <div class="panel panel-default">
<div class="panel-heading">
  @yield('title')

  </div>
  <div class="panel-body">

    @yield('content')




            </div>
          </div>

              </div>
            </div>
          </div>
    </div>
    </div>

    <footer class="footer mb-l mt-s">
      <div class="container  text-center">
        <hr />
        <p class="text-muted text-center"><small><a href="//trywarbler.com" class="text-muted">TryWarbler&copy;</a> <?= date('Y') ?></small></p>
      </div>
    </footer>

</body>
</html>

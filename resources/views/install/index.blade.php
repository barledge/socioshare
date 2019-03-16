@extends('layouts.install')
@section('title', 'Your MySQL details')

@section('content')


          <div class="flash-message">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style=" list-style-position: inside;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            </div> <!-- end .flash-message -->

{!! Form::open(['url' => url()->current(), 'class'=>'form-horizontal']) !!}

  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3  control-label">Database name</label>
    <div class="col-sm-9">
      {{ Form::text('database_name', null, ['class' => 'form-control', 'placeholder' => 'warbler']) }}
      <p class="help-block">The name of the database for Warbler to use<br />(note: you must have already created this).</p>
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3  control-label">Username</label>
    <div class="col-sm-9">
      {{ Form::text('database_username', null, ['class' => 'form-control', 'placeholder' => 'username']) }}
      <p class="help-block">Your MySQL username.</p>

    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3  control-label">Password</label>
    <div class="col-sm-9">
      {{ Form::text('database_password', null, ['class' => 'form-control', 'placeholder' => 'password']) }}
      <p class="help-block">..and your MySQL password.</p>

    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3  control-label">Database host</label>
    <div class="col-sm-9">
      {{ Form::text('database_host',  old('database_host', 'localhost'), ['class' => 'form-control', 'placeholder' => 'localhost']) }}
      <p class="help-block">You should be able to get this info form your webshot if <i>localhost</i> doesn't work.</p>

    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-9">
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </div>
  {!! Form::close() !!}

@endsection

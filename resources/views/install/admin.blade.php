@extends('layouts.install')
@section('title', 'Admin login details')

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

{!! Form::open(['url' => url()->current() , 'class'=>'form-horizontal']) !!}

  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3  control-label">Admin email</label>
    <div class="col-sm-9">
      {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'e.g. jack@sparrow.com']) }}
      <p class="help-block">Double check your email before continuing.</p>
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3  control-label">Admin name</label>
    <div class="col-sm-9">
      {{ Form::text('admin_username',  old('admin_username', 'admin'), ['class' => 'form-control', 'placeholder' => 'username']) }}
      <p class="help-block">Display names can only have alphanumeric characters.</p>

    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3  control-label">Admin password (twice)</label>
    <div class="col-sm-9">
      {{ Form::password('admin_password', ['class' => 'form-control', 'placeholder' => 'Password']) }}

    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3  control-label">&nbsp;</label>
    <div class="col-sm-9">
      {{ Form::password('admin_password_confirmation', ['class' => 'form-control', 'placeholder' => 'Confirm password']) }}
      <p class="help-block">The password should be at least 6 characters long.</p>

    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-9">
      <button type="submit" class="btn btn-primary">Install Warbler</button>
    </div>
  </div>
  {!! Form::close() !!}

@endsection

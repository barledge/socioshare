@extends('layouts.inner')
@section('title', 'Analytics')

@section('inner')

<div class="row">

    <div class="col-md-12">
        <h4>Analytics</h4><br />

          <div class="row">
                <div class="col-md-12">
                    <analytics :groups="{{json_encode($groups)}}" :socialAccounts="{{ $social_accounts->toJSON() }}"></analytics>
                </div>
          </div>
          <br />

    </div>
</div>

@endsection

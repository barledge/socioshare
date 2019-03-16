@extends('layouts.inner')
@section('title', 'Schedule')

@section('inner')
<a href="<?= route('social-accounts.index') ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back to accounts</a><br /><br />

<div class="panel panel-default">
<div class="panel-body">
  <h4>Posting times for &lsquo;{{$account->label}}&rsquo;</h4>
          <div class="row">

            <div class="col-md-12">

                <p>Select the times you want to post to your social network accounts.</p>
                <br />
          </div>
          </div>
          <schedule :my-days="{{json_encode($days)}}" id="{{$account->id}}" :my-times="{{json_encode($times)}}"></schedule>
        </div>
        </div>


@endsection

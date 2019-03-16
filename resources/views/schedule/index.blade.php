@extends('layouts.inner')
@section('title', 'Schedule')

@section('inner')
<a href="<?= route('queue.create') ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i> View queue</a><br /><br />

<div class="panel panel-default">
<div class="panel-body">
  <h4>Posting times</h4>
          <div class="row">

            <div class="col-md-12">

                <p>Select the times you want to post to twitter, facebook or instagram.</p>
                <br />
          </div>
          </div>
          <schedule :my-days="{{json_encode($days)}}" :my-times="{{json_encode($times)}}"></schedule>
        </div>
        </div>


@endsection

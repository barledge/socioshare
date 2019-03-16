@extends('layouts.inner')
@section('title', 'History')

@section('inner')


<div class="row ">
    <? foreach($posts as $post) : ?>

      <div class="col-sm-6 panel-item mb-xxs">

          <review-post :my-post="{{$post->toJSON()}}"></review-post>

      </div>

    <? endforeach; ?>
</div>


@endsection

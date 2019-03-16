@extends('layouts.inner')
@section('title', 'Feeds')

@section('inner')


          <div class="row">

            <div class="col-md-12">

                              <ul class="nav nav-pills">
                    <li role="presentation"><a href="<?= route('content.create') ?>">Add/Edit Post</a></li>
                    <li role="presentation" ><a href="<?= route('content.index') ?>">Drafts</a></li>
                    <li role="presentation"  class="active"><a href="<?= route('feeds.index') ?>">RSS Feeds</a></li>
                </ul>
<br />

<div class="panel panel-default">
    <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                  <h4>RSS Feeds</h4>
                <p>Feeds added here will be published to your twitter, facebook and instagram accounts automatically without being queued.</p>
                <br />
                <feeds :items="{{json_encode($feeds)}}"></feeds>

                      </div>
                      </div>
                      </div>
      </div>
      </div>

            </div>
          </div>



@endsection

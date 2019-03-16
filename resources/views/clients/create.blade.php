@extends('layouts.inner')
@section('title', 'Clients')

@section('inner')
<a href="<?= route('groups.index') ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back to accounts</a><br /><br />
<div class="panel panel-default">
    <div class="panel-body">
                  <div class="row">


                              <div class="col-sm-8 col-sm-offset-2">

                                <div class="row">


                                            <div class="col-sm-6">
											<br />
                  <h4>Adding a new client</h4>

                </div>

                <div class="col-sm-6">
                </div>
                </div>
                  <hr />

 {!! form($form, ['autocomplete' => 'new-password']) !!}
 <br />
        </div>
        </div>
        </div>

	</div>


@endsection

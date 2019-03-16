@extends('layouts.inner')
@section('title', 'Schedule')

@section('inner')
<a href="<?= route('social-accounts.index') ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back to accounts</a><br /><br />

                @if(session()->has('message'))
                <br />
                    <div class="alert alert-success  alert-dismissable">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        {!! session()->get('message') !!}
                    </div>
                @endif
<group :platforms="{{json_encode($platforms)}}"></group>
@endsection

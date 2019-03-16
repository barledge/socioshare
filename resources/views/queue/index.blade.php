@extends('layouts.inner')
@section('title', 'Queued Posts')

@section('inner')
    <div class="row">
        <div class="col-md-12">
            <h4>Queued Posts</h4><br/>
            <form>
                <div class="row">
                    <div class="col-md-12">
                        <queue :groups="{{json_encode($groups)}}" :socialAccounts="{{ $social_accounts->toJSON() }}"
                               defaulttab=<?php echo $tab; ?>>
                        </queue>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@extends('layouts.inner')
@section('title', 'History')

@section('inner')
    <div class="panel panel-default">
        <div class="panel-body" style="min-height: 60vh">

            <div class="row">

                <div class="col-md-12">
                    <h4>History &amp; Logs</h4>

                    @if(count($notices) == 0)
                        <br/>
                        <div class="alert alert-warning" role="alert">
                            <strong>Notice:</strong> You have no history or logs yet.
                        </div>
                    @endif

                    <div class="history-wapper">
                        <div class="history-container">
                            <div class="item">
                                <div id="timeline">
                                    <div>
                                        @foreach($notices as $date => $notice)
                                            <section class="year">
                                                <h3>{{ $date }}</h3>
                                                <section>
                                                    <ul>
                                                        @foreach($notice as $item)
                                                            <li>
                                                                {{ $item->message }}
                                                                <a href="{{ $item->url }}">
                                                                    {{ $item->type }}
                                                                    #{{ $item->type_id }}</a><br/>
                                                                @if($item->error)
                                                                    <p class="text-danger" style="color: #EF5350">
                                                                        <i class="mdi mdi-alert-outline"></i>&nbsp;&nbsp;{{ $item->error }}
                                                                    </p>
                                                                @else
                                                                @endif
                                                                <h4 style="margin-top: 0">&nbsp;&nbsp;Posted
                                                                    to {{$item->social_account_label}}
                                                                    at {{ $item->created_at->toTimeString() }}</h4>
                                                            </li>
                                                            @endforeach
                                                    </ul>
                                                </section>
                                            </section>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

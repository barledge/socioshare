@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">

            <div class="col-md-12">

                <h4>Select a company</h4>
                <br/>
                <div class="row">

                    <div class="col-md-12">

                        @if(count($companies) == 0)
                            <div class="alert alert-danger">
                                <span>Oops! No results found.</span>
                            </div>
                        @else

                            <div class="row">

                                @foreach($companies as $company)
                                    <div class="col-sm-3 col-md-4">
                                        <div class="panel panel-default">
                                            <div class="panel-body">

                                                <div class="row">
                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <img src="{{ Avatar::create($company['name'])->toBase64() }}"
                                                             style="width: 100%; display: block;" src="="
                                                             data-holder-rendered="true">
                                                    </div>

                                                    <div class="col-sm-12 col-md-6 col-lg-8">
                                                        <div class="caption">
                                                            <h5 class="mt-0 mb-0">
                                                                <strong><?= $company['name'] ?></strong>
                                                            </h5>
                                                            <p class="mt-0 mb-0"><a
                                                                        href="<?= url('linkedin/select/' . $company['id']) ?>"
                                                                        class="btn btn-primary btn-xs btn-block"
                                                                        role="button">Select</a>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

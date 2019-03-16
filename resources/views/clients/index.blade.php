@extends('layouts.inner')
@section('title', 'Clients')

@section('inner')
<a href="<?= route('social-accounts.index') ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back to accounts</a><br /><br />
<div class="panel panel-default">
    <div class="panel-body">
                  <div class="row">


                              <div class="col-sm-12">

                                <div class="row">


                                            <div class="col-sm-6">
                  <h4>Clients</h4>

                </div>

                <div class="col-sm-6">
<a href="/clients/create" class="btn btn-default pull-right"><i class="fa fa-plus" aria-hidden="true"></i> Add new client</a>
                </div>
                </div>


                @if(session()->has('message'))
                <br />
                    <div class="alert alert-success">
                        {!! session()->get('message') !!}
                    </div>
                @endif
                  <hr />

                  <table class="table">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Date Created</th>
                        </tr>
                      </thead>
                      <tbody>
                        <? foreach($clients as $client) : ?>
                        <tr>
                          <td>#<?= $client->id ?></td>
                          <td><?= $client->name ?></td>
                          <td><a href="/clients/{{$client->id}}/edit"><?= $client->email ?></a></td>
                          <td><?= $client->created_at ?></td>
                          <td><a href="#" class="delete-confirm" data-text="Deleting this account means any scheduled posts associated with it may not be posted" data-url="/clients/<?= $client->id ?>"><i class="mdi mdi-close" aria-hidden="true"></i></a></td>

                        </tr>
                        <? endforeach; ?>
                      </tbody>
                    </table>


                {{ $clients->appends($_GET)->links() }}
        </div>
        </div>
        </div>

	</div>


@endsection

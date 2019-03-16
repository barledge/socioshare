@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">

            <div class="col-sm-6 col-sm-offset-3">

              <div class="panel panel-default">
                  <div class="panel-body">

                <h4>Facebook account selection</h4>
                <br />
                <a href="/facebook/<?= $select ?>" class="btn btn-block btn-primary">Proceed with default account <i aria-hidden="true" class="fa fa-chevron-right"></i></a>

                <br />
                <div class="row">

                  <div class="col-md-12">
                    <h5 style="margin-bottom: 0">Or select an account to continue</h5>
<small class="text-muted">All accounts below have unique Facebook App Keys and Secrets so you can post with their own brand names.</small><br />
<div class="scroll" style="height: 225px;">

                    <div class="row">
                    <? foreach($users as $user) : ?>
                      <div class="col-md-12">
                        <div class="panel panel-default">
                          <div class="panel-body">
                        <div class="row">
       <div class="col-sm-3">
         <img src="{{ Avatar::create($user->name)->toBase64() }}" style="width: 100%; display: block;" src="=" data-holder-rendered="true">
       </div>

       <div class="col-sm-9">
           <div class="caption">
             <h5 class="mt-0 mb-0"><strong><?= $user->name ?></strong></h5>
             <p class="mt-0 mb-0"><?= $user->email ?></p>
             <p  class="mt-0 mb-0"><a href="<?= url('facebook/preselect/'. $user->id .'/'. $select) ?>" class="btn btn-primary btn-xs btn-block" role="button">Select <i aria-hidden="true" class="mdi mdi-chevron-right"></i></a>
             </p>
           </div>
       </div>
   </div>

 </div>
</div>


                      </div>
                    <? endforeach; ?>
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

@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">

            <div class="col-md-12">

                <h4>Choose a profile, page or group</h4>
                <br />
                <div class="row">

                  <div class="col-md-12">

                        @if(count($options) == 0)
                            <div class="alert alert-danger">
                                <span>Oops! No results found.</span>
                            </div>
                        @endif

                    <div class="row">

                    <? foreach($options as $option) : ?>
                      <div class="col-md-4">
                        <div class="panel panel-default">
                          <div class="panel-body">
                        <div class="row">
       <div class="col-sm-12 col-md-6 col-lg-4">
         <img src="https://graph.facebook.com/<?= $option->getProperty('id') ?>/picture" style="width: 100%; display: block;" src="=" data-holder-rendered="true">
       </div>

       <div class="col-sm-12 col-md-6 col-lg-8">
           <div class="caption">
             <h5 class="mt-0 mb-0"><strong><?= $option->getProperty('name') ?></strong></h5>
             <p class="mt-0 mb-1"><?= ucfirst($type) ?></p>
             <p  class="mt-0 mb-0"><a href="<?= url('facebook/select/'.$type.'/'.$option->getProperty('id')) ?>" class="btn btn-primary btn-xs btn-block" role="button">Select</a>
             </p>
           </div>
       </div>
   </div>

 </div>
</div>


                      </div>
                    <? endforeach; ?>



                    <div class="col-md-4">
                      <div class="panel panel-default">
                        <div class="panel-body">

                              <div class="row">
                                 <div class="col-sm-12">
                                     <div class="caption">

                                         {!! Form::open(['url' => '/facebook/select_custom/'.$type, 'method' => 'get']) !!}
                                            <div class="form-group">
                                                <input type="text" id="custom_id" name="custom_id" class="form-control" placeholder="Custom ID">
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-xs btn-block">Select</button>
                                        {!! Form::close() !!}

                                     </div>
                                 </div>
                             </div>

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

@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">

            <div class="col-md-12">
<form method="POST" action="/pinterest/save">
    {{ csrf_field() }}

              <div class="panel panel-default">
                  <div class="panel-body">
                <h4>Select one or more boards you'd like to post to</h4>
                <br />
                <div class="row">

                  <div class="col-md-12">

                        @if(count($boards) == 0)
                            <div class="alert alert-danger">
                                <span>Oops! No results found.</span>
                            </div>
                        @endif

                    <div class="row">

                    <? foreach($boards as $board) : ?>
                      <div class="col-sm-4">
					   <div class="checkbox" style="border: 1px solid #ddd; display: block; width: 100%; padding: 3px 6px; margin-bottom: 6px; text-decoration: none;">
						<label>
						  <input type="checkbox" name="boards[<?= $board->id ?>|<?= trim(parse_url($board->url, PHP_URL_PATH), '/') ?>]" value="<?= $board->name ?>"> <?= $board->name ?>
						</label>
					  </div>

                      </div>
                    <? endforeach; ?>
                  </div>


  <button type="submit" class="btn btn-primary mt-s">Continue <i class="fa fa-chevron-right" aria-hidden="true"></i></button>



        </div>
        </div>
        </div>
		
		
		
		
		</form>

        </div>
        </div>
    </div>
</div>

@endsection

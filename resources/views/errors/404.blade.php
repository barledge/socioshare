@extends('errors.error')

@section('content')
<p>
		Server Error: 404 (Not Found)
</p>

	<p class="additional">
		Additional information: {{ $exception->getMessage() }}
	</p>
@endsection

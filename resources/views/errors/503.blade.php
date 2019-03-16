@extends('errors.error')

@section('content')
<p>
	Server Error: 503 (Service Unavailable)
</p>

	<p class="additional">
		Additional information: {{ $exception->getMessage() }}
	</p>
@endsection

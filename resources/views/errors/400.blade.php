@extends('errors.error')

@section('content')
<p>
		Server Error: 400 (Bad request)
</p>

	<p class="additional">
		Additional information: {{ $exception->getMessage() }}
	</p>


@endsection

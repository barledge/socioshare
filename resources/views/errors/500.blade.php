@extends('errors.error')

@section('content')
<p>
		Server Error: 500 (Internal Server Error)
</p>

	<p class="additional">
		Additional information: {{ $exception->getMessage() }}
	</p>
@endsection

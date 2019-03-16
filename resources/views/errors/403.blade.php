@extends('errors.error')

@section('content')
<p>
		Server Error: 403 (Forbidden)
</p>

	<p class="additional">
		Additional information: {{ $exception->getMessage() }}
	</p>
@endsection

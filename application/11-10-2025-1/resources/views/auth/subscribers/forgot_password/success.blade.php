@extends('nweb.layout.app_new')
@section('content')

<div class="registration-container">
	<h2 class="registration-title">Registration Successful</h2>
	<div class="row justify-content-center">
		<div class="card">
			<div class="card-body">
				<h4>Thank you for registering!</h4>
				<p>Your registration was successful. Please check your email for verification instructions.</p>
				<a href="{{ url('subscriber/login') }}" class="btn btn-primary">Go to Login</a>
			</div>
		</div>
	</div>


</div>
@endsection
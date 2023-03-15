@extends('auth.master')

@section('content')
<h1 class="login-header">Reset your password</h1>
<h6>Please enter your email to receive password reset email.</h6>
<div class="card card-body">
    <form class="user" method="POST" action="{{ route('reset-password') }}" id="passwordreset_form" >@csrf
    <x-alert/>
        <div class="form-group required">
            <label for="username">Email</label>
            <input type="text" class="form-control"  name="email" value="{{ old('email') }}" placeholder="Enter your email" autofocus>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group d-flex password-feild">
            
            <a class="ml-auto border-link small-xl" href="{{ route('login') }}">Login</a>
        </div>
        <div class="form-group pt-1">
            <button class="btn btn-primary btn-block sign_in" type="submit">{{ __('Send Password Reset Link') }}</button>
        </div>
    </form>	
</div>
@endsection

@section('js-scripts')
<script>
    $(document).ready(function() {
        $("form[id='passwordreset_form']").validate({
            // Specify validation rules
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
            // Specify validation error messages
            messages: {
                email: {
                    required: 'Email address is required',
                    email: 'Provide a valid Email address',
                }
            },
            errorElement : 'label',
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
    
</script>
@endsection
@extends('auth.master')

@section('content')
<h1 class="login-header">Login</h1>
<h6>Welcome back! Please enter your details.</h6>
<div class="card card-body">
    <form id="login_form" action="{{ route('login') }}" method="post" data-parsley-validate=""
        data-parsley-errors-messages-disabled="true" novalidate="" _lpchecked="1">
        <x-alert/>
        @csrf
        <div class="form-group required">
            <label for="username">Email</label>
            <input type="text" class="form-control" required="" name="email" value="" placeholder="Enter your email">
        </div>
        <div class="form-group required">
            <label class="d-flex flex-row align-items-center" for="password">Password </label>
            <input type="password" class="form-control" required="" id="password"
                name="password" value="" placeholder="Enter your password">
        </div>
        <div class="form-group d-flex password-feild">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="remember-me"
                    name="remember" data-parsley-multiple="remember-me" value="1">
                <label class="custom-control-label" for="remember-me">Remember me</label>
            </div>
            <a class="ml-auto border-link small-xl" href="{{ route('reset-password') }}">Forgot password</a>
        </div>
        <div class="form-group pt-1">
            <button class="btn btn-primary btn-block sign_in" type="submit">Sign in</button>
        </div>
    </form>
</div>
@endsection

@section('js-scripts')
<script>
    $(document).ready(function(){
        $("form#login_form").validate({
            rules: {
                email: {
                    required: true
                },
                password: {
                    required: true,
                }
            },
            // Specify validation error messages
            messages: {
                email: {
                    required: 'Email address is required',
                    email: 'Provide a valid Email address',
                },
                password: {
                    required: 'Password is required',	
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>
@endsection